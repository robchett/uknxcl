<?php

namespace track;

use classes\error_handler;
use Generator;

/**
 * @psalm-import-type JsonPart from igc_parser
 * @psalm-import-type JsonPartSet from igc_parser
 */
class igcPartResult {

    /** @var list<track_part> */
    public array $sets; 
    public string $date;
    public ?bool $validated = null;

    /** @param JsonPartSet $data */
    public function __construct(public int $id, array $data) {
        $this->date = $data['date'];
        $this->sets = array_map(fn(array $arr): track_part => new track_part($arr), $data['sets']);   
        $this->validated = isset($data['validated']) ? (bool) $data['validated'] : null;
    }
}
