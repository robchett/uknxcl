<?php

namespace classes;

use classes\ajax as _ajax;
use classes\db as _db;
use classes\get as _get;
use core;
use db\count as _count;
use db\delete as _delete;
use db\insert as _insert;
use db\replace as _replace;
use db\select as _select;
use db\stub\field;
use db\update as _update;
use Exception;
use html\node;
use JetBrains\PhpStorm\Pure;
use module\cms\model\_cms_module;
use PDO;
use PDOException;
use PDOStatement;

class db implements interfaces\database_interface {

    /** @var PDO */
    public static PDO $con;
    /**
     * @var
     */
    public static string $con_name;
    /**
     * @var array
     */
    public static array $con_arr = [];
    /**
     * @var int
     */
    public static int $timeout = 30;

    public static array $default_table_settings = [
        'ENGINE'        => 'innoDB',
        'CHARACTER SET' => 'utf8',
    ];

    #[Pure]
    public static function insert($table_name, $mode = ''): _insert {
        return new _insert($table_name, $mode);
    }

    #[Pure]
    public static function update($table_name): _update {
        return new _update($table_name);
    }

    #[Pure]
    public static function delete($table_name): _delete {
        return new _delete($table_name);
    }

    #[Pure]
    public static function replace($table_name): _replace {
        return new _replace($table_name);
    }

    public static function count($table_name, $primary_key = '*'): _count {
        $count = new _count($table_name);
        $count->add_field_to_retrieve($primary_key);
        return $count;
    }

    public static function connect_root() {
        try {
            $var = new PDO('mysql:host=localhost', 'root', '');
        } catch (PDOException) {
            die('Could not connect to database, please try again shortly...');
        }
        _db::$con_arr['root'] = [
            'connection' => $var,
            'created'    => time(),
        ];
        _db::$con_name = 'root';
        _db::$con = _db::$con_arr['root']['connection'];
        _db::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param $res
     * @param string $class
     * @return array
     */
    public static function fetchAll($res, string $class = 'stdClass'): array {
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
     * @return _select
     */
    public static function get_query($object, array $fields_to_retrieve, $options): _select {
        $query = db::select(_get::__class_name($object));
        $base_object = _get::__class_name($object);
        if (!empty($fields_to_retrieve)) {
            foreach ($fields_to_retrieve as $field) {
                if (strstr($field, '.') && !strstr($field, '.*') && !strstr($field, ' AS ')) {
                    $query->add_field_to_retrieve($field . ' AS `' . str_replace('.', '@', $field) . '`');
                } else if (strstr($field, '(') === false && strstr($field, '.*') === false && strstr($field, '.') === false) {
                    $query->add_field_to_retrieve($base_object . '.' . $field);
                } else {
                    $query->add_field_to_retrieve($field);
                }
            }
        } else {
            $query->add_field_to_retrieve($base_object . '.*');
        }
        if (isset($options['parameters'])) {
            $query->filter('1', $options['parameters']);
        }
        if (isset($options['join'])) {
            foreach ($options['join'] as $key => $val) {
                $query->add_join($key, $val);
            }
        }
        if (isset($options['where'])) {
            $query->filter($options['where']);
        }
        if (isset($options['where_equals']) && $options['where_equals']) {
            foreach ($options['where_equals'] as $key => $val) {
                $query->filter_field($key, $val);
            }
        }
        if (isset($options['order'])) {
            $query->set_order($options['order']);
        }
        if (isset($options['limit'])) {
            $query->set_limit($options['limit']);
        }
        if (isset($options['group'])) {
            $query->add_grouping($options['group']);
        }
        return $query;
    }

    #[Pure]
    public static function select($table_name): _select {
        return new _select($table_name);
    }

    /**
     * @return ?int
     */
    public static function insert_id(): ?int {
        return _db::$con->lastInsertId();
    }

    /**
     * @param $table
     * @return bool
     */
    public static function table_exists($table): bool {
        $res = _db::query('show tables like ' . _db::esc($table));
        return _db::num($res) > 0;
    }

    /**
     * @param $sql
     * @param array $params
     * @param bool $throwable
     * @return false|PDOStatement
     */
    public static function query($sql, array $params = [], bool $throwable = false): bool|PDOStatement {
        // Attempt to reconnect if connection has gone away.
        if (!_db::connected()) {
            _db::reconnect();
        }
        $prep_sql = _db::$con->prepare($sql);
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $prep_sql->bindValue($key, $val);
            }
        }
        $prep_sql->execute();

        return $prep_sql;
    }

