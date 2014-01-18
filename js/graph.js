function Graph($container) {
    this.$container = $container;
    this.$a_canvas = null;
    this.width = this.$container.width();
    this.height = this.$container.height();
    this.obj = null;
    this.type = 0;
    this.options = {
        toggles: [
            {"name": "Height", "index": 2, "xAxis": "Height (m)", "min_value": "minEle", "max_value": "maxEle"},
            {"name": "Climb Rate", "index": 4, "xAxis": "Climb Rate (m/s)", "min_value": "min_cr", "max_value": "maximum_cr"},
            {"name": "Speed", "index": 5, "xAxis": "Speed (m/s)", "min_value": "min_speed", "max_value": "maximum_speed"}
        ]
    };

    var ths = this;

    this.initiated = false;


    this.init = function () {
        this.$container.css({position: "relative"});
        this.$container.html("<canvas id='graph_a_canvas' style='height:100%;width:100%' width='1000' height='" + this.height + "'></canvas>");
        this.$a_canvas = $('#graph_a_canvas');
        this.add_radios();
        this.initiated = true;
    };

    this.add_radios = function () {
        this.$container.find('.graph_toggles').remove();
        if (this.options.toggles) {
            var html = '<span class="graph_toggles">';
            this.options.toggles.each(function (toggle, count, ths) {
                html += '<label><input type="radio" name="graph_type" data-type="' + count + '" ' + (count == 0 ? 'checked' : '' ) + '/>' + toggle.name + '</label>';
            });
            html += '</span>';
            this.$container.prepend(html);
        }
        this.$container.find("[name=graph_type]").change(function () {
            ths.changeType($(this).data('type'));
        });
    };

    this.set_subsets = function (options) {
        this.options.toggles = options;
        if (this.initiated) {
            this.add_radios();
        }
    };

    this.resize = function (width) {
        if (this.initiated) {
            this.width = width - 5;
            this.$a_canvas[0].width = (width);
            this.setGraph();
        }
    };
    this.swap = function (obj) {
        this.obj = obj;
        this.setGraph();
    };
    this.changeType = function (val) {
        this.type = val;
        this.setGraph();
    };
    this.setGraph = function () {
        if (this.obj === null) {
            this.$container.hide();
            return;
        }
        this.$container.show();
        var title = this.options.toggles[this.type].xAxis;
        var index = this.options.toggles[this.type].index;
        var min_value = this.options.toggles[this.type].min_value;
        var max_value = this.options.toggles[this.type].max_value;
        var max = -1000000;
        var min = 10000000;
        this.obj.nxcl_data.track.each(function (track) {
            if (track.drawGraph) {
                if (parseFloat(track[max_value]) > max) {
                    max = parseFloat(track[max_value]);
                }
                if (parseFloat(track[min_value]) < min) {
                    min = parseFloat(track[min_value]);
                }
            }
        });
        this.drawGraph(max, min, '#' + this.obj.nxcl_data.track[0].colour, index, title);
    };

    this.drawGraph = function (max, min, colour, index, text) {
        this.$a_canvas[0].width = this.$a_canvas.width();
        var context = this.$a_canvas[0].getContext('2d');
        context.fillStyle = "rgba(255, 255, 255, 0.7)";
        context.fillRect(0, 0, this.width, this.height);

        for (x1 = 0; x1 <= 20; x1++) {
            var x_coord = x1 * this.width / 20;
            context.moveTo(x_coord, 0);
            context.lineTo(x_coord, this.height);
        }
        for (y1 = 0; y1 <= 10; y1++) {
            var y_coord = y1 * this.height / 10;
            context.moveTo(0, y_coord);
            context.lineTo(this.width, y_coord);
        }
        context.strokeStyle = '#DBDBDB';
        context.stroke();

        // Get graph data;
        if (this.obj) {
            var obj = this.obj.nxcl_data
        } else {
            return;
        }
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
        context.fillText(max, 10, 15);
        context.fillText(min, 10, this.height - 5);
        context.fillText(text, 10, this.height / 2);
    }
}


