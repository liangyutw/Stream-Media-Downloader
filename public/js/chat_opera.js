var socket = io('http://192.168.136.128:4000');
var image_height = '';

$(document).ready(function(){
    
    showMessage();

    var name = prompt("請輸入暱稱","guest");

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
        //var text = $('#m').val();
        var msg_data = {
            name:name,
            text:$('#m').val()
        };
        appendMessage(msg_data);
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


});