<!doctype html>
<html>
<head>
    <meta charset=utf-8>
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Socket.IO chat</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/chat.css">

</head>
<body>

<input type="hidden" id="csrf-token" name="csrf-token" value="{{csrf_token()}}">
<span id="text" style="visibility: hidden"></span>
<div id="message_block">
    <ul id="messages"></ul>
</div>
<div class="input_area">
    <input id="m" autocomplete="off" class="form-control" placeholder="輸入訊息"/><button id="send" class="btn btn-success">送出</button>
</div>

</body>
</html>

<script src="/js/socket.io-1.4.5.js"></script>
<script src="/js/jquery-1.12.4.min.js"></script>
<script src="/js/jquery-1.12.1-ui.js"></script>

<script>var _token = $("input[name='csrf-token']").val();</script>
<script src="/js/chat_func.js"></script>
<script src="/js/chat_opera.js"></script>

<!-- 最新編譯和最佳化的 JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>


