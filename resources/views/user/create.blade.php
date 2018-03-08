@extends('layout.master')

<?php

    $layout = [
        'title' => '新規会員登録',
    ];

?>

@section('content')

      <h2 class="text-center">新規登録</h2>
      <div class="col-xs-4 col-xs-offset-4">
        <div class="panel panel-default">
          {!! Form::open(['route' => 'user.store', 'method' => 'post']) !!}
            <div class="panel-body">
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email2">E-Mail</label>
                <input name="email" id="email2" value="{{ Request::old('email') }}" class="form-control" placeholder="info@example.com" type="email">
                @if ($errors->has('email'))
                <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                @endif
              </div>
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password">パスワード</label>
                <input name="password" id="password" value="" class="form-control" placeholder="password" type="password">
                @if ($errors->has('password'))
                <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                @endif
              </div>
              <input name="remember" value="1" type="hidden">
              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">お名前</label>
                <input name="name" id="name" value="{{ Request::old('name') }}" class="form-control" placeholder="名前" type="text">
                @if ($errors->has('name'))
                <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
              </div>
            </div>
            <div class="panel-footer clearfix">
              <div class="pull-right">
                <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> 登録</button>
              </div>
            </div>
            <!-- / .panel-footer -->
          {!! Form::close() !!}
        </div>
      </div>

@endsection
