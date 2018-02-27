@extends('layout.master')

<?php

    $layout = [
        'title' => '編集 | システム設定',
    ];

?>

@section('content')

      <h2>システム設定編集</h2>
      {{ Form::model($system, ['route' => ['system.update', 1] , 'method' => 'put', 'class' => 'form-horizontal']) }}
      <div class="panel panel-default">
        <div class="panel-body clearfix">
          @include('system._form', ['system' => $system])
        </div>
        <!-- / .panel-body -->
        <div class="panel-footer text-right">
          <a class="btn btn-default" href="{{ route('system.index') }}">戻る</a>
          <button type="submit" class="btn btn-primary"><span>更新</span></button>
        </div>
        <!-- / .panel-footer -->
        {!! Form::close() !!}
      </div>
      <!-- / .panel panel-default -->

@endsection
