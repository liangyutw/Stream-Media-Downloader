
// 建立 socket.io 的連線
var register = io.connect('http://192.168.136.128:3001');

// 當連接到 socket.io server 時觸發 set-token 設定使用者的 room
register.on('connect', function() {
    //register.emit('set-token', RegisterReturn.TOKEN);
});

// 當從 socket.io server 收到 notification 時將訊息印在 console 上
register.on('register_mail', function(data) {
    //console.log(data);
    if (data.return == 1) {
        $("#return_msg").text('已寄出驗証信!');
    }
});