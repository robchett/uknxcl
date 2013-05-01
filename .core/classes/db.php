<?php
class db {
    /** @var PDO */
    public static $con;
    public static $con_arr = array();

    public static function connect($name = 'new', $db = 'nxcl') {

        if (strstr($_SERVER['HTTP_HOST'], 'uknxcl.co.uk') !== false) {
            $db = 'eacommsc_' . $db;
            $host = "localhost";
            $username = "eacommsc_nxcl";
            $password = '2TTFBDJ4Q$zD';
        } else {
            $host = '127.0.0.1';
            $username = "root";
            $password = "";
        }
        self::$con_arr[$name] = new PDO('mysql:host=' . $host . ';dbname=' . $db, $username, $password, array(PDO::ATTR_PERSISTENT => true));
        self::$con = self::$con_arr[$name];
        self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    static function esc($str) {
        mysql_real_escape_string($str);
    }

    /* @return array */
    public static function fetch_all(PDOStatement $res, $class = 'stdClass') {
        if ($class != null) {
            return $res->fetchAll(PDO::FETCH_OBJ);
        } else {
            return $res->fetchAll();
        }
    }

    static function get_query($object, array $fields_to_retrieve, $options, &$parameters = array()) {
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

    static function num(PDOStatement $res) {
        return $res->rowCount();
    }

    public static function prepare($sql) {
        return self::$con->prepare($sql);
    }

    static function result($sql, $params = array(), $class = 'stdClass') {
        $res = self::query($sql, $params);
        if ($res) {
            return self::fetch($res, $class);
        }
        return false;
    }

    static function query($sql, $params = array(), $throwable = false) {
        $prep_sql = self::$con->prepare($sql);
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $prep_sql->bindValue($key, $val);
            }
        }
        try {
            $prep_sql->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 'HY000') {
                self::connect();
                self::query($sql, $params = array(), $throwable);
            } else {
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
        }

        return $prep_sql;
    }

    static function fetch(PDOStatement $res, $class = 'stdClass') {
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
