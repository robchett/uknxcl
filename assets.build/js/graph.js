function Graph($container) {
    this.$container = $container;
    this.$a_canvas = null;
    this.width = this.$container.width();
    this.height = this.$container.height();
    this.obj = null;
    this.type = 0;
    this.options = {};
    this.options.toggles = [
        {"name": "Height", "index": 1, "xAxis": "Height (m)", "min_value": "min_ele", "max_value": "max_ele"},
        {"name": "Climb Rate", "index": 2, "xAxis": "Climb Rate (m/s)", "min_value": "min_cr", "max_value": "max_cr"},
        {"name": "Speed", "index": 3, "xAxis": "Speed (m/s)", "min_value": "min_speed", "max_value": "max_speed"}
    ];
    this.$container.css({position: "relative"});
    this.$container.html("<canvas class='graph_a_canvas' style='height:100%;width:100%' width='1000' height='" + this.height + "'></canvas>");
    this.$a_canvas = this.$container.find('.graph_a_canvas');
    this.$container.hide();
    this.initiated = true;
    this.legend = {
        show: false,
        position: {
            x: "right",
            y: "top"
        }
    };
    this.grid = {
        x: {
            show: true,
            count: 20
        },
        y: {
            show: true,
            count: 10
        }
    };
    this.initiated = false;
}

Graph.prototype.add_radios = function () {
    this.$container.find('.graph_toggles').remove();
    if (this.options.toggles) {
        var html = '<span class="graph_toggles">';
        this.options.toggles.each(function (toggle, count, ths) {
            html += '<label><input type="radio" name="graph_type" data-type="' + count + '" ' + (count == ths.type ? 'checked' : '' ) + '/>' + toggle.name + '</label>';
        }, this);
        html += '</span>';
        this.$container.prepend(html);
    }
    var ths = this;
    this.$container.find("[name=graph_type]").change(function () {
        ths.changeType($(this).data('type'));
    });
};
Graph.prototype.set_subsets = function (options) {
    this.options.toggles = options;
    if (this.initiated) {
        this.add_radios();
    }
};
Graph.prototype.resize = function (width) {
    if (this.initiated) {
        this.width = width - 5;
        this.$a_canvas[0].width = (width);
        this.setGraph();
    }
};
Graph.prototype.swap = function (obj) {
    this.obj = obj;
    this.setGraph();
};
Graph.prototype.changeType = function (val) {
    this.type = val;
    this.setGraph();
};
Graph.prototype.setGraph = function () {
    if (this.obj === null) {
        this.$container.hide();
        return;
    }
    this.$container.show();
    var title = '';
    var index = '';
    var min_value = 0;
    var max_value = 0;
    if (typeof this.options.toggles[this.type] != 'undefined') {
        var title = this.options.toggles[this.type].xAxis;
        var index = this.options.toggles[this.type].index;
        var min_value = this.options.toggles[this.type].min_value;
        var max_value = this.options.toggles[this.type].max_value;
    }
    var max = -1000000;
    var min = 10000000;
    this.obj.nxcl_data.track.each(function (track) {
        if (track.draw_graph) {
            if (parseFloat(track[max_value]) > max) {
                max = parseFloat(track[max_value]);
            }
            if (parseFloat(track[min_value]) < min) {
                min = parseFloat(track[min_value]);
            }
        }
    });
    this.draw_graph(max.roundUp(), min.roundDown(), '#' + this.obj.nxcl_data.track[0].colour, index, title);
};
Graph.prototype.addLegend = function (colour, obj) {
    this.$container.find('.legend').remove();
    if (this.legend.show) {
        var html = '';
        obj.track.each(function (track) {
            colour = track.colour || colour;
            html += '<span class="legend_entry" style="display: block">' + track.name + '<span class="line" style="display:inline-block; margin-left:10px; width:7px; height:2px; margin-bottom: 5px; background:#' + colour + ';text-indent:-999px;overflow:hidden">' + colour + '</span></span>';
        });
        this.$container.prepend('<div class="legend" style="background-color:#ffffff; padding: 4px; border: 1px solid #EEEEEE; position: absolute;' + this.legend.position.x + ':10px;' + this.legend.position.y + ':10px;">' + html + '</div>');
    }
};

Graph.prototype.draw_graph = function (max, min, colour, index, text) {
    // Get graph data;
    if (this.obj && this.obj.nxcl_data.track.length) {
        this.$container.show();
        this.$a_canvas[0].width = this.$a_canvas.width();
        this.width = this.$container.width();
        this.height = this.$container.height();
        var context = this.$a_canvas[0].getContext('2d');
        context.fillStyle = "rgba(255, 255, 255, 0.7)";
        context.fillRect(0, 0, this.width, this.height);

        if (this.grid.x.show) {
            for (var x1 = 0; x1 <= (this.grid.x.count - 1); x1++) {
                var x_coord = x1 * this.width / (this.grid.x.count - 1);
                context.moveTo(x_coord, 0);
                context.lineTo(x_coord, this.height);
            }
        }
        if (this.grid.y.show) {
            for (var y1 = 0; y1 <= (this.grid.y.count - 1); y1++) {
                var y_coord = y1 * this.height / (this.grid.y.count - 1);
                context.moveTo(0, y_coord);
                context.lineTo(this.width, y_coord);
            }
        }
        context.strokeStyle = '#DBDBDB';
        context.stroke();
        var obj = this.obj.nxcl_data
        this.addLegend(colour, obj);
        var Xscale = this.width / (obj.xMax - obj.xMin);
        var Yscale = this.height / (max - min);
        if (obj.track.count) {
            obj.track.each(function (track, count, ths) {
                if (track.draw_graph) {
                    context.beginPath();
                    context.strokeStyle = track.colour ? ('#' + track.colour) : colour;
                    for (j in track.data) {
                        var coord = track.data[j];
                        context.lineTo(coord[0] * Xscale, ths.height - ((parseInt(coord[index]) - min) * Yscale));
                    }
                    context.stroke();
                }
            }, this);
        } 
        context.font = '12px sans-serif';
        context.fillStyle = '#444';
        context.fillText(max, 10, 15);
        context.fillText(min, 10, this.height - 5);
        context.fillText(text, 10, this.height / 2);
        this.add_radios();
    } else {
        this.$container.hide();
    }
}

Number.prototype.roundDown = function (significant) {
    significant = significant || 2;
    var power = Math.floor(Math.log(this) / Math.LN10);
    if (power > significant) {
        var new_number = (this / Math.pow(10, power - significant));
        return (this < 0 ? Math.ceil(new_number) : Math.floor(new_number)) * Math.pow(10, power - significant);
    }
    return Math.floor(this);
};

Number.prototype.roundUp = function (significant) {
    significant = significant || 2;
    var power = Math.floor(Math.log(this) / Math.LN10);
    if (power > significant) {
        var new_number = (this / Math.pow(10, power - significant));
        return (this > 0 ? Math.ceil(new_number) : Math.floor(new_number)) * Math.pow(10, power - significant);
    }
    return Math.ceil(this);
};