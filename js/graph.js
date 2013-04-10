function Graph($container) {
    this.$container = $container;
    this.$container.html("<a style='position:absolute;left:60px;z-index:1000000'>" + '<input type="radio" name="graph_type" onclick="map.graph.changeType(1)" checked>Height</input>' + '<input type="radio" name="graph_type" onclick="map.graph.changeType(2)">Climb</input>' + '<input type="radio" name="graph_type" onclick="map.graph.changeType(3)">Speed</input></a>' + "<canvas id='graph_a_canvas' style='height:100%;width:100%' width='1000' height='200'></canvas>");
    this.a_canvas = $('#graph_a_canvas');
    this.width = this.$container.width();
    this.height = this.$container.height();
    this.obj = null;
    this.type = 1;

    this.resize = function (width) {
        this.width = width - 5;
        this.a_canvas[0].width = (width);
        this.setGraph();
    }
    this.swap = function (obj) {
        this.obj = obj;
        this.setGraph();
    }
    this.changeType = function (val) {
        this.type = val;
        this.setGraph();
    }
    this.setGraph = function () {
        if (this.obj === null) {
            this.drawGraph(0, 0, '', 2, 'Height (m)');
        } else if (this.type === 1) {
            var maxEle = 0;
            var minEle = 10000000000;
            for (var i in this.obj.nxcl_data.track) {
                var track = this.obj.nxcl_data.track[i];
                if (track.drawGraph) {
                    if (track.maxEle > maxEle)maxEle = track.maxEle;
                    if (track.minEle < minEle)minEle = track.minEle;
                }
            }
            this.drawGraph(maxEle, minEle, '#' + this.obj.nxcl_data.track[0].colour, 2, 'Height (m)');
        } else if (this.type == 2) {
            this.drawGraph(this.obj.nxcl_data.track[0].max_cr, this.obj.nxcl_data.track[0].min_cr, '#FF00FF', 4, 'Clime Rate (m/s)');
        } else {
            this.drawGraph(this.obj.nxcl_data.track[0].max_speed, 0, '#0000FF', 5, 'Speed (m/s)');
        }
    }

    this.drawGraph = function (max, min, colour, index, text) {
        this.a_canvas[0].width = this.a_canvas.width();
        var context = this.a_canvas[0].getContext('2d');
        context.fillStyle = "rgba(255, 255, 255, 0.7)";
        context.fillRect(0, 0, this.width, this.height);

        for (x1 = 0; x1 <= 20; x1++) {
            context.moveTo(x1 * this.width / 20, 0);
            context.lineTo(x1 * this.width / 20, this.height);
        }
        for (y1 = 0.5; y1 < this.height; y1 += 10) {
            context.moveTo(0, y1);
            context.lineTo(this.width, y1);
        }
        context.strokeStyle = '#DBDBDB';
        context.stroke();

        // Get graph data;
        if (this.obj)obj = this.obj.nxcl_data; else return;
        var size = this.obj.size();
        var Xscale = this.width / (obj.EndT - obj.StartT);
        var Yscale = this.height / (max - min);
        for (i in obj.track) {
            var track = obj.track[i];
            if (track.drawGraph) {
                context.beginPath();
                context.strokeStyle = track.colour || colour;
                for (j in track.coords) {
                    var coord = track.coords[j];
                    context.lineTo(coord[3] * Xscale, this.height - ((coord[index] - min) * Yscale));
                }
                context.stroke();
            }
        }
        context.font = '12px sans-serif';
        context.fillStyle = '#444';
        context.fillText(max, 10, 10);
        context.fillText(min, 10, this.height);
        context.fillText(text, 10, this.height / 2);
    }
}


