@extends('layouts.chat')

@section('notice_count', $notice_count)

@section('title_word', $room_name.' 聊天室')

@section('content')
<div class=container>
    @if(Session::has('error_msg'))
        <p id="error_msg" style="background-color: #FF0000;padding:15px;position: absolute;z-index: 99;width:60%;opacity: 0.8;color: #fff;font-size: 15px;">{{Session::get('error_msg')}}<button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></p>
    @endif
    <ul>
        <h3>你正在{{$room_name}}聊天室</h3>
        <button type="button" class="btn btn-primary btn-md" onclick="location.href='/chat/list'"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>回上頁</button>

        <button type="button" class="btn btn-primary btn-md"  onclick="location.href='{{route('chat_invite_member', $chat_id)}}'">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>邀請會員
        </button>

        <button type="button" class="btn btn-primary btn-md" id="member_list">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>聊天室會員<span class="badge">{{count($chat_member_list)}}</span>
        </button>

        <input type="hidden" id="csrf-token" name="csrf-token" value="{{csrf_token()}}">
        <span id="text" style="visibility: hidden"></span>
        <div id="message_block">
            <div id="history_path" style='width:65px;padding:5px;margin:0 auto;'></div>
            <ul id="messages"></ul>
        </div>
        <div class="input_area">
            <textarea id='m' style='width:89%;' rows="1" class=form-control autocomplete="off" placeholder="輸入訊息"></textarea><button id="send" class="btn btn-success">送出</button>
        </div>
    </ul>

    <div style='display:none;'>
        <div id='inline' title="聊天室會員列表" style='padding:10px;background:#fff;'>
            @if (is_array($chat_member_list))
                <ul>
                @foreach($chat_member_list as $key => $user_info)
                    <li>{{$user_info['user_name']}}</li>
                @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection




@section('scripts')
<script>
    var _token = $("#csrf-token").val(),
        user_id = '<?php echo $user_id;?>',
        user_name = '<?php echo $user_name;?>',
        chat_id = '<?php echo $chat_id;?>';

    var dialog;
    dialog = $( "#inline" ).dialog({
        autoOpen: false,
        minHeight: "400px",
        minWidth: "500px",
        modal: true,
        buttons: {
            Close: function() {
                dialog.dialog( "close" );
            }
        }
    });

    $( "#member_list" ).button().on( "click", function() {
        dialog.dialog( "open" );
    });

</script>
<script src="/js/chat_func.js"></script>
<script src="/js/chat_opera.js"></script>
@endsection

