@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
<p></p>

@endsection

@section('scripts')
<script>
    Notification.TOKEN = '{{ $token or null }}';
</script>
<script src="/js/socket.io-1.4.5.js"></script>
<script src="/js/jquery-1.12.4.min.js"></script>
<script src="/js/app.js"></script>
@endsection