@extends('layouts.app')

<?php
//echo "<pre>";print_r($send_data);exit;
?>
@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">boardcast area</div>

                <div class="panel-body">
                {!! Form::open(['route' => 'boardcast_create', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    <input name="user_id" type="text" value="{{$send_data['user_id'] or ""}}" placeholder="輸入會員 id"><BR>
                    <textarea name="content" cols="100" rows="10" placeholder="輸入顯示內容">{{$send_data['content'] or ""}}</textarea><BR>
                    <input name="go_boardcast" type="submit" value="送出">
                {!! Form::close() !!}
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">register area</div>

                <div class="panel-body">
                    {!! Form::open(['route' => 'register_store', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    <input name="register_name" type="text" value="" placeholder="輸入註冊名稱"><BR>
                    <input name="register_email" type="text" value="" placeholder="輸入註冊信箱"><BR>
                    <input name="go_boardcast" type="submit" value="送出">
                    <div id="return_msg"></div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<p></p>

@endsection

@section('scripts')
    <script src="/js/socket.io-1.4.5.js"></script>
    <script src="/js/jquery-1.12.4.min.js"></script>
    <script src="/js/register_return.js"></script>
@endsection