function LoadCallback(callbacks) {

    var array = callbacks || [];
    var triggered = false;

    this.trigger = function () {
        triggered = true;
        for (var i in array) {
            if (array.hasOwnProperty(i)) {
                array[i]();
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