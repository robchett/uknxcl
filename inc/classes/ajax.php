<?php

namespace classes;

use core;
use DOMDocument;
use DOMXPath;
use stdClass;

class ajax {

    /** @var ?ajax */
    protected static ?ajax $singleton;

    public array $inject = [];
    /** @var string[] */
    public array $inject_script = [];
    /** @var string[] */
    public array $inject_script_before = [];
    public array $update = [];
    public ?push_state $push_state = null;
    public ?string $redirect = null;
    
    public static function singleton(): self {
        static::$singleton ??= new self();
        return static::$singleton;
    }

    public static function update(string $html): void {
        if (!$html) {
            return;
        }
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('/html/body/*') as $node) {
            $o = new stdClass();
            $o->id = $node->nodeName;
            $o->html = '';
            /** @psalm-suppress NullReference */
            $id = (string) ($node->attributes->getNamedItem('id')->nodeValue ?? '');
            /** @psalm-suppress NullReference */
            $class = (string) ($node->attributes->getNamedItem('class')->nodeValue ?? '');
            if ($id) {
                $o->id .= '#' . $id;
            }
            if ($class) {
                $o->id .= '.' . trim(str_replace(' ', '.', $class));
            }
            foreach ($node->childNodes as $subnode) {
                $o->html .= $dom->saveXML($subnode);
            }
            static::singleton()->update[] = $o;
        }
    }

    public static function do_serve(): void {
        if (!empty(core::$inline_script)) {
            foreach (core::$inline_script as $script) {
                self::add_script($script);
            }
        }
        if ($redirect = static::singleton()->redirect) {
            self::inject('body', 'append', '<script id="ajax">window.location.href = "' . $redirect . '";</script>');
        }
        $o = new stdClass();
        $o->pre_inject = [];
        if (static::singleton()->inject_script_before) {
            $s = new stdClass();
            $s->id = 'body';
            $s->pos = 'append';
            $s->html = '<script id="ajax_script_pre">' . implode(';', static::singleton()->inject_script_before) . '</script>';
            $s->over = '#ajax_script_pre';
            $o->pre_inject[] = $s;
        }
        $o->update = static::singleton()->update;
        $o->inject = static::singleton()->inject;
        if (static::singleton()->inject_script) {
            $s = new stdClass();
            $s->id = 'body';
            $s->pos = 'append';
            $s->html = '<script id="ajax_script">' . implode(';', static::singleton()->inject_script) . '</script>';
            $s->over = '#ajax_script';
            $o->inject[] = $s;
        }
        if (isset(static::singleton()->push_state)) {
            $o->push_state = static::singleton()->push_state->getAjax();
        }
        if (isset($_REQUEST['no_ajax'])) {
            echo '
    <script>
        Array.prototype.each = function (callback, context) {
            for (var i = 0; i < this.length; i++) {
                callback(this[i], i, context);
            }
        }
        Array.prototype.count = function () {
            return this.length - 2;
        }
            window.top.window.handle_json_response(' . json_encode($o) . ')
    </script>';
        } elseif ($json = json_encode($o)) {
            echo $json;
        } else {
            trigger_error('Could not encode data.');
        }
    }

    public static function add_script(string $script, bool $before = false): void {
        if ($before) {
            static::singleton()->inject_script_before[] = $script;
        } else {
            static::singleton()->inject_script[] = $script;
        }
    }

    /** @param 'before'|'after'|'append'|'prepend' $pos */
    public static function inject(string $id, string $pos, string $html, string $overwrite = ''): void {
        $o = new stdClass();
        $o->id = $id;
        $o->pos = $pos;
        $o->html = $html;
        $o->over = $overwrite;
        static::singleton()->inject[] = $o;
    }

    public static function push_state(push_state $push_state): void {
        static::singleton()->push_state = $push_state;
    }

    public static function current(): ajax {
        return static::singleton();
    }
}
