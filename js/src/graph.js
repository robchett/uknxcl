function Graph($container) {
    this.$container = $container;
    this.$a_canvas = null;
    this.width = this.$container.width();
    this.height = this.$container.height();
    this.obj = null;
    this.type = 0;
    this.title = '';
    this.options = {
        toggles: []
    };
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

    var ths = this;

    this.initiated = false;


    this.init = function () {
        this.$container.css({position: "relative"});
        this.$container.html("<canvas class='graph_a_canvas' style='height:100%;width:100%' width='1000' height='" + this.height + "'></canvas>");
        this.$a_canvas = this.$container.find('.graph_a_canvas');
        if(this.title) {
            this.$container.prepend('<strong style="position: absolute; top: 10px; width: 100%; left: 0; text-align: center">' + this.title + '</strong>');
        }
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

    this.addLegend = function (colour, obj) {
        this.$container.find('.legend').remove();
        if (this.legend.show) {
            var html = '';
            obj.track.each(function (track) {
                colour = track.colour || colour;
                if (!track.name) {
                    console.log(track);
                    return;
                }
                html += '<span class="legend_entry" style="display: block">' + track.name + '<span class="line" style="display:inline-block; margin-left:10px; width:7px; height:2px; margin-bottom: 5px; background:#' + colour + ';text-indent:-999px;overflow:hidden">' + colour + '</span></span>';
            });
            this.$container.prepend('<div class="legend" style="background-color:#ffffff; padding: 4px; border: 1px solid #EEEEEE; position: absolute;' + this.legend.position.x + ':10px;' + this.legend.position.y + ':10px;">' + html + '</div>');
        }
    };

    this.draw_graph = function (max, min, colour, index, text) {
        this.$a_canvas[0].width = this.$a_canvas.width();
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

        // Get graph data;
        if (this.obj) {
            var obj = this.obj.nxcl_data
        } else {
            return;
        }
        this.addLegend(colour, obj);
        var Xscale = this.width / (obj.xMax - obj.xMin);
        var Yscale = this.height / (max - min);
        obj.track.each(function (track) {
            if (track.draw_graph) {
                context.beginPath();
                context.strokeStyle = track.colour || colour;
                for (j in track.data) {
                    var coord = track.data[j];
                    context.lineTo(coord[0] * Xscale, ths.height - ((coord[index] - min) * Yscale));
                }
                context.stroke();
            }
        });
        context.font = '12px sans-serif';
        context.fillStyle = '#444';
        context.fillText(max, 10, 15);
        context.fillText(min, 10, this.height - 5);
        context.fillText(text, 10, this.height / 2);
    }
}



Number.prototype.roundDown = function (significant) {
    significant = significant || 2;
    var power = Math.floor(Math.log(this) / Math.LN10) ;
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
        var new_number = (this / Math.pow(10, power - significant)) ;
        return (this > 0 ? Math.ceil(new_number) : Math.floor(new_number)) * Math.pow(10, power - significant);
    }
    return Math.ceil(this);
};