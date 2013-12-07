<?php
namespace template;

use classes\ajax;
use classes\module;

abstract class html extends \core\template\html {

    public function __construct(module $module) {
        if (!ajax) {
            \core::$global_script[] = 'var map';
            \core::$inline_script[] = '
map = new UKNXCL_Map($("#map_wrapper"));
if (typeof google != \'undefined\') {
    map.load_map();
} else {
    $(\'#map\').children(\'p.loading\').html(\'Failed to load Google resources\');
}
';
        }
        parent::__construct($module);
    }

    public function get() {
        if (!ajax) {
            \core::$inline_script[] = 'loaded_modules = {"' . uri . '":true};';
            return '
<div id="left_col">
    <div id="nav">
        <ul>
            ' . \core::$singleton->module->get_main_nav() . '
        </ul>
    </div>
    <div id="main">
        <div id="' . $this->get_page_selector() . '" data-url="' . (isset($_POST['url']) ? $_POST['url'] : uri) . '">
            ' . $this->get_view() . '
        </div>
    </div>
</div>
<div id="map_wrapper">
    <div id="waypoint_mode_help" style="display:none">You are in waypoint mode</div>
    <div id="map_interface">
        <div id="map_interface_padding">
            <div id="graph_wrapper"></div>
            <div id="slider">
            </div>
            <div id="controls">
                <input id="play" type="submit" value="play" onclick="map.play()"/>
                <input id="pause" type="submit" value="pause" onclick="map.pause()"/>
                <a id="slider_time">00:00</a>
            </div>
        </div>
    </div>
    <div id="map_interface_3d">
        <span class="show">Show</span>
        <span class="hide">Hide</span>

        <div id="tree_content"><a href="#" title="Load Airspace" class="load_airspace button" onclick="map.load_airspace();">Load Airspace</a></div>
    </div>
    <div id="map"><p class="loading">Google Maps are loading...</p></div>
    <div id="map3d"><p class="loading">Google Earth is loading...</p></div>
</div>';
        } else {
            $content = $this->get_view();
            ajax::inject('#main', 'append', '<div id="' . $this->get_page_selector() . '" data-url="' . (isset($_POST['url']) ? $_POST['url'] : uri) . '">' . $content . '</div>', '#' . $this->get_page_selector());
        }
        return '';
    }
}
 