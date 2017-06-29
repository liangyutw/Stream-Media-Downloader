@extends('layouts.app')

<?php
// if (isset($send_data)){
// echo "<pre>";print_r($send_data);
// }
// exit;
?>
@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-heading">程式操作說明</div>
                <div class="panel-body">
                請確保以下幾項服務開啟
                <ul>
                    <li>Laravel Application（Nginx or php artisan serve）</li>
                    <li>socket.io server（node notification.js or nodemail.js）</li>
                    <li>隊列監聽器（php artisan queue:listen）</li>
                    <li>Redis server
                        <ul>
                            <li>執行 systemctl status redis 查看是否 active (running)</li>
                            <li>執行 redis-cli 進入設定密碼</li>
                            <li>執行 config set requirepass n!KpH8a+z?</li>
                            <li>執行 auth n!KpH8a+z? 認証密碼</li>
                            <li>執行 ping，回應 pong 表示成功</li>
                        </ul>
                    </li>
                </ul>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">發生問題排除</div>
                <div class="panel-body">
                當準備要執行其他的 node js的時候，會發生錯誤
                <blockquote>
                    [ioredis] Unhandled error event: ReplyError: NOAUTH Authentication required.
                        at JavascriptReplyParser.returnError (/usr/share/nginx/html/laravel/node_modules/ioredis/lib/redis/parser.js:25:25)
                        at JavascriptReplyParser.run (/usr/share/nginx/html/laravel/node_modules/redis-parser/lib/javascript.js:135:18)
                        at JavascriptReplyParser.execute (/usr/share/nginx/html/laravel/node_modules/redis-parser/lib/javascript.js:112:10)
                        at Socket.<anonymous> (/usr/share/nginx/html/laravel/node_modules/ioredis/lib/redis/event_handler.js:107:22)
                        at emitOne (events.js:96:13)
                        at Socket.emit (events.js:188:7)
                        at readableAddChunk (_stream_readable.js:176:18)
                        at Socket.Readable.push (_stream_readable.js:134:10)
                        at TCP.onread (net.js:548:20)
                </blockquote>
                <ul>
                    <li>先重啟 redis ，執行 systemctl restart redis</li>
                    <li>再執行 node notification.js(或 nodemail.js)</li>
                    <li>執行 redis-cli 進入設定密碼</li>
                    <li>執行 config set requirepass n!KpH8a+z?</li>
                    <li>執行 auth n!KpH8a+z? 認証密碼</li>
                    <li>執行 ping，回應 pong 表示成功</li>
                </ul>
                </div>
            </div>


            <div>執行根目錄的 notification.js</div>
            <div>顯示路徑 http://192.168.136.128/home/1 </div>
            <div class="panel panel-default">

                <div class="panel-heading">boardcast area</div>

                <div class="panel-body">
                {!! Form::open(['route' => 'boardcast_create', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    <input name="user_id" type="text" value="{{$send_data['user_id'] or ""}}" placeholder="輸入會員 id"><BR>
                    <textarea name="content" cols="100" rows="10" placeholder="輸入顯示內容">{{$send_data['content'] or ""}}</textarea><BR>
                    <input name="go_boardcast" type="submit" value="送出">
                {!! Form::close() !!}
                <div id="notification_return_msg"></div>
                </div>
            </div>

            <div>執行根目錄的 nodemail.js</div>
            <div>輸入的信箱會收到由這表單送出的內容</div>
            <div class="panel panel-default">

                <div class="panel-heading">register area</div>

                <div class="panel-body">
                    {!! Form::open(['route' => 'register_store', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    <input name="register_name" type="text" value="" placeholder="輸入註冊名稱"><BR>
                    <input name="register_email" type="text" value="" placeholder="輸入註冊信箱"><BR>
                    <input name="go_boardcast" type="submit" value="送出">
                    {!! Form::close() !!}
                    <div id="return_msg"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<p></p>

@endsection

@section('scripts')
    <script src="/js/socket.io-1.4.5.js"></script>
    <script src="/js/jquery-1.12.4.min.js"></script>
    <script src="/js/register_return.js"></script>
@endsection
