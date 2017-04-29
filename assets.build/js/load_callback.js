function load_callback(callbacks) {

    var array = [];
    var triggered = false;

    this.trigger = function () {
        triggered = true;
        for (var i in this.array) {
            if (this.array.hasOwnProperty(i)) {
                callback();
            }
        }
    }; 
    this.push = function (callback) {
        if (triggered) {
            callback();
        } else {
            array.push(callback);
        }
    }
}