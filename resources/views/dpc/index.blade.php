@extends('layout.master')

<?php

    $layout = [
        'title' => 'ファイル取込',
    ];

?>

@section('content')

      <div class="flowNav steps">
        <ul>
          <li class="current"><a href="{{ route('dpc.index') }}" class="flow">ファイル取込</a></li>
          <li><a href="{{ route('results.index') }}" class="flow">算定</a></li>
        </ul>
      </div>
      <!-- / .steps -->
      {{Form::open(['route' => 'dpc.upload', 'files' => true, 'class' => 'file-upload-form form-inline', 'method' => 'post', 'target' => 'file-upload-frame'])}}
      <div class="panel panel-default">
        <div class="panel-body">
          <p class="m_u20"> 文字コｰドは{{ config('my.dpc.import.encode') }}です。1行目はヘッダとして扱いますので、1行目はデｰタにならないようにしてください。改行コｰドは｢lf｣です。</p>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group pull-left{{ $errors->has('ef_file') ? ' has-error' : '' }}"> <span class="text-danger">※必須</span>
                <label class="control-label" for="tsv_file2">EFファイル (TSV)</label>
                <input name="ef_file" id="tsv_file2" type="file" style="display: inline-block;">
                @if ($errors->has('ef_file'))
                <span class="help-block"><strong>{{ $errors->first('ef_file') }}</strong></span>
                @endif
              </div>
            </div>
          </div>
          <!-- / .row -->
          <hr>
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group{{ $errors->has('h_file') ? ' has-error' : '' }}"> <span class="text-danger">※必須</span>
                <label class="control-label" for="tsv_file2">Hファイル (TSV)</label>
                <input name="h_file" id="tsv_file2" type="file" style="display: inline-block;">
                @if ($errors->has('h_file'))
                <span class="help-block"><strong>{{ $errors->first('h_file') }}</strong></span>
                @endif
              </div>
            </div>
          </div>
          <!-- / .row -->
          <hr>
          <div class="row">
            <div class="col-xs-6">
              <label class="control-label" for="chk_code"><input type="checkbox" value="1" name="chk_code" id="chk_code" class="dpc_chk_code"> 施設コード、識別番号を暗号化</label>
              <p>施設コード・識別番号に、入力した暗号化コードを加算して取り込みます。</p>

              <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }} dpc_chk_area"><br>
                <label class="control-label" for="code">暗号化コード</label>
                <input name="code" class="form-control" id="end_date" value="{{ Request::old('code') ?: rand(0, 999999) }}" type="number">
                @if ($errors->has('code'))
                <span class="help-block"><strong>{{ $errors->first('code') }}</strong></span>
                @endif
              </div>
            </div>
          </div>
          <!-- / .row -->
          <hr>
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                <label class="control-label" for="end_date">最終日</label>
                <input name="end_date" class="form-control datepicker" id="end_date" value="{{ Carbon\Carbon::today()->format('Y/m/d') }}" type="text">
                @if ($errors->has('end_date'))
                <span class="help-block"><strong>{{ $errors->first('end_date') }}</strong></span>
                @endif
              </div>
            </div>
          </div>
          <!-- / .row -->
        </div>
        <div class="panel-footer clearfix">
          <div class="pull-right">
            <button type="submit" class="btn btn-primary upload-btn" onclick="return confirm('完了までにデータによっては数十分程度の時間がかかることがあります')"> 更新</button>
          </div>
        </div>
        <!-- / .panel-footer -->
      </div>
      {!! Form::close() !!}

      <div id="loading"><div><img src="{{ asset('img/icon_loader.gif') }}" alt=""></div></div>
      <iframe name="file-upload-frame" style="width:0;height:0;display: none;"></iframe>

<script>
$(function(){
});
</script>

@endsection
