<?php

namespace classes;

use html\node;

class paginate {

    public int $total;
    public string $base_url;
    public int $npp;
    public int $page;
    public attribute_callable $act;
    public string $title = '';
    public string $post_title = '';
    public array $post_data = [];

    public function __toString(): string {
        return $this->get();
    }

    public function get(): string {
        $output = "";
        $options = [];
        if ($this->npp && $this->total > $this->npp) {
            $pages = (int) ceil($this->total / $this->npp);
            if ($this->title) {
                $output .= "<span class='title'>{$this->do_replace($this->title)}</span>";
            }
            if ($pages > 10) {
                $options['dataAjaxChange'] = $this->act;
                $options['dataAjaxPost'] = json_encode($this->post_data);
                $options['class'][] = 'form-control';
                $attrs = node::get_attributes($options);
                $options = array_reduce(range(1, $pages), fn(string $a, int $i) => $a . "<option value='$i' " . ($this->page == $i ? "selected" : ""). ">$i</option>", "");
                $output .= "<select {$attrs}>$options</select>";
            } else {
                $lis = array_reduce(range(1, $pages), function (string $a, int $i) {
                    $options = [];
                    $options['dataAjaxClick'] = $this->act;
                    $options['dataAjaxPost'] = json_encode($this->post_data + ['value' => $i]);
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

    public function do_replace(string $source): string {
        $source = str_replace('{npp}', (string) $this->npp, $source);
        $source = str_replace('{page}', (string) $this->page, $source);
        $source = str_replace('{total}', (string) $this->total, $source);
        return $source;
    }

}
 
