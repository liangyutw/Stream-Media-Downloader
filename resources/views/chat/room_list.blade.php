@extends('layouts.chat')

@section('notice_count', isset($notice_count) ? $notice_count : "")

@section('content')

    <div class=container>

        @if(Session::has('nologin_msg'))
            <p id="error_msg" style="background-color: #FF0000;padding:15px;position: absolute;z-index: 99;width:60%;opacity: 0.8;color: #fff;font-size: 15px;">{{Session::get('nologin_msg')}}<button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></p>
        @else
            @if(Session::has('error_msg'))
                <p id="error_msg" style="background-color: #FF0000;padding:15px;position: absolute;z-index: 99;width:60%;opacity: 0.8;color: #fff;font-size: 15px;">{{Session::get('error_msg')}}<button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></p>
            @endif
            <h3>聊天室列表</h3>
            <ul>
                @if (isset($chat_list_result) and count($chat_list_result) > 0)

                    @foreach ($chat_list_result as $key => $row)
                        <li style="list-style: none; border:1px solid #ccc; margin:5px;padding:5px;">
                        <div style="min-width:200px; width:auto; font-size: 16px; font-weight: bold;padding:8px 0px 5px 3px;"><a href="{{$row['chat_room_id']}}">{{$row['name']}}</a></div>
                        <div style="width:60%;float: left;">描述：{{$row['description']}}<BR>
                                    人數限制：{{$row['limit']}}<BR>
                                    建立者：{{$row['create_user_name']}}<BR>
                                    @if (isset($del_status_arr[$row['chat_room_id']]))<a href="javascript:del_chat_room({{$row['chat_room_id']}});" >刪除</a>@endif
                        </div>
                        <div style="width:100px;float: left; line-height:60px;">
                            <button type="button" class="btn btn-info">進入聊天室</button>
                        </div>
                        <div style="clear: both;"></div>
                        </li>
                    @endforeach

                @else
                    <li>無任何聊天室</li>
                @endif
            </ul>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function del_chat_room(id) {
            if (confirm('確定要刪除聊天室?')) {
                console.log('/chat/del_chat_room/'+id);
                location.href='/chat/del_chat_room/'+id;
            }
        }
        </script>
@endsection