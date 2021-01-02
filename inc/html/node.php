<?php

namespace html;

use Stringable;

class node implements Stringable {

    public $parent;
    protected $type = '';
    protected $id = '';
    protected string $content = '';
    protected array $class = [];
    protected array $attributes = [];
    protected array $children = [];
    protected ?node $pointer = null;

    /**
     * @param $type
     * @param string $content
     * @param array $attr
     */
    public function __construct($type, $attr = [], $content = '') {
        $nodes = explode(' ', $type, 2);
        if (strstr($nodes[0], '#')) {
            [$this->type, $id] = explode('#', $nodes[0], 2);
            if (strstr($id, '.')) {
                [$this->id, $classes] = explode('.', $id, 2);
                $this->class = explode('.', $classes);
            } else {
                $this->id = $id;
            }
        } else if (strstr($nodes[0], '.')) {
            [$this->type, $classes] = explode('.', $nodes[0], 2);
            $this->class = explode('.', $classes);
        } else {
            $this->type = $nodes[0];
        }
        if (isset($nodes[1])) {
            $node = static::create($nodes[1], $attr, $content);
            $this->add_child($node);
            $this->pointer = $node;
            $attr = [];
        } else if (is_array($content)) {
            $this->children = $content;
        } else {
            $this->content = $content;
        }
        $this->attributes = $attr;
    }

    /**
     * @param $type
     * @param string $content
     * @param array $attr
     * @return static
     */
    public static function create($type, $attr = [], $content = ''): static {
        return new static($type, $attr, $content);
    }

    /**
     * @param node $child
     * @return string
     */
    public function add_child(node $child): string {
        if ($this->pointer) {
            $this->pointer->add_child($child);
        } else {
            $this->children[] = $child;
            $child->parent = $this;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->get();
    }

    /**
     * @return string|null
     */
    public function get(): ?string {
        $attributes = $this->attributes;
        $this->set_standard_attributes($attributes);
        if ($this->id) {
            $attributes['id'] = str_replace(' ', '-', $this->id);
        }
        if ($this->class) {
            if (!isset($attributes['class']))
                $attributes['class'] = [];
            $attributes['class'] = array_merge($attributes['class'], $this->class);
        }
        $html = '';
        if ($this->type == '-') {
            $html .= $this->combine_nodes($this->content);
            /** @var node $child */
            foreach ($this->children as $child) {
                $html .= $child;
            }
        } else if ($this->is_self_closing()) {
            $html .= '<' . $this->type . static::get_attributes($attributes) . '/>';
        } else {
            $html .= '<' . $this->type . static::get_attributes($attributes) . '>';
            $html .= $this->combine_nodes($this->content);
            /** @var node $child */
            foreach ($this->children as $child) {
                $html .= $child;
            }
            $html .= '</' . $this->type . '>';
        }
        return $html;
    }

    /**
     * @param $attributes
     */
    public function set_standard_attributes(&$attributes) {
    }

    protected function combine_nodes($nodes): string {
        if (is_array($nodes)) {
            $html = '';
            foreach ($nodes as $node) {
                $html .= $this->combine_nodes($node);
            }
        } else {
            $html = $nodes;
        }
        return $html;
    }

    public function is_self_closing(): bool {
        return ($this->type == 'input');
    }

    /**
     * @param $attributes
     *
     * @return string
     */
    public static function get_attributes($attributes): string {
        $html = '';
        foreach ($attributes as $attr => $value) {
            if (is_array($value)) {
                if ($attr == 'class') {
                    $html .= ' ' . $attr . '="' . htmlentities(implode(' ', $value), ENT_QUOTES) . '"';
                } else if ($attr == 'style') {
                    $styles = [];
                    foreach ($value as $_attr => $_value) {
                        $styles[] = $_attr . ':' . $_value;
                    }
                    $html .= ' ' . $attr . '="' . htmlentities(implode(';', $styles), ENT_QUOTES) . '"';
                } else {
                    $html .= ' ' . $attr . '=\'' . json_encode($value) . '\'';
                }
            } else {
                $html .= ' ' . $attr . '="' . htmlentities($value, ENT_QUOTES) . '"';
            }
        }
        return $html;
    }

    public function nest(...$children): string {
        if ($this->pointer) {
            $this->pointer->nest($children);
        } else {
            if (is_array($children)) {
                foreach ($children as $child) {
                    if (is_array($child)) {
                        $this->nest($child);
                    } else if ($child) {
                        $this->add_child($child);
                    }
                }
            }
        }
        return $this;
    }

    public function add_class(string $classes): static {
        $this->class[] = $classes;
        return $this;
    }

    public function add_attribute($name, $val): string {
        $this->attributes[$name] = $val;
        return $this;
    }
}
