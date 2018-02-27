@extends('layout.master')

<?php

    $layout = [
        'title' => '編集 | A項目',
    ];

?>

@section('content')

      <h2>マスタ修正</h2>
      <div class="panel panel-default">

        {{ Form::model($item, ['route' => ['a_items.update', $item->id] , 'method' => 'put', 'class' => 'form-horizontal document-form']) }}
          <div class="panel-body clearfix">
            @include('master.a_item._form', ['item' => $item])
          </div>
          <!-- / .panel-body -->
          <div class="panel-footer text-right">
            <a href="{{ route('a_items.index') }}" class="btn btn-default">戻る</a>
            <button type="submit" class="btn btn-primary"><span>保存</span></button>
          </div>
          <!-- / .panel-footer -->
        {!! Form::close() !!}

      </div>
      <!-- / .panel panel-default -->

@endsection
