<?php
class dashboard_view extends view {

    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('h2', 'UKNXCL Conversion Tools'),
                $this->get_coordinate_converter()
            )
        );
        return $html;
    }

    public function get() {
        return $this->get_view()->get();
    }

    public function get_coordinate_converter() {
        $form = new coordinate_conversion_form();
        return $form->get_html();
    }
}
