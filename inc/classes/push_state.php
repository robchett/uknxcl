<?php

namespace classes;

use classes\push_state_data as _data;
use core;

class push_state
{
    public string $url = '';
    public string $title = '';
    public string $id = '';

    public function getAjax(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'id' => $this->id
        ];
    }

    public function get(): void
    {
        $data = json_encode([
            'url' => $this->url,
            'title' => $this->title,
        ]);
        $script = '$.fn.ajax_factory.states["' . $this->url . '"] = ' . $data . ';';
        $script .= 'window.history.pushState(' . $data . ', "","' . $this->url . '");';
        core::$inline_script[] = $script;
    }
}
