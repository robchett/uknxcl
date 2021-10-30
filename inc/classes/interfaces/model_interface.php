<?php

namespace classes\interfaces;

use classes\table_array;
use classes\table_form;
use classes\tableOptions;
use form\field;
use form\schema;

interface model_interface
{
    public function get_url(): string;

    public function get_tablename(): string;

    public function get_primary_key_name(): string;

    public static function get_schema(): schema;

    /**
     * @return field[]
     */
    public static function get_fields(): array;

    public function class_name(): bool|string;

    public function get_primary_key(): int;

    public function get_parent_primary_key(): int;

    public function do_submit(): bool;

    public function get_cms_edit(): string;

    public function get_form_ajax(): void;

    public function get_cms_list(): string;

    public function get_title(): string;

    public function is_live(): bool;

    public function is_deleted(): bool;

    public function format_date(string|int $date, string $format = 'Y-m-d'): bool|string;

    public static function getFromId(int $id, ?tableOptions $options = null): self|false;

    public static function get(tableOptions $options): self|false;

    /**
     * @return table_array<self>
     */
    public static function get_all(tableOptions $options): table_array;

    public static function do_cms_update(): int;

    public static function get_form(): table_form;

    public static function do_save(array $data): int;

    public static function do_reorder(): void;

    public static function do_toggle_live(): void;
}
