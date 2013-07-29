<?php
class db implements database_interface {
    /** @var PDO */
    public static $con;
    public static $con_name;
    public static $con_arr = array();
    public static $timeout = 30;

    public static function connect($host, $db, $username, $password, $name = 'default') {
        self::$con_arr[$name] = array(
            'connection' => new PDO('mysql:host=' . $host . ';dbname=' . $db, $username, $password),
            'settings' => array(
                'host' => $host,
                'database' => $db,
                'username' => $username,
                'password' => $password,
            ),
            'created' => time()
        );
        self::$con_name = $name;
        self::$con = self::$con_arr[$name]['connection'];
        self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function reconnect() {
        $settings = self::$con_arr[self::$con_name]['settings'];
        self::connect($settings['host'], $settings['database'], $settings['username'], $settings['password'], self::$con_name);
    }

    public static function default_connection() {
        self::connect(get::ini('server', 'mysql'), get::ini('database', 'mysql'),  get::ini('username', 'mysql'), get::ini('password', 'mysql'));
    }

    public static function esc($str) {
        mysql_real_escape_string($str);
    }

    /* @return array
     * @param res PDOStatement
     * @param class string
     * @return mixed
     */
    public static function fetch_all($res, $class = 'stdClass') {
        if ($class != null) {
            return $res->fetchAll(PDO::FETCH_OBJ);
        } else {
            return $res->fetchAll();
        }
    }

    public static function get_query($object, array $fields_to_retrieve, $options, &$parameters = array()) {
        $fields = array();
        $where = 'WHERE 1 ';
        $order = '';
        $limit = '';
        $join = '';
        $group = '';
        if (!empty($fields_to_retrieve)) {
            foreach ($fields_to_retrieve as $field) {
                if (strstr($field, '.') && !strstr($field, '.*') && !strstr($field, ' AS ')) {
                    $fields[] = $field . ' AS ' . str_replace('.', '_', $field);
                } else if (strstr($field, '(') === false && strstr($field, '.*') === false && strstr($field, '.') === false) {
                    $fields[] = $object . '.' . $field;
                } else {
                    $fields[] = $field;
                }
            }
        } else {
            $fields[] = $object . '.*';
        }
        if (isset($options['join'])) {
            foreach ($options['join'] as $key => $val) {
                $join .= ' LEFT JOIN ' . $key . ' ON ' . $val;
            }
        }
        if (isset($options['where'])) {
            $where .= 'AND ' . $options['where'];
        }

        if (isset($options['where_equals']) && !empty($options['where_equals'])) {
            $where_cnt = 0;
            foreach ($options['where_equals'] as $key => $val) {
                $where_cnt++;
                if (strpos($key, '.') !== false) {
                    $where .= ' AND `' . str_replace('.', '`.', $key) . '=:where_' . $where_cnt;
                } else {
                    $where .= ' AND `' . $key . '`=:where_' . $where_cnt;
                }
                $parameters['where_' . $where_cnt] = $val;
            }
        }
        if (isset($options['order'])) {
            $order .= 'ORDER BY ' . $options['order'];
        }
        if (isset($options['limit'])) {
            $limit .= 'LIMIT ' . $options['limit'];
        }
        if (isset($options['group'])) {
            $group .= 'GROUP BY ' . $options['group'];
        }
        return $sql = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $object . ' ' . $join . ' ' . $where . ' ' . $group . ' ' . $order . ' ' . $limit . ' ';

    }

    public static function insert_id() {
        return self::$con->lastInsertId();
    }

    /**
     * @param PDOStatement $res
     * @return int
     */
    public static function num($res) {
        return $res->rowCount();
    }

    private static function prepare($sql) {
        return self::$con->prepare($sql);
    }

    public static function result($sql, $params = array(), $class = 'stdClass') {
        $res = self::query($sql, $params);
        if ($res) {
            return self::fetch($res, $class);
        }
        return false;
    }

    public static function has_timed_out() {
        return time() - self::$con_arr[self::$con_name]['created'] > self::$timeout;
    }

    static function query($sql, $params = array(), $throwable = false) {
        // Attempt to reconnect if connection has gone away.
        if (self::has_timed_out()) {
            self::reconnect();
        }
        $prep_sql = self::$con->prepare($sql);
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $prep_sql->bindValue($key, $val);
            }
        }
        try {
            $prep_sql->execute();
        } catch (PDOException $e) {
            $error = '<div class="error_message mysql"><p>' . $e->getMessage() . '</p>' . core::get_backtrace() . print_r((isset($prep_sql->queryString) ? $prep_sql->queryString : ''), 1) . print_r($params, true) . '</div>';
            if (ajax) {
                ajax::inject('body', 'append', $error);
                if (!$throwable) {
                    ajax::do_serve();
                    die();
                }
            } else {
                echo $error;
                if (!$throwable) {
                    die();
                }
            }
        }

        return $prep_sql;
    }

    /**
     * @param PDOStatement $res
     * @param string $class
     * @return mixed
     */
    public static function fetch($res, $class = 'stdClass') {
        if ($class != null) {
            return $res->fetchObject($class);
        } else {
            return $res->fetch();
        }
    }

    public static function swap_connection($name) {
        self::$con = self::$con_arr[$name];
    }
}
