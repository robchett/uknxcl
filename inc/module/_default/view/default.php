<div id="left_col">
    <div id="nav">
        <ul>
            <?php echo $this->get_main_nav() ?>
        </ul>
    </div>
    <div id="main">
        <?php echo $this->get_body() ?>
    </div>
</div>
<div id="map_wrapper">
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
</div>