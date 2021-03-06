var app = require('/usr/lib/node_modules/express')();
var http = require('http').Server(app);
var io = require('/usr/lib/node_modules/socket.io')(http);

app.get('/chatroom', function(req, res){
    res.sendFile(__dirname + '/index.html');
});

//當新的使用者連接進來的時候
io.on('connection', function(socket){
    
    //新user
    socket.on('add user',function(msg){
        socket.username = msg;
        //console.log("new user:"+msg+" logged.");
        io.emit('add user',{
            username: socket.username
        });
    });

    //監聽新訊息事件
    socket.on('chat message', function(msg){
        chat_msg = {
            username:socket.username,
            msg:msg
        };
        //console.log(chat_msg);
        //console.log(socket.username+":"+msg);

        //發佈新訊息
        io.emit('chat message', chat_msg);
    });

    //left
    socket.on('disconnect',function(){
        //console.log(socket.username+" left.");
        io.emit('user left',{
            username:socket.username
        });
    });


});

//指定port
http.listen(process.env.PORT || 4000, function(){
    console.log('listening on *:4000');
});