<!doctype html>
<html>
<head>
    <title>Socket.IO chat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 13px Helvetica, Arial; }

        #m{
            border: 2px solid black;
            padding: 10px;
            width: 90%;
            margin: .5%;
            position: fixed;
            bottom: 0;

        }
        #send{
            width: 8%;
            margin: .5%;
            background: rgb(121,2,2);
            color:white;

            padding: 10px;
            position: fixed;
            bottom: 0;
            right:5px;
        }
        #message_block{
            width:100%;
            position: absolute;
            top:0;
            bottom:5%;
            margin-bottom:10px;
            border: solid 2px black;
            overflow:auto;
        }
        #messages {
            list-style-type: none;
            margin: 10px;

            padding: 0;
        }
        #messages li {
            padding: 5px 10px;
            font-size:16pt;
        }
        #messages li:nth-child(odd) {
            /*background: #eee;*/
        }
    </style>

    <script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>



        var socket = io('http://192.168.136.128:4000');

        $(document).ready(function(){
            var _token = $("input[name='csrf-token']").val();
            showMessage();
            var name = "guest"; //prompt("請輸入暱稱","guest");

            if(name=="" || name==null){
                name = "guest";
            }

            //tell server
            socket.emit("add user",name);

            //監聽新訊息事件
            socket.on('chat message', function(data){
                var content = data.msg;
                appendMessage(data.username, content);
            });

//            socket.on('add user',function(data){
//                appendMessage(data.username+" 已加入，在 "+getTodayDate());
//            });
//
//            socket.on('user left',function(data){
//                appendMessage(data.username+" 已離開，在 "+getTodayDate());
//            });

            $('#send').click(function(){
                var text = $('#m').val();
                socket.emit('chat message', text);
                $('#m').val('');
                return false;
            });

            $("#m").keydown(function(event){
                if ( event.which == 13 ){
                    $('#send').click();
                }
            });

            $("#m").on('drop', function (e){
                e.preventDefault();
                e.stopPropagation();
                var image = e.originalEvent.dataTransfer.files;
                createFormData(image);
            });

            function showMessage(){
                $.ajax({
                    url: "/chat/get_massage",
                    type: "GET",
                    data:{},
                    headers: {'X-CSRF-TOKEN': _token},
                    beforeSend: function(){

                    },
                    statusCode: {
                        404: function () {
                            alert('找不到頁面');
                        },
                        500: function () {
                            alert('內部伺服器錯誤');
                        }
                    },
                    success: function(html){
                        //console.log(html);
                        $.each(html, function(k, v){
                            //console.log(v);
                            $('#messages').append($('<li>').html(v));
                        });

                        $("#message_block").scrollTop(($("#messages").height()));
                    }
                });
            }

            function appendMessage(name, msg){
                $.ajax({
                    url: "/chat/save",
                    type: "POST",
                    data:{name:name, msg_data:msg},
                    headers: {'X-CSRF-TOKEN': _token},
                    beforeSend: function(){

                    },
                    statusCode: {
                        404: function () {
                            alert('找不到頁面');
                        },
                        500: function () {
                            alert('內部伺服器錯誤');
                        }
                    },
                    success: function(html){
                        //console.log(html);
                        $('span#text').text(msg);
                        var content = '<div>'+name+':</div> <div style="background:#eaeaea; padding:10px;margin:10px 0px 10px 20px;">';
                        content += $('span#text').html();
                        content += '<span style="font-size:10pt;color:#aaa;"> '+getTodayDate()+'</span></div>';
                        $('#messages').append($('<li>').html(content));

                        $("#message_block").scrollTop($("#messages").height());
                    }
                });
            }

            function createFormData(image) {
                var formImage = new FormData();
                formImage.append('userImage', image[0]);
                uploadFormData(formImage);
            }

            function uploadFormData(formData) {

                //console.log(formData);
                //console.log(_token);
                //return false;

                $.ajax({
                    url: "/chat/upload",
                    type: "POST",
                    data:formData,
                    headers: {'X-CSRF-TOKEN': _token},
                    contentType:false,
                    cache: false,
                    processData: false,
                    beforeSend: function(){

                    },
                    statusCode: {
                        404: function () {
                            alert('找不到頁面');
                    },
                        500: function () {
                            alert('內部伺服器錯誤');
                    },
                        413: function (e) {
                            alert(e.statusText);
                            appendMessage(name+" :<BR>傳送檔案過大");
                        }
                },
                success: function(html){
                    //console.log(html);
                    appendMessage(name+" :<BR><a href="+html.file_name+" target=_blank><img src="+html.file_name+" title="+html.file_name+" width=300></a><span style=font-size:12pt;color:#369;>"+html.file_size+"KB</span> - <span style=font-size:10pt;color:#aaa;>"+getTodayDate()+"</span>");
                }});
            }

            //取得今天的日期，讓使用者送出訊息時參考用
            function getTodayDate() {
                var str = '';

                // 宣告日期物件
                var today = new Date();

                // 年
                var today_year = today.getFullYear();
                str += today_year;

                // 月
                var today_month = today.getMonth() + 1;
                if (today_month >= 10)
                    str += '-' + today_month;
                else
                    str += '-0' + today_month;

                // 日
                var today_date = today.getDate();
                if (today_date >= 10)
                    str += '-' + today_date;
                else
                    str += '-0' + today_date;

                var today_hour = today.getHours();
                if (today_hour >= 10)
                    str += ' ' + today_hour;
                else
                    str += ' 0' + today_hour;

                var today_minute = today.getMinutes();
                if (today_minute >= 10)
                    str += ':' + today_minute;
                else
                    str += ':0' + today_minute;

                var today_second = today.getSeconds();
                if (today_second >= 10)
                    str += ':' + today_second;
                else
                    str += ':0' + today_second;
                return str;
            }
        });




    </script>
</head>
<body>
<input type="hidden" id="csrf-token" name="csrf-token" value="{{csrf_token()}}">
<span id="text" style="visibility: hidden"></span>
<div id="message_block">
    <ul id="messages"></ul>
    <div>
        <input id="m" autocomplete="off" /><button id="send">Send</button>
    </div>
</div>
</body>
</html>