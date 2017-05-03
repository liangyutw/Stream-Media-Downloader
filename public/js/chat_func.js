function showMessage(){

    $.ajax({
        url: "/chat/get_massage",
        type: "GET",
        data:{chat_id:chat_id},
        headers: {'X-CSRF-TOKEN': _token},
        beforeSend: function(){

        },
        statusCode: {
            404: function () {
                console.log('找不到頁面');
            },
            500: function () {
                console.log('內部伺服器錯誤');
            }
        },
        success: function(html){
            // console.log(html);
            // return false;

            var msg_content = '';

            if (typeof html.room_msg == 'object') {
                $.each(html.room_msg, function (k, sub_arr) {

                    $.each(sub_arr, function (sub_k, sub_v) {

                        if (typeof sub_v == 'string') {
                            msg_content += '<li>'+sub_v+'</li>';
                        }else {
                            msg_content += '<li><div>' + sub_k + ':</div>';
                            $.each(sub_v, function (sub_s_k, sub_s_v) {
                                msg_content += sub_s_v;
                            });
                            msg_content += '</li>';
                        }

                    });
                });

                $('#messages').html(msg_content);

                var message_block = parseInt($("#messages").height());
                var document_height = parseInt($(document).height());

                $("#message_block").scrollTop((message_block + document_height));
            }

            if (typeof html.history_path == 'string' && html.history_path != "") {
                $('#history_path').html("<a href=javascript:getHistoryMsg('"+chat_id+"','"+html.history_path+"','"+html.init_json_cnt+"');>歷史訊息</a>");
            }else{
                $('#history_path').remove();
            }
        }
    });
}

function appendMessage(msg_data){

    var name = msg_data.name;
    var msg = msg_data.text;
    var upload_status = (msg_data.upload_status) ? msg_data.upload_status : "";
    var chat_id = msg_data.chat_id;
    var msg_token = msg_data.msg_token;

    $.ajax({
        url: "/chat/save",
        type: "POST",
        data:{
            name:name,
            msg_data:msg,
            chat_id:chat_id,
            msg_token:msg_token,
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
            //console.log(html);return false;

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

   // console.log(formData);
   //console.log(_token);
   // return false;

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

                var msg_data = {
                    name:user_name,
                    text:"傳送檔案過大"
                };
                appendMessage(msg_data);
            }
        },
        success: function(html){
            // console.log(html);
            // return false;

            image_height = html.image_height;

            if (html.extension == 'jpg' || html.extension == 'jpeg' || html.extension == 'png' || html.extension == 'gif') {
                var msg = "<a href="+html.file_name+" target=_blank><img src="+html.file_name+" title="+html.file_name+" width=200></a><BR><a href='/chat/dl_chat_pic"+html.file_name.replace('chat_upload_pic/','')+"' style=font-size:10pt;>下載</a> <a href='/chat/del_pic/"+chat_id+"/"+html.msg_token+"' style=font-size:10pt;>刪除</a> <span style=font-size:12pt;color:#369;>"+html.file_size+"</span> - <span style=font-size:10pt;color:#aaa;>"+getTodayDate()+"</span>";
            }else{
                var msg = "<div style='height:30px;'>已傳送 <b>"+html.originalname+"</b></div><span style=font-size:12pt;color:#369;>"+html.file_size+"</span> - <span style=font-size:10pt;color:#aaa;>"+getTodayDate()+"</span>";
            }


            var msg_data = {
                name:user_name,
                text:msg,
                upload_status:formData.upload_status,
                image_height:html.image_height
            };

            appendMessage(msg_data);
        }});
}

function getTodayDate_noformat() {
    var str = '';

    // 宣告日期物件
    var today = new Date();

    // 年
    str +=today.getFullYear();
    str +=(today.getMonth() < 10) ? '0'+(today.getMonth() + 1) : today.getMonth()+1;
    str +=(today.getDate() < 10) ? '0'+today.getDate() : today.getDate();
    str +=(today.getHours() < 10) ? '0'+today.getHours() : today.getHours();
    str +=(today.getMinutes() < 10) ? '0'+today.getMinutes() : today.getMinutes();
    str +=(today.getSeconds() < 10) ? '0'+today.getSeconds() : today.getSeconds();
    //str +=today.getMilliseconds();

    return str;
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

// ucs-2 string to base64 encoded ascii
function utoa(str) {
    return window.btoa(unescape(encodeURIComponent(str)));
}

// base64 encoded ascii to ucs-2 string
function atou(str) {
    return decodeURIComponent(escape(window.atob(str)));
}

function getHistoryMsg(chat_id, date, init_json_cnt){

    $.ajax({
        url: "/chat/history_msg",
        type: "POST",
        data:{
            chat_id:chat_id,
            date:date,
            init_json_cnt:init_json_cnt
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
            // console.log(html);
            // return false;

            var msg_content = '';

            if (typeof html.room_msg == 'object') {
                $.each(html.room_msg, function (k, sub_arr) {

                    $.each(sub_arr, function (sub_k, sub_v) {

                        if (typeof sub_v == 'string') {
                            msg_content += '<li>'+sub_v+'</li>';
                        }else {
                            msg_content += '<li><div>' + sub_k + ':</div>';
                            $.each(sub_v, function (sub_s_k, sub_s_v) {
                                msg_content += sub_s_v;
                            });
                            msg_content += '</li>';
                        }

                    });
                });

                $('#messages').prepend(msg_content);

                var message_block = parseInt($("#messages").height());
                var document_height = parseInt($(document).height());

                $("#message_block").scrollTop((message_block + document_height));
            }

            if (typeof html.history_path == 'string' && html.history_path != "") {
                $('#history_path').html("<a href=javascript:getHistoryMsg('"+chat_id+"','"+html.history_path+"','"+html.init_json_cnt+"');>歷史訊息</a>");
            }else{
                $('#history_path').remove();
            }
        }
    });
}
