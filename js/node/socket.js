var io = require('socket.io').listen(8000);

var clients = [];

io.sockets.on('connection', function (socket) {
    console.log('New connection');
    var clientID;
    socket.on('set nickname', function (name) {
        clientID = name;
        clients[clientID] = socket;
    });
    socket.on('message', function (response) {
        console.log(response);
        var data = JSON.parse(response);
        if(data.clientID && clients[data.clientID]) {
            console.log('Receiver found');
            clients[data.clientID].emit('message', data.message);
        } else {
            console.log('Receiver not found');
        }
    });
    socket.on('disconnect', function () {
        if(clientID) {
            delete clients[clientID];
        }
    });
});