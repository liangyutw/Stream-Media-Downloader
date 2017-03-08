<!DOCTYPE html>

<html lang=en>
<head>
    <meta charset=utf-8>
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name=description content="">
    <meta name=author content="">

    <title>@yield('title_word')</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href=https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css rel=stylesheet>
    <link href=/css/chat_sticky_footer_styles.css rel=stylesheet>
    <link rel="stylesheet" type="text/css" href="/css/chat.css">

    <!--[if lt IE 9]>
    <script src=https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js>
    </script>
    <script src=https://oss.maxcdn.com/respond/1.4.2/respond.min.js>
    </script>
    <![endif]-->
<body>
<nav class="navbar navbar-default navbar-fixed-top" role=navigation>
    <div class=container>
        <div class=navbar-header>
            <button type=button class="navbar-toggle collapsed" data-toggle=collapse data-target=#navbar aria-expanded=false aria-controls=navbar>
                <span class=sr-only>Toggle navigation
                </span>
                <span class=icon-bar>

                </span>
                <span class=icon-bar>

                </span>
                <span class=icon-bar>

                </span>
            </button>
            <a class=navbar-brand>聊天室系統</a>
        </div>
        <div id=navbar class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href={{route('chat_room_list')}}>回聊天室列表</a>
                <li>
                    <a href={{route('chat_room_add')}}>新增聊天室</a>
                <li>
                    <a href={{route('chat_invite_list')}}>邀請列表<span class="badge">@yield('notice_count', '')</span></a>
                {{--<li class=dropdown>
                    <a href=# class=dropdown-toggle data-toggle=dropdown role=button aria-expanded=false>Dropdown
                        <span class=caret></span></a>
                    <ul class=dropdown-menu role=menu>
                        <li>
                            <a href=#>Action</a>
                        <li>
                            <a href=#>Another action</a>
                        <li>
                            <a href=#>Something else here</a>
                        <li class=divider>
                        <li class=dropdown-header>Nav header
                        <li>
                            <a href=#>Separated link</a>
                        <li>
                            <a href=#>One more separated link</a>
                    </ul>--}}

                    @if (session::has('user_info'))
                        <li><a href="{{route("chat_logout")}}">
                                @if (isset(session::get('user_info')['email']))
                                    {{session::get('user_info')['email']}} 登出
                                @endif</a></li>
                    @else
                        <li><a href="{{route("chat_login")}}">登入</a></li>
                    @endif

            </ul>
        </div>
    </div>
</nav>

@yield('content')


</body>
</html>

<script src="/js/socket.io-1.4.5.js"></script>
<script src="/js/jquery-1.12.4.min.js"></script>
<script src="/js/jquery-1.12.1-ui.js"></script>
<script>
    $(function(){
        $("button.close").click(function(){
            $("#error_msg").remove();
        });
    });
</script>

@yield('scripts')

