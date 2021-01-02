<?php

namespace classes\interfaces;

use db\select;

/**
 * Class database_interface
 */
interface database_interface {

    /**
     * @param string $host
     * @param string $db
     * @param string $username
     * @param string $password
     * @param string $name
     * @return bool
     */
    public static function connect(string $host, string $db, string $username, string $password, string $name = 'new'): bool;

    /**
     * @param $str
     * @return string
     */
    public static function esc($str): string;

    /**
     * @param $res
     * @param string $class
     * @return array
     */
    public static function fetchAll($res, string $class = 'stdClass'): array;

    /**
     * @param $object
     * @param array $fields_to_retrieve
     * @param $options
     * @return select
     */
    public static function get_query($object, array $fields_to_retrieve, $options): select;

    /**
     * @return ?int
     */
    public static function insert_id(): ?int;

    /**
     * @param $res
     * @return int
     */
    public static function num($res): int;

    /**
     * @param $sql
     * @param array $params
     * @return ?array
     */
    public static function result($sql, array $params = []): ?array;

    /**
     * @param $sql
     * @param array $params
     * @param bool $throwable
     * @return mixed
     */
    public static function query($sql, array $params = [], bool $throwable = false): mixed;

    /**
     * @param $res
     * @return array|bool
     */
    public static function fetch($res): array|bool;

    /**
     * @param $table
     * @return bool
     */
    public static function table_exists($table): bool;

    /**
     * @param $table
     * @param $column
     * @return bool
     */
    public static function column_exists(string $table, string $column): bool;
}
