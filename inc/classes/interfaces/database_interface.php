<?php

namespace classes\interfaces;

use db\select;
use classes\tableOptions;

interface database_interface
{

    public static function connect(string $host, string $db, string $username, string $password, string $name = 'new'): bool;

    public static function esc(string $str): string;

    public static function fetchAll(\PDOStatement $res, string $class = 'stdClass'): array;

    /**
     * @param string $object
     * @param string[] $fields_to_retrieve
     */
    public static function get_query(
        string $object,
        array $fields_to_retrieve,
        tableOptions $options
    ): select;

    public static function insert_id(): string;

    public static function num(\PDOStatement $res): int;

    /** @param array<string, scalar> $params */
    public static function result(string $sql, array $params = []): ?array;

    /** @param array<string, scalar> $params */
    public static function query(string $sql, array $params = [], bool $throwable = false): mixed;

    public static function fetch(\PDOStatement $res): array|false;
}
