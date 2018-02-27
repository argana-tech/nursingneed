@extends('layout.master')

<?php

    $layout = [
        'title' => '産科項目マスタ',
    ];

?>

@section('content')

<h2>マスタ管理</h2>
<ul class="tabBtn">
  <li><a href="{{ route('a_items.index') }}">A項目</a></li>
  <li><a href="{{ route('c_items.index') }}">C項目</a></li>
  <li class="current"><a href="{{ route('obstetrics_items.index') }}">産科項目</a></li>
</ul>
<div class="tabContents m_u40">
  <div class="content">


<div class="panel panel-default">
  <div class="panel-heading">産科項目マスターファイルのアップロード</div>
  <div class="panel-body">
    <div class="col-sm-20 double-margin-bottom">
      <p class="text-secondary"> 文字コードはUTF-8です。ヘッダ行はありませんので、1行目からデータになるようにしてください。改行コードは「lf」です。</p>
      {{Form::open(['route' => 'obstetrics_items.upload', 'files' => true, 'class' => 'form-inline'])}}

        <div class="form-group pull-left{{ $errors->has('tsv_file') ? ' has-error' : '' }}">
          <label class="control-label" for="tsv_file">TSVファイル</label>
          <input name="tsv_file" id="tsv_file" type="file" style="display: inline-block;">
          @if ($errors->has('tsv_file'))
          <span class="help-block"><strong>{{ $errors->first('tsv_file') }}</strong></span>
          @endif
        </div>
        <div class="pull-right">
          <input name="commit" value="産科項目を更新" class="btn btn-primary" data-disable-with="産科項目を更新" type="submit">
        </div>

      {!! Form::close() !!}
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">データのダウンロード</div>
  <div class="panel-body">
    <p class="text-secondary"> 文字コードはUTF-8です。</p>
    <div class="pull-right"><a class="btn btn-primary" href="{{ route('obstetrics_items.download') }}">ダウンロード</a></div>
  </div>
</div>

<table class="table table-bordered">
  <colgroup>
    <col class="col-xs-2">
    <col class="col-xs-1">
    <col class="col-xs-1">
    <col class="col-xs-1">
  </colgroup>
  <thead>
    <tr>
      <th>名称</th>
      <th>コード</th>
      <th>Kコード</th>
      <th>備考</th>
    </tr>
  </thead>
  <tbody>
  @foreach($items as $item)
    <tr>
      <td><a href="{{ route('obstetrics_items.edit', ['aitem' => $item->id ]) }}" class="">{{ $item->name }}</a></td>
      <td><a href="{{ route('obstetrics_items.edit', ['aitem' => $item->id ]) }}" class="">{{ $item->code }}</a></td>
      <td><a href="{{ route('obstetrics_items.edit', ['aitem' => $item->id ]) }}" class="">{{ $item->kcode }}</a></td>
      <td><a href="{{ route('obstetrics_items.edit', ['aitem' => $item->id ]) }}" class="">{{ $item->remark }}</a></td>
    </tr>
  @endforeach
  </tbody>
</table>


  </div>
  <!-- / .content -->
</div>
<!-- / .tabContents -->

@endsection
