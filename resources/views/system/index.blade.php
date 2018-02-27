@extends('layout.master')

<?php

    $layout = [
        'title' => 'システム設定',
    ];

?>

@section('content')

      <h2>システム設定</h2>
      <div class="panel panel-default">
        <table class="table table-bordered">
          <colgroup>
            <col class="col-xs-2">
            <col class="col-xs-8">
          </colgroup>
          <thead>
            <tr>
              <th></th>
              <th>値</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>集中病棟</th>
              <td>{{ $system->intensive_ward }}</td>
            </tr>
            <tr>
              <th>産科病棟</th>
              <td>{{ $system->obstetrics_ward }}</td>
            </tr>
            <tr>
              <th>子供の手術名</th>
              <td>{{ $system->child_operation_name }}</td>
            </tr>
          </tbody>
        </table>
        <div class="panel-footer">
          <div class="text-right"> <a class="btn btn-primary" href="{{ route('system.edit', ['system' => 1 ]) }}">編集</a></div>
        </div>
      </div>

@endsection
