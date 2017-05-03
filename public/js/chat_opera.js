
var image_height = '';

$(document).ready(function(){

    var socket = io('http://192.168.136.128:4000');
    //連線錯誤（可能 server 端有狀況）
    socket.on('disconnect', function() {
        $('#socket_error').text('(失去連線)');
        $('#send').attr('disabled', true);
    });
    socket.on('connect', function() {
        $('#socket_error').text('');
        $('#send').attr('disabled', false);
    });

    showMessage();

    var name = user_name;//prompt("請輸入暱稱","guest");

    if(name=="" || name==null){
        name = "guest";
    }

    //tell server
    socket.emit("add user",name);

    //監聽新訊息事件

    socket.on('chat message', function(data){
        showMessage();
    });

//            socket.on('add user',function(data){
//                appendMessage(data.username+" 已加入，在 "+getTodayDate());
//            });
//
//            socket.on('user left',function(data){
//                appendMessage(data.username+" 已離開，在 "+getTodayDate());
//            });

    $('#send').click(function(){
        var msg = $.trim($('#m').val()),
            getTime = getTodayDate_noformat(),
            msg_token = utoa(user_id+'_'+msg+'_'+getTime).replace('/','|');

        if(msg == "") {
            alert('請輸入訊息');
            return false;
        }

        var msg_data = {
            name:name,
            chat_id:chat_id,
            msg_token:msg_token,
            //text:'<div style="padding: 0px 0px 0px 5px;margin-left: 15px;background-color: #eaeaea;">'+msg+' <a href=/chat/del_msg/'+chat_id+'/'+msg_token+' style=font-size:10pt;>刪除</a> <span style=font-size:10pt;color:#aaa;>'+getTodayDate()+'</span></div>'
            text:msg
        };

        appendMessage(msg_data);
        $('#m').val('');

        return false;
    });

    $("#m").keydown(function(event){
        //console.log(event);
        if (event.shiftKey == true && event.which == 13) {
            $(this).attr("rows","3").css({"overflow":"auto"});
            $(this).focus();
        }
        else if ( event.which == 13 ){
            //$('#m').replace('\n', '').attr("rows", 1);
            $('#send').click();
        }
    });

    $("#m").on('drop', function (e){
        e.preventDefault();
        e.stopPropagation();
        var image = e.originalEvent.dataTransfer.files;
        createFormData(image);
    });

});