<?php
use html\node;

/**
 * Class db
 */
class db implements interfaces\database_interface {
    /** @var PDO */
    public static $con;
    /**
     * @var
     */
    public static $con_name;
    /**
     * @var array
     */
    public static $con_arr = array();
    /**
     * @var int
     */
    public static $timeout = 30;

    public static $default_table_settings = array(
        'ENGINE' => 'innoDB',
        'CHARACTER SET' => 'utf8',
    );

    public static function select($table_name) {
        return new db\select($table_name);
    }

    /**
     * @param $host
     * @param $db
     * @param $username
     * @param $password
     * @param string $name
     * @return bool
     */
    public static function connect($host, $db, $username, $password, $name = 'default') {
        try {
            $var = new PDO('mysql:host=' . $host . ';dbname=' . $db, $username, $password);
        } catch (MemcachedException $e) {
            die('Could not connect to database, please try again shortly...');
        }
        self::$con_arr[$name] = array(
            'connection' => $var,
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

    /**
     *
     */
    public static function reconnect() {
        $settings = self::$con_arr[self::$con_name]['settings'];
        self::connect($settings['host'], $settings['database'], $settings['username'], $settings['password'], self::$con_name);
    }

    /**
     *
     */
    public static function default_connection() {
        self::connect(get::ini('server', 'mysql'), get::ini('database', 'mysql'), get::ini('username', 'mysql'), get::ini('password', 'mysql'));
    }

    /**
     * @param string $str
     * @return string
     */
    public static function esc($str) {
        return mysql_real_escape_string($str);
    }

    /**
     * @param PDOStatement $res
     * @param string $class
     * @return mixed
     */
    public static function fetch_all($res, $class = 'stdClass') {
        if ($class != null) {
            return $res->fetchAll(PDO::FETCH_OBJ);
        } else {
            return $res->fetchAll();
        }
    }

    /**
     * @param $object
     * @param array $fields_to_retrieve
     * @param $options
     * @param array $parameters
     * @return string
     */
    public static function get_query($object, array $fields_to_retrieve, $options, &$parameters = array()) {
        $fields = array();
        $where = 'WHERE 1 ';
        $order = '';
        $limit = '';
        $join = '';
        $group = '';
        $base_object = get::__class_name($object);
        if (!empty($fields_to_retrieve)) {
            foreach ($fields_to_retrieve as $field) {
                if (strstr($field, '.') && !strstr($field, '.*') && !strstr($field, ' AS ')) {
                    $fields[] = $field . ' AS ' . str_replace('.', '_', $field);
                } else if (strstr($field, '(') === false && strstr($field, '.*') === false && strstr($field, '.') === false) {
                    $fields[] = $base_object . '.' . $field;
                } else {
                    $fields[] = $field;
                }
            }
        } else {
            $fields[] = $base_object . '.*';
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
        return $sql = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $base_object . ' ' . $join . ' ' . $where . ' ' . $group . ' ' . $order . ' ' . $limit . ' ';

    }

    /**
     * @return string
     */
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

    /**
     * @param $sql
     * @return PDOStatement
     */
    private static function prepare($sql) {
        return self::$con->prepare($sql);
    }

    /**
     * @param $sql
     * @param array $params
     * @param string $class
     * @return bool|mixed
     */
    public static function result($sql, $params = array(), $class = 'stdClass') {
        $res = self::query($sql, $params);
        if ($res) {
            return self::fetch($res, $class);
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function has_timed_out() {
        return time() - self::$con_arr[self::$con_name]['created'] > self::$timeout;
    }

    /**
     * @param $sql
     * @param array $params
     * @param bool $throwable
     * @return PDOStatement
     */
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
            $error = node::create('div.error_message.mysql', [],
                node::create('p', [],
                    $e->getMessage() .
                    core::get_backtrace() .
                    print_r((isset($prep_sql->queryString) ? $prep_sql->queryString : ''), 1) . print_r($params, true)
                )
            );
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

    /**
     * @param $name
     * @return bool
     */
    public static function swap_connection($name) {
        if (isset(self::$con_arr[$name])) {
            self::$con = self::$con_arr[$name];
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $table
     * @return bool
     */
    public static function table_exists($table) {
        $res = self::query('show tables like "test1"');
        return \db::num($res);
    }

    /**
     * @param $table string
     * @param $column string
     * @return bool
     */
    public static function column_exists($table, $column) {
        $res = self::query("SHOW COLUMNS FROM `table` LIKE 'fieldname'");
        return \db::num($res);
    }

    public static function create_table($table_name, $fields = array(), $settings = array()) {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_name;
        $column_strings = array();
        foreach ($fields as $field => $structure) {
            $column_strings[] = '`' . $field . '` ' . $structure;
        }
        $sql .= ' (' . implode(',', $column_strings) . ') ';
        $setting_strings = array();
        $settings = array_merge(self::$default_table_settings, $settings);
        foreach ($settings as $setting => $value) {
            $setting_strings[] = $setting . ' = ' . $value;
        }
        $sql .= implode(',', $setting_strings);
        self::query($sql);
    }
}
