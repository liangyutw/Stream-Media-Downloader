
// var io = require('socket.io-client');
//建立 socket.io 的連線
var notification = io.connect('http://192.168.136.128:3000');

// 當連接到 socket.io server 時觸發 set-token 設定使用者的 room
notification.on('connect', function() {

  notification.emit('set-token', Notification.TOKEN);
});

// 當從 socket.io server 收到 notification 時將訊息印在 console 上
notification.on('notification', function(data) {
    notification.emit('return_status', true);
    console.log(data);
    var msg = '<div class="container spark-screen"><div class="row"><div class="col-md-10 col-md-offset-1"><div class="panel panel-default"><div class="panel-heading">Message on '+data.date+'</div><div class="panel-body">'+data.message+'</div></div></div></div></div>';
    $("p").append(msg);
});
