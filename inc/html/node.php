<?php

namespace html;

use classes\attribute_list;
use Stringable;

class node implements Stringable {

    public node $parent;
    protected string $type = '';
    protected string $id = '';
    protected string $content = '';
    /** @var string[] */
    protected array $class = [];
    protected attribute_list $attributes;

    public function __construct(string $type, attribute_list $attributes, string $content = '') {
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
            $this->content = static::create($nodes[1], $attributes, $content);
            $this->attributes = new attribute_list();
        } else {
            $this->content = $content;
            $this->attributes = $attributes;
        }
    }

    public static function create(string $type, array|attribute_list $attr = null, string $content = ''): string {
        $attr ??= new attribute_list();
        /** @psalm-suppress MixedArgument */
        if (is_array($attr)) $attr = new attribute_list(...$attr);
        return (string) new self($type, $attr, $content);
    }

    public function __toString(): string {
        return $this->get();
    }

    public function get(): string {
        $attributes = $this->set_standard_attributes($this->attributes);
        if ($this->id) {
            $attributes->id = str_replace(' ', '-', $this->id);
        }
        if ($this->class) {
            $attributes->class = array_merge($attributes->class, $this->class);
        }
        $html = '';
        if ($this->type == '-') {
            $html .= $this->combine_nodes($this->content);
        } else if ($this->is_self_closing()) {
            $html .= '<' . $this->type . $attributes . '/>';
        } else {
            $html .= '<' . $this->type . $attributes . '>';
            $html .= $this->combine_nodes($this->content);
            $html .= '</' . $this->type . '>';
        }
        return $html;
    }

    public function set_standard_attributes(attribute_list $attributes): attribute_list {
        return $attributes;
    }

    /**
     * @param string[]|string $nodes
     */
    protected function combine_nodes(array|string $nodes): string {
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

    public static function get_attributes(array $attributes): string {
        $html = '';
        /** @psalm-suppress MixedAssignment */
        foreach ($attributes as $attr => $value) {
            if (is_null($value) || (is_array($value) && empty($value))) {
                continue;
            }
            /** @psalm-suppress MixedArgumentTypeCoercion, MixedArgument, MixedOperand */
            if (is_array($value)) {
                if ($attr == 'class') {
                    $html .= ' ' . $attr . '="' . htmlentities(implode(' ', $value), ENT_QUOTES) . '"';
                }
            } elseif (str_starts_with($attr, 'data')) {
                $html .= ' data-' . strtolower(substr($attr, 4)) . '="' . htmlentities($value, ENT_QUOTES) . '"';
            } else {
                $html .= ' ' . $attr . '="' . htmlentities($value, ENT_QUOTES) . '"';
            }
        }
        return $html;
    }

    public function add_attribute(string $name, mixed $val): self {
        $this->attributes->$name = $val;
        return $this;
    }
}
