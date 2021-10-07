<?php

namespace classes;

use classes\db as _db;
use db\count as _count;
use db\delete as _delete;
use db\insert as _insert;
use db\replace as _replace;
use db\select as _select;
use db\update as _update;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

class db implements interfaces\database_interface
{

    public static PDO $con;
    public static ?string $con_name;
    /** @var array<string, array{connection:PDO, created: int, settings: array{host: string, database: string, password: string, username: string}}> */
    public static array $con_arr = [];
    public static int $timeout = 30;

    /** @var string[] */
    public static array $default_table_settings = [
        'ENGINE'        => 'innoDB',
        'CHARACTER SET' => 'utf8',
    ];

    public static function insert(string $table_name, string $mode = ''): _insert
    {
        return new _insert($table_name, $mode);
    }

    public static function update(string $table_name): _update
    {
        return new _update($table_name);
    }

    public static function delete(string $table_name): _delete
    {
        return new _delete($table_name);
    }

    public static function replace(string $table_name): _replace
    {
        return new _replace($table_name);
    }

    public static function count(string $table_name, string $primary_key = '*'): _count
    {
        $count = new _count($table_name);
        $count->add_field_to_retrieve($primary_key);
        return $count;
    }

    public static function fetchAll(PDOStatement $res, string $class = 'stdClass'): array
    {
        if ($class != null) {
            return $res->fetchAll(PDO::FETCH_OBJ);
        } else {
            return $res->fetchAll();
        }
    }

    public static function get_query(
        string $object,
        array $fields_to_retrieve,
        tableOptions $options,
    ): _select {
        $query = db::select($object);
        if (!empty($fields_to_retrieve)) {
            foreach ($fields_to_retrieve as $field) {
                if (strstr($field, '(') === false && strstr($field, '.') === false && strstr($field, ' ') === false) {
                    $query->add_field_to_retrieve($object . '.' . $field);
                } else {
                    $query->add_field_to_retrieve($field);
                }
            }
        } else {
            $query->add_field_to_retrieve($object . '.*');
        }
        if ($options->parameters) {
            $query->filter('1', $options->parameters);
        }
        foreach ($options->join as $key => $val) {
            $query->add_join($key, $val);
        }
        if ($options->where) {
            $query->filter($options->where);
        }
        foreach ($options->where_equals as $key => $val) {
            $query->filter_field($key, $val);
        }
        if ($options->order) {
            $query->set_order($options->order);
        }
        if ($options->limit) {
            $query->set_limit($options->limit);
        }
        if ($options->group) {
            $query->add_grouping($options->group);
        }
        return $query;
    }

    public static function select(string $table_name): _select
    {
        return new _select($table_name);
    }

    public static function insert_id(): string
    {
        return _db::$con->lastInsertId();
    }

    public static function query(string $sql, array $params = [], bool $throwable = false): PDOStatement
    {
        try {
            // Attempt to reconnect if connection has gone away.
            if (!_db::connected()) {
                _db::reconnect();
            }
            $prep_sql = _db::$con->prepare($sql);
            if (!$prep_sql) {
                throw new Exception('Query failed');
            }
            if (!empty($params)) {
                foreach ($params as $key => $val) {
                    $prep_sql->bindValue($key, $val);
                }
            }

            $prep_sql->execute();
            return $prep_sql;
        } catch (Exception $e) {
            error_handler::error("Query failure: $sql");
            throw $e;
        }
    }

    public static function connected(): bool
    {
        return isset(_db::$con_name);
    }

    public static function reconnect(): void
    {
        if (isset(_db::$con_name) && isset(_db::$con_arr[_db::$con_name])) {
            $settings = _db::$con_arr[_db::$con_name]['settings'];
            _db::connect($settings['host'], $settings['database'], $settings['username'], $settings['password'], _db::$con_name);
        } else {
            _db::default_connection();
        }
    }

    /**
     * @param string $host
     * @param string $db
     * @param string $username
     * @param string $password
     * @param string string $name
     * @return bool
     */
    public static function connect(string $host, string $db, string $username, string $password, string $name = 'default'): bool
    {
        try {
            $var = new PDO('mysql:host=' . $host . ';dbname=' . $db, $username, $password);
        } catch (PDOException $e) {
            die('Could not connect to database, please try again shortly...' . $e->getMessage());
        }
        _db::$con_arr[$name] = [
            'connection' => $var,
            'settings'   => [
                'host'     => $host,
                'database' => $db,
                'username' => $username,
                'password' => $password,
            ],
            'created'    => time(),
        ];
        _db::$con_name = $name;
        _db::$con = _db::$con_arr[$name]['connection'];
        _db::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    }

    public static function default_connection(): void
    {
        _db::connect((string) getenv('mysqlServer'), (string) getenv('mysqlDatabase'), (string) getenv('mysqlUsername'), (string) getenv('mysqlPassword'));
    }

    public static function esc(string $str): string
    {
        if (!_db::connected()) {
            _db::reconnect();
        }
        return _db::$con->quote($str);
    }

    public static function num(PDOStatement $res): int
    {
        return $res->rowCount();
    }

    /**
     * @param string $sql
     * @param array<string, scalar> $params
     * @return ?array<string, string>
     */
    public static function result(string $sql, array $params = []): ?array
    {
        $res = _db::query($sql, $params);
        return _db::fetch($res) ?: null;
    }

    /**
     * @param PDOStatement $res
     * @return array<string, string>|false
     * @psalm-suppress InvalidReturnType
     */
    public static function fetch(PDOStatement $res): array|false
    {
        /** @psalm-suppress InvalidReturnStatement */
        return $res->fetch(PDO::FETCH_ASSOC);
    }
}
