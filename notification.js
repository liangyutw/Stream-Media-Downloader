
var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('notification', function(err, count) {
  console.log('connect!');
});

io.on('connection', function(socket) {
    socket.on('set-token', function(token) {
        //console.log(token);
        socket.join('token:' + token);
    });

});

redis.on('message', function(channel, notification) {

    notification = JSON.parse(notification);
// console.log(notification);
    // 將訊息推播給使用者
    if (notification.data.token == "" || notification.data.token == null) {
        io.emit('notification', notification.data);
    }else{
        io.to('token:' + notification.data.token).emit('notification', notification.data);
        //console.log(notification.data);
    }
});

http.listen(3000, function() {
    console.log('Listening on Port 3000');
});