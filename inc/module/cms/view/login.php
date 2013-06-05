<?php
class login_view extends cms_view {

    public function get_view() {
        $form = new cms_login_form();
        return $form->get_html();
    }
}
