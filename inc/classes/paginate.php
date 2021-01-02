<?php

namespace classes;

use html\node;

class paginate {

    public $total;
    public $base_url;
    public $npp;
    public $page;
    public attribute_callable $act;
    public string $title;
    public $post_title;
    public array $post_data = [];

    public function __toString(): string {
        return (string)$this->get();
    }

    public function get(): string {
        $output = "";
        if ($this->npp && $this->total > $this->npp) {
            $pages = ceil($this->total / $this->npp);
            if ($this->title) {
                $output .= "<span class='title'>{$this->do_replace($this->title)}</span>";
            }
            if ($pages > 40) {
                $options['data-ajax-change'] = $this->act;
                $options['data-ajax-post'] = $this->post_data;
                $options['class'][] = 'form-control';
                $attrs = node::get_attributes($options);
                $options = array_reduce(range(1, $pages), fn($a, $i) => $a . "<option value='$i' " . ($this->page == $i ? "selected" : ""). ">$i</option>", "");
                $output .= "<select {$attrs}>$options</select>";
            } else {
                $lis = array_reduce(range(0, $pages), function ($a, $i) {
                    $options['data-ajax-click'] = $this->act;
                    $options['data-ajax-post'] = $this->post_data + ['value' => $i];
                    $li_options = ($this->page == $i) ? ['class' => ['active']] : [];
                    $li_attrs = node::get_attributes($li_options);
                    $attrs = node::get_attributes($options);
                    return $a . "<li {$li_attrs}><a {$attrs}>{$i}</a></li>";
                }, "");
                $output .= "<ul id='pagi' class='pagination'>${lis}</ul>";
            }
            if ($this->post_title) {
                $output .= "<span class='title'>{$this->do_replace($this->post_title)}</span>";
            }
        }
        return "<div class='paginate'>$output</div>";
    }

    public function do_replace($source) {
        foreach ($this as $key => $value) {
            if (!is_array($value)) {
                $source = str_replace('{' . $key . '}', $value, $source);
            }
        }
        return $source;
    }

}
 