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
            <h3>邀請列表</h3>
                <table class="table table-bordered">
                    @if (isset($invite_list_data) and count($invite_list_data) > 0)
                        @foreach ($invite_list_data as $key => $row)
                            <tr>
                                <th>標題</th>
                                <th>介紹內容</th>
                                <th>詳細資訊</th>
                            </tr>
                            <tr>
                                <td>{{$row['subject']}}</td>
                                <td>{{$row['content']}}</td>
                                <td>聊天室名稱：{{$row['chat_room_name']}}<br>建立人：{{$row['create_user_id']}}<br>邀請人：{{$row['from']}}<br>邀請時間：{{$row['set_time']}}<br>
                                    {!! Form::open(['route' => 'chat_invite_judge', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                                    @if (isset($return_reject) and $return_reject == 1)
                                        <input type="button" name="show_msg" class="btn btn-primary" value="你已拒絕此聊天室邀請">
                                    @else
                                        <input type="submit" name="accept" class="btn btn-primary" value="同意"> <input type="submit" name="reject" class="btn btn-danger" value="拒絕">
                                        <input type="hidden" name="notice_id" class="btn btn-primary" value="{{$row['id']}}">
                                        <input type="hidden" name="chat_room" class="btn btn-primary" value="{{$row['chat_room']}}">
                                    @endif
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>無任何邀請</tr>
                    @endif
                </table>
        @endif
    </div>
@endsection
