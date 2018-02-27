@extends('layout.master')

<?php

    $layout = [
        'title' => 'ログイン',
    ];

?>

@section('content')

<h2 class="text-center">ログイン</h2>
<div class="col-xs-4 col-xs-offset-4">
  <div class="panel panel-default">
    <form method="post" action="{{ route('auth.signin') }}">
    {{ csrf_field() }}
      <div class="panel-body">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
          <label for="email">E-Mail</label>
          <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="info@example.com">
          @if ($errors->has('email'))
          <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
          @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="password">パスワード</label>
          <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control" placeholder="password">
          @if ($errors->has('password'))
          <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
          @endif
        </div>
        <input name="remember" value="1" type="hidden">
      </div>
      <div class="panel-footer clearfix">
        <div class="pull-right">
          <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> ログイン</button>
        </div>
      </div>
      <!-- / .panel-footer -->
    </form>
  </div>
</div>

@endsection