    public static function connected(): bool {
        if (!isset(_db::$con_name) || _db::has_timed_out()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public static function has_timed_out(): bool {
        return time() - _db::$con_arr[_db::$con_name]['created'] > _db::$timeout;
    }

    /**
     *
     */
    public static function reconnect() {
        if (isset(_db::$con_name) && isset(_db::$con_arr[_db::$con_name]) && isset(_db::$con_arr[_db::$con_name]['settings'])) {
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
    public static function connect(string $host, string $db, string $username, string $password, string $name = 'default'): bool {
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

    /**
     *
     */
    public static function default_connection() {
        _db::connect(ini::get('mysql', 'server'), ini::get('mysql', 'database'), ini::get('mysql', 'username'), ini::get('mysql', 'password'));
    }

    /**
     * @param $str
     * @return string
     */
    public static function esc($str): string {
        if (!_db::connected()) {
            _db::reconnect();
        }
        return _db::$con->quote($str);
    }

    /**
     * @param $res
     * @return int
     */
    public static function num($res): int {
        return $res->rowCount();
    }

    /**
     * @param $table string
     * @param $column string
     * @return bool
     */
    public static function column_exists(string $table, string $column): bool {
        $res = _db::query('SHOW COLUMNS FROM `' . $table . '` LIKE ' . _db::esc($column));
        return _db::num($res) > 0;
    }

    public static function add_column($table, $name, $type, $additional_options) {
        _db::query('ALTER TABLE ' . $table . ' ADD `' . $name . '` ' . $type . ' ' . $additional_options, [], 1);
    }

    public static function move_column($table, $name, $type, $additional_options) {
        _db::query('ALTER TABLE ' . $table . ' MODIFY `' . $name . '` ' . $type . ' ' . $additional_options, [], 1);
    }

    public static function create_table($table_name, $fields = [], $keys = [], $settings = []) {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_name;
        $column_strings = [];
        foreach ($fields as $field => $structure) {
            $column_strings[] = '`' . $field . '` ' . $structure;
        }
        foreach ($keys as $key) {
            $column_strings[] = $key;
        }
        $sql .= ' (' . implode(',', $column_strings) . ') ';
        $setting_strings = [];
        $settings = array_merge(_db::$default_table_settings, $settings);
        foreach ($settings as $setting => $value) {
            if (is_numeric($setting)) {
                $setting_strings[] = $value;
            } else {
                $setting_strings[] = $setting . ' = ' . $value;
            }
        }
        $sql .= implode(',', $setting_strings);
        _db::query($sql);
    }

    public static function create_table_join($source, $destination) {
        $source_module = new _cms_module();
        $source_module->do_retrieve(['primary_key'], ['where_equals' => ['table_name' => $source]]);
        $destination_module = new _cms_module();
        $destination_module->do_retrieve(['primary_key'], ['where_equals' => ['table_name' => $destination]]);

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $source . '_link_' . $destination . '
            (
                `' . $source_module->primary_key . '` INT(6) NOT NULL DEFAULT 0,
                `link_' . $destination_module->primary_key . '` INT(6) NOT NULL DEFAULT 0,
                `fid` INT(6) NOT NULL DEFAULT 0,
                INDEX(`' . $source_module->primary_key . '`,`link_' . $destination_module->primary_key . '`,`fid`),
                INDEX(`link_' . $destination_module->primary_key . '`)
            )
        ';
        $setting_strings = [];
        foreach (_db::$default_table_settings as $setting => $value) {
            if (is_numeric($setting)) {
                $setting_strings[] = $value;
            } else {
                $setting_strings[] = $setting . ' = ' . $value;
            }
        }
        $sql .= implode(',', $setting_strings);
        _db::query($sql);
    }

    public static function create_table_json($json) {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $json->tablename;
        $column_strings = [];
        foreach ($json->fieldset as $field => $structure) {
            $string = static::get_column_type_json($structure);
            if ($string) {
                $column_strings[] = '`' . $field . '` ' . $string;
            }
        }
        foreach ($json->indexes as $type => $indexes) {
            switch ($type) {
                case 'primary' :
                    $column_strings[] = 'PRIMARY KEY (`' . $indexes . '`)';
                    break;
                case 'standard':
                    foreach ($indexes as $index) {
                        $column_strings[] = 'INDEX (`' . implode('`,`', $index) . '`)';
                    }
                    break;
            }
        }
        $sql .= ' (' . implode(',', $column_strings) . ') ';

        $setting_strings = [];
        foreach (_db::$default_table_settings as $setting => $value) {
            if (is_numeric($setting)) {
                $setting_strings[] = $value;
            } else {
                $setting_strings[] = $setting . ' = ' . $value;
            }
        }
        foreach ($json->settings as $setting => $value) {
            if (is_numeric($setting)) {
                $setting_strings[] = $value;
            } else {
                $setting_strings[] = $setting . ' = ' . $value;
            }
        }
        $sql .= implode(',', $setting_strings);
        _db::query($sql);
    }

    public static function get_column_type_json(field $structure): bool|string {
        $string = '';
        switch ($structure->type) {
            case 'int':
                $string .= 'INT(' . ($structure->length ?: 6) . ')';
                $default = 0;
                break;
            case 'boolean':
                $default = 0;
                $string .= 'INT(1)';
                break;
            case 'password':
            case 'string':
                $default = '';
                $string .= 'VARCHAR(' . ($structure->length ?: 64) . ')';
                break;
            case 'textarea':
                $default = '';
                $string .= 'TEXT';
                break;
            case 'date':
                $default = '0000-00-00';
                $string .= 'TIMESTAMP';
                break;
            case 'link':
                $default = 0;
                $string .= 'INT(' . ($structure->length ?: 6) . ')';
                break;
            default :
                return false;
        }
        $string .= ' NOT NULL ' . ($structure->autoincrement ? 'AUTO_INCREMENT' : 'DEFAULT "' . ($structure->default ?: $default) . '"');
        return $string;
    }

    public static function rename_table($old, $new) {
        echo 'RENAME TABLE ' . $old . ' TO ' . $new;
        static::query('RENAME TABLE ' . $old . ' TO ' . $new);
    }

    public static function rename_column($table, $old, $new): bool|PDOStatement {
        try {
            $field = static::get_column_definition($table, $old);
            return static::query('ALTER TABLE ' . $table . ' CHANGE ' . $old . ' ' . $new . ' ' . $field);
        } catch (Exception) {
            return false;
        }
    }

    protected static function get_column_definition($table, $column) {
        $table = static::result('SHOW CREATE TABLE ' . $table, []);
        $matches = [];
        if (preg_match('#`' . $column . '` (.*?),#', $table['Create Table'], $matches)) {
            return $matches[1];
        } else {
            throw new Exception('Could not find column ' . $column . ' in ' . $table);
        }
    }

    /**
     * @param $sql
     * @param array $params
     * @return ?array
     */
    public static function result($sql, array $params = []): ?array {
        $res = _db::query($sql, $params);
        if ($res) {
            return _db::fetch($res);
        }
        return null;
    }

    /**
     * @param $res
     * @return array|bool
     */
    public static function fetch($res): array|bool {
        return $res->fetch(PDO::FETCH_ASSOC);
    }


}