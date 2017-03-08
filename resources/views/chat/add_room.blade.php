@extends('layouts.chat')

@section('notice_count', isset($notice_count) ? $notice_count: "")

@section('content')
<div class=container>
    @if(Session::has('nologin_msg'))
        <p id="error_msg" style="background-color: #FF0000;padding:15px;position: absolute;z-index: 99;width:60%;opacity: 0.8;color: #fff;font-size: 15px;">{{Session::get('nologin_msg')}}<button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></p>
    @else
        @if(Session::has('error_msg'))
            <p id="error_msg" style="background-color: #FF0000;padding:15px;position: absolute;z-index: 99;width:60%;opacity: 0.8;color: #fff;font-size: 15px;">{{Session::get('error_msg')}}<button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></p>
        @endif
            <h3>新增聊天室</h3>
            {!! Form::open(['route' => 'chat_room_add', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
            <div class="col-md-6 col-md-offset-3">
                <h2 class=form-signin-heading>建立聊天室資訊</h2>
                <input type=text name="name" class=form-control placeholder="聊天室名稱(文字格式)" required autofocus>
                <textarea name="description" class=form-control placeholder="聊天室描述(文字格式)"></textarea>
                <input type=text name="limit" class=form-control placeholder=限制人數(數字格式) required>

                <label>開啟狀態：</label>
                <input type=radio name="status" value="0" placeholder=關 > 關 <input type=radio name="status" value="1" placeholder=開 checked> 開

                <button class="btn btn-lg btn-primary btn-block" type=submit>建立</button>
                <button class="btn btn-lg btn-primary btn-block" type=button onclick="location.href='/chat/list'">取消</button>
            </div>

            {!! Form::close() !!}
    @endif
</div>
@endsection

