<?php
use classes\css\css;

header("Content-type: text/css");
define('load_core', false);
include($_SERVER['DOCUMENT_ROOT'] . '/index.php');

$css = new css('less');
$css->add_resource_root('/.core/css/');
$css->add_resource_root('/.core/module/cms/css/');
$css->add_resource_root('/module/cms/css/');
$css->cached_name = 'cms_css';
echo $css->compile();