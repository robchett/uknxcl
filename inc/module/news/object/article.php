<?php
namespace news;
class article extends \table{
    use \table_trait;

    /* @var string */
    public $date;
    /* @var string */
    public $poster;
    /* @var string */
    public $post;
    /* @var int */
    public $aid;
    /* @var string */
    public $title;

    public $table_key = 'aid';
    public static $module_id = 8;


    public function get_url() {
        return '/news/' . $this->aid;
    }

    public function get_title() {
        return '<span class="date">' . date('d/m/Y', strtotime($this->date)) . '</span><span class="name">' .
        $this->title .
        '</span>
        <span class="author">By: ' . $this->poster . '</span>';
    }

    public function get_body() {

    }

    public function get_cell() {
        $html = '';
        $html .= '<article id="article' . $this->aid . '">';
        $html .= '<div class="title">';
        $html .= $this->get_title();
        $html .= '</div>';
        $html .= '<div class="content">';
        $html .= (empty($this->snippet) ? $this->post : $this->snippet . '<a href="' . $this->get_url() . '" class="button">Read more</a>');
        $html .= '</div>';
        $html .= '</article>';
        return $html;
    }


    /**
     * @param array $fields
     * @param array $options
     * @return article_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return article_array::get_all($fields, $options);
    }
}

class article_array extends \table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'article_iterator');
        $this->iterator = new article_iterator($input);
    }

    /* @return article */
    public function next() {
        return parent::next();
    }

}

class article_iterator extends \table_iterator {

    /* @return article */
    public function key() {
        return parent::key();
    }
}