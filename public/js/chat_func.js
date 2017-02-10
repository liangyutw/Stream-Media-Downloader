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
            var msg_content = '';
            $.each(html, function(k, v){
                //console.log(v);
                msg_content += '<li>'+v+'</li>';
            });

            $('#messages').html(msg_content);
//                        console.log(($("#message_block").height()+$(document).height()));
//                        console.log($(document).height());
            var message_block = parseInt($("#messages").height());
            var document_height = parseInt($(document).height());

            $("#message_block").scrollTop((message_block+document_height));
        }
    });

}

function appendMessage(msg_data){

    var name = msg_data.name;
    var msg = msg_data.text;
    var upload_status = (msg_data.upload_status) ? msg_data.upload_status : "";

    $.ajax({
        url: "/chat/save",
        type: "POST",
        data:{
            name:name,
            msg_data:msg,
            upload_status:upload_status
        },
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

//                        $('span#text').text(msg);
//                        var content = '<div>'+name+':</div> <div style="background:#eaeaea; padding:10px;margin:10px 0px 10px 20px;">';
//                        content += $('span#text').html();
//                        content += '<span style="font-size:10pt;color:#aaa;"> '+getTodayDate()+'</span></div>';
//                        $('#messages').append($('<li>').html(content));

            socket.emit('chat message', msg);
            var message_block = parseInt($("#messages").height());
            var document_height = parseInt($(document).height());

            $("#message_block").scrollTop((message_block+document_height));
        }
    });
}

function createFormData(image) {
    var formImage = new FormData();
    formImage.append('userImage', image[0]);
    formImage.upload_status = 1;
    uploadFormData(formImage);
}

function uploadFormData(formData) {

//                console.log(formData);
    //console.log(_token);
//                return false;

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
                var msg_data = {
                    name:name,
                    text:"傳送檔案過大"
                };
                appendMessage(msg_data);
            }
        },
        success: function(html){
            //console.log(html);return false;
            image_height = html.image_height;

            var unit = (html.file_size > 1024) ? 'KB' : 'byte';

            if (html.extension == 'jpg' || html.extension == 'png' || html.extension == 'gif') {
                var msg = "<a href="+html.file_name+" target=_blank><img src="+html.file_name+" title="+html.file_name+" width=300></a><span style=font-size:12pt;color:#369;>"+html.file_size+unit+"</span> - <span style=font-size:10pt;color:#aaa;>"+getTodayDate()+"</span><BR><a href='/dl_chat_pic"+html.file_name.replace('chat_upload_pic/','')+"' style=font-size:10pt;>下載</a>";
            }else{
                var msg = "<div style='height:30px;'>已傳送 <b>"+html.originalname+"</b></div><span style=font-size:12pt;color:#369;>"+html.file_size+unit+"</span> - <span style=font-size:10pt;color:#aaa;>"+getTodayDate()+"</span><BR><a href='/dl_chat_pic"+html.file_name.replace('chat_upload_pic/','')+"' style=font-size:10pt;>下載</a>";
            }


            var msg_data = {
                name:name,
                text:msg,
                upload_status:formData.upload_status,
                image_height:html.image_height
            };

            appendMessage(msg_data);
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