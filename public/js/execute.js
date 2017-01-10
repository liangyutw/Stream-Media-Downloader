var video_name = ''; //給切割命名用，在編輯鈕按下的那一刻來決定
var path_arr = {}; //合併檔使用
var video_split_num = 1;
var del_ts_cnt = 0;
var cnt = 0;
var concat_name = '';

$(document).ready(function(){

    //透過 web server 的 3000 port 來進行連線
    socket = io.connect('http://192.168.136.128:3000/');

    //建立連結
    socket.on('connect', function() {
        $('#server_connection').removeClass("label-danger").addClass('label-success').text('已連線');
        $('div#msg_board').append('<div style="font-family: Courier New">您已與伺服器連線（Node.js Socket.io）</div>');
    });

    //連線錯誤（可能 server 端有狀況）
    socket.on('connect_error',function(){
        $('#server_connection').removeClass("label-success").addClass('label-danger').text('尚未連線');
        $('div#msg_board').append('<div style="font-family: Courier New">您已與伺服器斷線...........</div>');
    });

    //傳送 url 連結到伺服器
    $(document).on('click', '#btn_download', function() {
        if( $('#url').val() == "" )
        {
            alert('請輸入網址，謝謝');
            return false;
        }

        socket.emit('url', $('#url').val());
        $('#url').val('');
        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });

    //接收從 node.js 程式回傳的結果
    socket.on('server_msg', function(data) {
        //var data = new TextDecoder("utf-8").decode(data);
        //alert( data );
        $('div#msg_board').append('<div style="font-family: Courier New;padding:3px 0px 5px 0px;border-bottom:1px solid;">' + data + '</div>');

        //div 滾輪往下滾
        $('div#msg_board').scrollTop( $('div#msg_board')[0].scrollHeight );
    });

    //當指令執行完畢後，會丟一個確認訊息 (exited code) 過來
    socket.on('server_cmd_finished', function(code) {

        if (typeof(code) == 'object') {
            var del_file = code.file_path.replace('/storage/','');
            var code = parseInt(code.code);
        }else{
            var code = parseInt(code);
        }

        switch( code )
        {
            case 0:
                if (confirm('處理完畢!\n是否重整?')){
                    location.reload();
                }
                //getS3Files();
                break;

            case 1:
                alert('Node.js 程式執行出錯與終止，請至伺服器查看');
            case 2:
                cnt+=1;
                //合併影片

                if (Object.keys(path_arr).length == cnt) {
                    $.each(path_arr, function(k, v) {
                        concat_name += "/usr/share/nginx/html/laravel/public"+v.replace('mp4', 'ts')+'|';
                    });
                    //$("#concat_string").html(concat_name.replace(/\|+$/g, ''));
                    socket.emit('concat_video', concat_name.replace(/\|+$/g, ''));
                }
                break;
            case 3:

                //刪除轉換用的 TS 檔
                del_ts_cnt++;
                var _token = $('div#csrf-token').html();
                $.ajax({
                    method: 'get',
                    url: '/youtube/deleteTsFile/'+del_ts_cnt,
                    data: {},
                    statusCode: {
                        404: function () {
                            alert('找不到頁面');
                        },
                        500: function () {
                            alert('內部伺服器錯誤');
                        }
                    },
                    headers: {'X-CSRF-TOKEN': _token}
                }).done(function (html) {
                    //console.log(html);
                    if (parseInt(html) == 1) {
                        if (confirm('合併完成!\n是否重整?')){
                            location.reload();
                        }
                    }
                    else if (parseInt(html) == -1) {
                        alert('合併失敗…');
                    }
                });
                break;
            case 4:
                var _token = $('div#csrf-token').html();

                $.ajax({
                    method: 'get',
                    url: '/youtube/deleteRebuildFile/'+del_file,
                    data: {},
                    statusCode: {
                        404: function () {
                            alert('找不到頁面');
                        },
                        500: function () {
                            alert('內部伺服器錯誤');
                        }
                    },
                    headers: {'X-CSRF-TOKEN': _token}
                }).done(function (html) {
                    // console.log(html);
                    if (parseInt(html) == 1) {
                        if (confirm('重製完成!\n是否重整?')){
                            location.reload();
                        }
                    }
                    else if (parseInt(html) == -1) {
                        alert('重製失敗…');
                    }
                });


                break;

        }
        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });

    //取得 video player
    var player = document.getElementById('player');

    //播放軸的目前選擇時間
    player.addEventListener("seeked", function(){
        $('#current_time').val( toHHMMSS(player.currentTime) );
    });

    //編輯按鈕，開啟編輯模式 (會在播放器裡面加入 source 元素)
    $(document).on('click', '.editFile', function(){

        var vp = $('video#player');
        vp.find('source').remove();
        video_name = $(this).attr('data-path');
        var path = $(this).attr('data-path');
        var ext = path;
        ext = ext.split('.');
        ext = ext[ext.length-1];

        $("#video_name").html(video_name);


        //加入 source 元素
        vp.append('<source src="/storage/' + path + '" type="video/' + ext + '">');

        //重新讀取 video 的 source 元素
        player.load();

//            if ($('video#player > source').attr('src') == undefined) {
//                $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
//                return false;
//            }else{
        $('#getStartTime, #getEndTime, #btn_split, #btn_snapshot').attr('disabled', false);
//            }
    });

    //取得切割開始時間
    $(document).on('click', '#getStartTime', function(){
        $('#start_time').val( toHHMMSS(player.currentTime) );
    });

    //取得切割結束時間
    $(document).on('click', '#getEndTime', function(){
        $('#end_time').val( toHHMMSS(player.currentTime) );
    });

    //切割影片
    $(document).on('click', '#btn_split', function(){
        video_split_num++;
        //確認欲處理的時間是否有正確配置
        if( checkTime() != true ) return false;

        if ( $('#start_time').val() == "" || $('#end_time').val() == "" )
        {
            alert('請先確實選擇欲切割之起始與結束時間，謝謝。');
            return false;
        }

        var obj = {};
        obj['start_time'] = $('#start_time').val();
        obj['end_time'] = $('#end_time').val();
        obj['file_path'] = $('video#player > source').attr('src');
        obj['video_name'] = video_name.replace('.mp4', '_'+video_split_num+'.mp4');

        socket.emit('split_video', obj);

        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });



    //合併影片
    $(document).on('click', '#btn_merge', function(){
        var obj = {};
        //檢查有打勾的影片
        $("input[type=checkbox]").each(function(k, v) {
            //console.log($(v).prop("checked"));
            if ($(v).prop("checked") == true) {
                path_arr[$(this).attr("data-path").replace('.mp4', '')] = "/storage/"+$(this).attr("data-path");
            }
        });

        //走迴圈轉成 .ts 檔案進行合併
        $.each(path_arr, function(k, v) {
            obj['file_path'] = v;
            obj['video_name'] = k;
            //console.log(obj);
            socket.emit('trans_video', obj);
        });

        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });

    //轉換格式影片
    $(document).on('click', '#btn_rebuild', function(){
        $("#btn_download, #btn_merge, #btn_rebuild").attr('disabled', true);

        var obj = {};
        //檢查有打勾的影片
        $("input[type=checkbox]").each(function(k, v) {

            //console.log($(v).prop("checked"));
            if ($(v).prop("checked") == true) {
                path_arr[$(this).attr("data-path").replace('.mp4', '')] = "/storage/"+$(this).attr("data-path");
            }
        });

        //走迴圈轉成 .ts 檔案進行合併
        $.each(path_arr, function(k, v) {
            obj['file_path'] = v;
            obj['video_name'] = k;
            //console.log(obj);
            socket.emit('trans_format_video', obj);
        });

        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });


    //擷圖
    $(document).on('click', '#btn_snapshot', function(){
        //確認欲處理的時間是否有正確配置
        if( checkTime() != true ) return false;

        var obj = {};
        obj['current_time'] = player.currentTime;
        obj['file_path'] = $('video#player > source').attr('src');
        obj['video_name'] = video_name;
        socket.emit('snapshot', obj);

        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });

    //轉成 mp3
    $(document).on('click', '#btn_mp3', function(){
        //確認欲處理的時間是否有正確配置
        if( checkTime() != true ) return false;

        var obj = {};
        obj['start_time'] = $('#start_time').val();
        obj['end_time'] = $('#end_time').val();
        obj['file_path'] = $('video#player > source').attr('src');
        obj['video_name'] = video_name;
        socket.emit('mp3', obj);

        $('#getStartTime, #getEndTime, #btn_split, #btn_download, #btn_snapshot').attr('disabled', true);
    });

    //刪除檔案
    $(document).on('click', 'input.deleteFile', function(){
        var btn = $(this);
        var path = btn.attr('data-path');
        var _token = $('#csrf-token').val();

        if( confirm('確定要刪除檔案?') ) {
            $.ajax({
                method: 'get',
                url: '/youtube/deleteFile/' + path,
                data: {},
                //dataType: 'html',
                //timeout:{},
                statusCode: {
                    404: function () {
                        alert('找不到頁面');
                    },
                    500: function () {
                        alert('內部伺服器錯誤');
                    }
                },
//                    beforeSend: function(){},
                headers: {'X-CSRF-TOKEN': _token}
            }).done(function (html) {
                //alert(html);
                if (parseInt(html) >= 1) {
                    alert('刪除檔案成功');
                    //location.reload();
                    //刪除按鈕元素即連結元素
                    btn.parent('p').remove();
                }
                else {
                    alert('刪除失敗…');
                }
            }).fail(function (e) {
                console.log(e);
                alert('傳遞失敗。請稍候再試，或是與程式設計人員聯絡，謝謝。' + '\n\n' + e.responseText);
            }).always(function () {});
        }
    });

});