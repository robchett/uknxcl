<?php

class html_node {

    public $parent;
    protected $type = '';
    protected $id = '';
    protected $content = '';
    protected $class = array();
    protected $attributes = array();
    protected $children = array();
    protected $pointer;

    public function __construct($type, $content = '', $attr = array()) {
        $nodes = explode(' ', $type, 2);
        if (strstr($type, '#')) {
            list($this->type, $id) = explode('#', $type);
            if (strstr($id, '.')) {
                list($this->id, $classes) = explode('.', $id, 2);
                $this->class = explode('.', $classes);
            } else {
                $this->id = $id;
            }
        } else if (strstr($type, '.')) {
            list($this->type, $classes) = explode('.', $type, 2);
            $this->class = explode('.', $classes);
        } else {
            $this->type = $type;
        }
        if (isset($nodes[1])) {
            $node = html_node::create($nodes[1], $content);
            $this->add_child($node);
            $this->pointer = $node;
        } else {
            $this->content = $content;
        }
        $this->attributes = $attr;

    }

    /* @return html_node */
    public static function create($type, $content = '', $attr = array()) {
        $node = new html_node($type, $content, $attr);
        return $node;
    }

    /* @return string */
    public static function inline($type, $content = '', $attr = array()) {
        $node = new html_node($type, $content, $attr);
        return $node->get();
    }

    public function get() {
        $html = '<' .
            $this->type .
            (!empty($this->id) ? ' id="' . str_replace(' ', '-', $this->id) . '"' : '') .
            (!empty($this->class) ? ' class=" ' . implode(' ', $this->class) . '"' : '') .
            $this->get_attributes() .
            '>';
        $html .= $this->content;
        foreach ($this->children as $child) {
            $html .= $child->get();
        }
        $html .= '</' . $this->type . '>';
        return $html;
    }

    protected function get_attributes() {
        $html = '';
        foreach ($this->attributes as $attr => $value) {
            $html .= ' ' . $attr . '="' . htmlentities($value, ENT_QUOTES) . '"';
        }
        return $html;
    }

    /* @return html_node */
    public function nest($children) {
        if ($this->pointer) {
            $this->pointer->nest($children);
        } else {
            if(is_array($children)) {
                foreach ($children as $child) {
                    if (is_array($child)) {
                        $this->nest($child);
                    } else {
                        $this->add_child($child);
                    }
                }
            } else {
                $this->add_child($children);
            }
        }
        return $this;
    }

    /* @return html_node */
    public function add_child(html_node $child) {
        if ($this->pointer) {
            $this->pointer->add_child($child);
        } else {
            $this->children[] = $child;
            $child->parent = $this;
        }
        return $this;
    }

    /* @return html_node */
    public function add_class($classes) {
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->class[] = $class;
            }
        } else {
            $this->class[] = $classes;
        }
        return $this;
    }

    /* @return html_node */
    public function add_attribute($name, $val) {
        $this->attributes[$name] = $val;
        return $this;
    }
}

