<style>
    p:hover {background-color: #eaeaea;}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<input type="hidden" id="csrf-token" name="csrf-token" value="{{csrf_token()}}">
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">YouTube 下載編輯器 - <span class="label label-danger" id="server_connection">尚未連線</span></div>
                <div class="panel-body">

                    <div class="form-group">
                        <label>請輸入 YouTube 影片網頁</label>
                        <input type="text" class="form-control" name="url" id="url" value="" placeholder="請輸入網頁連結，例如 https://www.youtube.com/watch?v=HNcwS4n5D2c" style="width:70%; font-size:12pt;"/>
                        <div>＊請參考可下載站台：<a href="https://rg3.github.io/youtube-dl/supportedsites.html" target="_blank">https://rg3.github.io/youtube-dl/supportedsites.html</a></div>
                    </div>
                    <button type="button" class="btn btn-primary" id="btn_download">下載影片</button>



                    <button type="button" class="btn btn-primary" id="video-list">影片列表</button>

                    <div style='display:none;'>
                        <div id='inline' title="編輯影片(可拖曳視窗)" style='padding:10px;background:#fff;'>

                            <label>控制按鈕</label>
                            <div><button type="button" class="btn btn-primary" id="btn_open_merge" value="0">合併(或重製)影片</button>
                                <button type="button" class="btn btn-primary" id="btn_open_delete_edit" value="0">開啟刪除/修改檔名</button></div>
                            <!-- 檔案列表 -->
                            <div id="s3_list" style="overflow-y: scroll;height:auto;max-height:300px;">
                                <label>檔案列表</label>
                                <div id="select_btn" style="display:none;"><button type="button" class="btn btn-primary" id="select_all">全選</button><button type="button" class="btn btn-primary" id="unselect_all">取消全選</button></div>
                                @if( isset($arr_list) && count($arr_list) > 0 )
                                    @for($i = 0; $i < count($arr_list); $i++)
                                        <?php
                                        $arr = explode('/', $arr_list[$i]);
                                        $value = $arr[count($arr)-1];
                                        $arr_ext = explode('.', $value);
                                        $ext = $arr_ext[count($arr_ext)-1];
                                        ?>
                                        <p id="single_file" style="cursor: pointer;">
                                            <input type="checkbox" class="mergeFile" data-path="{{ $value }}" style="display:none;"/>
                                            <input type="button" class="editFile" value="播放" data-path="{{ $value }}" style=" float: left; margin-right: 5px;" />
                                            <input type="button" class="deleteFile" value="刪除" data-path="{{ $value }}" style="display:none; float: left; margin-right: 5px;" />
                                            <input type="button" class="renameFile" value="更名" data-path="{{ $value }}" style="display:none; float: left; margin-right: 5px;" />
                                            <span><?php echo ' '.($i+1).' - ';?></span>
                                            <a class="preview" href="{{ asset('storage/'.$value) }}" target="_blank" title="{{ $value }}">{{ $value }}</a>
                                            <span style="font-size: 10pt;"><?php echo round(filesize(public_path().'/storage/'.$value)/1024/1024, 2);?>MB</span>
                                            <div style="clear: both;"></div>
                                        </p>
                                    @endfor
                                @else
                                    <p>尚無影片</p>
                                @endif

                            </div>
                            <div id="file_name_area"></div>
                            <hr />

                            <div class="form-group">
                                <label>影片播放/編輯區</label>
                                <h3>播放檔案：<span id="video_name" style="color:red;"></span></h3>
                                <div align="center" class="embed-responsive embed-responsive-16by9">
                                    <video id="player"  height="300" controls class="embed-responsive-item"></video>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                <div>切割起始時間：<input type="text" id="start_time" value="" /><input type="button" id="getStartTime" value="取得起始時間" /></div>
                                <div>切割結束時間：<input type="text" id="end_time" value="" /><input type="button" id="getEndTime" value="取得結束時間" /></div>
                                <button type="button" class="btn btn-primary" id="btn_split">切割影片</button>
                                <button type="button" class="btn btn-primary" id="btn_split_by_minute">設定影片切割數</button>
                                <button type="button" class="btn btn-primary" id="btn_merge">合併影片</button>
                                <button type="button" class="btn btn-primary" id="btn_rebuild">重製影片</button><BR>
                                <button type="button" class="btn btn-primary" id="btn_snapshot">擷取圖片（png）</button>
                                <button type="button" class="btn btn-primary" id="btn_mp3">擷取 MP3</button>
                                <input type="hidden" class="btn btn-primary" id="btn_duration" value="">
                            </div>

                        </div>
                    </div>


                    <hr />

                    <div class="form-group">
                        <div class="panel panel-primary">
                            <div class="panel-heading">處理狀態：<span id="process_end_msg">暫無事件處理</span><span>。處理完成後建議 <button type="button" class="btn btn-primary" onclick="location.reload();">頁面重整</button></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="panel panel-primary">
                            <div class="panel-heading"></div><button type="button" id="btn_show_process" onclick="$('#msg_board').toggle();">顯示/關閉 伺服器回傳訊息</button>
                            <div class="panel-body pre-scrollable" id="msg_board" style="overflow-y: scroll;height:500px;max-height:500px; display: none;"></div>
                        </div>
                    </div>


                    <div style='display:none;'>
                        <div id='split_area' title="設定每一切割影片的分鐘數(可拖曳視窗)" style='padding:10px;background:#fff;'>
                            每一影片切：<input type="text" id="split_set_number" value="" size="2">分鐘<BR>
                            <input type="button" id="split_set_ok" value="確定" />
                        </div>
                    </div>

                    <div id="concat_string"></div>


                </div>

            </div>
        </div>
    </div>
</div>


<!-- socket.io CDN -->
<script src="/js/socket.io-1.4.5.js"></script>
<script src="/js/jquery-1.12.4.min.js"></script>
<script src="/js/jquery-1.12.1-ui.js"></script>
<script src="/js/function.js"></script>

<script>

    var dialog;
    dialog = $( "#inline" ).dialog({
        autoOpen: false,
        height: "auto",
        width: "auto",
        modal: true,
        buttons: {
            Close: function() {
                dialog.dialog( "close" );
            }
        }
    });

    split_set_dialog = $( "#split_area" ).dialog({
        autoOpen: false,
        height: "auto",
        width: "auto",
        modal: true,
        buttons: {
            Close: function() {
                split_set_dialog.dialog( "close" );
            }
        }
    });
</script>

<script src="/js/execute.js"></script>

<!-- 自訂 js -->
<script>

$(function(){

    //預設下載、合併按鈕不可點
    $("#btn_download, #btn_merge, #btn_rebuild, #btn_split_by_minute").attr('disabled', true);

    //開啟設定分鐘數的 dialog
    $( "#btn_split_by_minute" ).button().on( "click", function() {
        split_set_dialog.dialog( "open" );
    });

    //偵測有勾選才開啟合併影片按鈕
//    $("input[type=checkbox]").on('click', function() {
//        if ($(this).prop("checked") == true) {
//            $("#btn_merge, #btn_rebuild").attr("disabled", false);
//        }else{
//            $("#btn_merge, #btn_rebuild").attr("disabled", true);
//        }
//    });

    //切換 checkbox、button
    $("#btn_open_merge").on('click', function(){
        var open_merge = this;
        if ($(open_merge).val() == 1) {
            $(open_merge).val(0).html('合併(或重製)影片');

            $("#select_btn").css('display', 'none');
        }else {

            $(open_merge).val(1).html('編輯影片');
            $("#select_btn").css('display', 'block');
        }
        $("input[type=checkbox], .deleteFile, .editFile, .renameFile").toggle();
        $(".deleteFile, .renameFile").css('display', 'none');
    });

    //全選
    $("#select_all").on('click', function(){
        $("input[type=checkbox]").prop({checked: true});
        $.each($("input[type=checkbox]"), function(k, v){
            if ($(v).prop("checked") == true) {
                $("#btn_merge, #btn_rebuild").prop({disabled: false});
            }
        })
    });

    //全不選
    $("#unselect_all").on('click', function(){
        $("input[type=checkbox]").prop({checked: false});
        var unchecked_arr = [], checkbox_cnt = 0;
        $.each($("input[type=checkbox]"), function(k, v){
            checkbox_cnt++;
            if ($(v).prop("checked") == false) {
                unchecked_arr[k] = 0;
            }
        });

        if (checkbox_cnt == unchecked_arr.length) {
            $("#btn_merge, #btn_rebuild").prop({disabled: true});
        }
    });


    //點修改檔名效果
    $(document).on('click', '.renameFile', function() {
        $("#file_name_area").html('');

        var old_name = $(this).attr('data-path');
        var input_content = '輸入新檔名：<input type="text" id="new_name" value="'+old_name+'" size="30" /><input type="button" id="upd_new_name" value="確定" /><input type="hidden" id="old_name" value="" />';

        $("#file_name_area").append(input_content).css({"padding":"15px","background-color": "#ffb1a6"});
        $("#old_name").val($("#new_name").val());

        $(".cancel_rename").val('更名').attr("class", "renameFile").removeAttr("style").parent('p').removeAttr("style");

        $("#file_name_area").append('<input type="button" class="cancel_rename" value="取消" />');

        if ($("input#new_name").val() == old_name) {
            $(this).val('取消').attr('class', 'cancel_rename').css({"background-color": "#ff0000", "color": "#ffffff"});
            $(this).parent('p').css({"padding": "5px 0px 5px 0px", "background-color": "#ffb1a6"});
        }
    });

    //點編輯按鈕時的效果
    $(document).on('click', '.editFile', function() {
        $('p').removeAttr('style');
        if ($(this).attr('data-path') == $("#video_name").html()) {
            $(this).parent('p').css({"padding": "5px 0px 5px 0px", "background-color": "#ffb1a6"});
        }
    });

    //點檔名空白處效果
    $(document).on('click', '#single_file', function() {
        $('p').css({"padding": "", "background-color": "", "cursor": "pointer"});
        if ($(".editFile").attr('data-path') == $(".preview").html()) {
            $(this).css({"padding": "5px 0px 5px 0px", "background-color": "#ffb1a6", "cursor": "pointer"});
        }
    });


    //取消修改檔名
    $(document).on('click', '.cancel_rename', function() {
        $("#file_name_area").html('').removeAttr("style");

        $(".cancel_rename").val('更名').attr("class", "renameFile").removeAttr("style").parent('p').removeAttr("style");

        $(this).val('更名').attr('class', 'renameFile').css({"background-color": "", "color": "#000000"});
        $(this).parent('p').css({"padding": "0px", "background-color": ""});
    });

    //修改檔名
    $(document).on('click', '#upd_new_name', function() {
        var old_name = $("input#old_name").val();
        var new_name = $("#new_name").val();
        var _token = $('div#csrf-token').html();

        $.ajax({
            method: 'get',
            url: '/youtube/updFileName',
            data: {
//                _token:_token,
                old_name:old_name,
                new_name:new_name
            },
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
            //alert(html);
            if (parseInt(html) != 0) {
                alert('更新檔名成功');
                location.reload();
            }
            else {
                alert('更新檔名失敗…');
            }
        }).fail(function(e){
            console.log(e);
        });
    });

    //刪除、修改按鈕控制
    $("#btn_open_delete_edit").on('click', function(){
        var open_delete = this;
        if ($(open_delete).val() == 1) {
            $(open_delete).val(0).html('開啟刪除/修改檔名');

            $(".deleteFile, .renameFile").css('display', 'none');
        }else {
            $(open_delete).val(1).html('關閉刪除/修改檔名');
            $(".deleteFile, .renameFile").css('display', 'block');
        }
    });




    //偵測 url 是否有貼上內容
    $('input#url').bind('paste', function () {
        var element = this;
        setTimeout(function() {
            var text = $(element).val();
            if (text != '') {
                count_to_open_btn();
            }
        }, 100);
    });

    //偵測 url 是否為空
    $('input#url').bind('keyup', function () {
        var element = this;
        setTimeout(function() {
            var text = $(element).val();
            if (text == '') {
                $("#btn_download").attr('disabled', true);
            }
        }, 100);

    });



    $( "#video-list" ).button().on( "click", function() {
        dialog.dialog( "open" );
    });

    //頁面偵測是否有影片可切割擷圖
    if ($('video#player > source').attr('src') == undefined) {
        $('#getStartTime, #getEndTime, #btn_split, #btn_snapshot, #btn_mp3').attr('disabled', true);
        return false;
    }else{
        $('#getStartTime, #getEndTime, #btn_split, #btn_snapshot, #btn_mp3').attr('disabled', false);
    }






});

</script>