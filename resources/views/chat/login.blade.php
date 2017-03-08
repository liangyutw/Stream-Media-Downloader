
@extends('layouts.chat')

@section('content')
<div class=container>
    <h2 class=form-signin-heading>聊天室登入帳號</h2>
    {!! Form::open(['route' => 'chat_login', 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form']) !!}


        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">信箱帳號</label>
            <div class="col-sm-5">
                <input type="email" class="form-control" name="email" placeholder="信箱帳號">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">密碼</label>
            <div class="col-sm-5">
                <input type="password" class="form-control" name="password" placeholder="密碼">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">登入</button>
            </div>
        </div>


    {!! Form::close() !!}
</div>
@endsection
