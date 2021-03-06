var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var port = 3000;


app.get('/', function(req, res){
    res.sendFile(__dirname + '/test.html');
});

io.on('connection', function(socket){
    socket.on('chat message', function(msg){
        io.emit('chat message', msg);
    });


    socket.on('disconnect', function(){
        console.log('user disconnected');
    });
});

http.listen(port, function(){
    console.log('listening on *:'+port);
});