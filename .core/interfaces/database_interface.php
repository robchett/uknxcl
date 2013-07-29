<?php

interface database_interface {

    public static function connect($host, $db, $username, $password, $name = 'new');

    public static function esc($str);

    public static function fetch_all($res, $class = 'stdClass');

    public static function get_query($object, array $fields_to_retrieve, $options, &$parameters = array());

    public static function insert_id();

    public static function num($res);

    public static function result($sql, $params = array(), $class = 'stdClass');

    public static function query($sql, $params = array(), $throwable = false);

    public static function fetch($res, $class = 'stdClass');

    public static function swap_connection($name);
}
