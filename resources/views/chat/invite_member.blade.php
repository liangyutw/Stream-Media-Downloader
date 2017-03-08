@extends('layouts.chat')

@section('notice_count', $notice_count)

@section('content')

    <div class=container>

        @if(Session::has('error_msg'))
            <p id="error_msg" style="background-color: #FF0000;padding:15px;position: absolute;z-index: 99;width:60%;opacity: 0.8;color: #fff;font-size: 15px;">{!!Session::get('error_msg') !!}<button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></p>
        @endif

        <h3>邀請會員</h3>
        <table class="table table-bordered">
            <tr>
                <th>聊天室資訊</th>
            </tr>
            <tr>
                <td>聊天室名稱：{{$chat_room_all['name']}}<BR>
                    可邀請人數：{{($chat_room_all['limit']-1)}}<br></td>
            </tr>
        </table>


        {!! Form::open(['route' => 'chat_invite_member', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
            <button type="button" class="btn btn-primary btn-md" onclick="location.href='/chat/{{$chat_id}}'"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>回聊天室</button>
            <input type="submit" name="invite_member_form" value="提出邀請" class="btn btn-primary">

        <h4>可邀請會員</h4>

            <ul style="list-style: none;">

                @foreach ($user_list_result as $key => $row)
                    <li style="display: inline; padding: 7px;margin: 3px;border: 1px solid #ccc;line-height: 30px;"><label for="{{$row['id']}}"><input type="checkbox" name="member[]" id="{{$row['id']}}" value="{{$row['id']}}">{{$row['name']}}</label></li>
                @endforeach

            </ul>


            <input type="hidden" name="chat_id" value="{{$chat_id}}">

        {!! Form::close() !!}
    </div>

@endsection
