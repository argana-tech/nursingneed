
    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
      <label class="control-label col-xs-1" for="input_name">名称 <span class="text-danger">※</span></label>
      <div class="col-xs-8">
        <input type="text" name="name" placeholder="名称" id="name" class="form-control" value="{{Request::old('name') ?: $item->name}}" />
        @if ($errors->has('name'))
        <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
        @endif
      </div>
    </div>

    <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
      <label class="control-label col-xs-1" for="input_code">コード <span class="text-danger">※</span></label>
      <div class="col-xs-8">
        <input type="text" name="code" placeholder="コード" id="code" class="form-control" value="{{Request::old('code') ?: $item->code}}" />
        @if ($errors->has('code'))
        <span class="help-block"><strong>{{ $errors->first('code') }}</strong></span>
        @endif
      </div>
    </div>

    <div class="form-group{{ $errors->has('kcode') ? ' has-error' : '' }}">
      <label class="control-label col-xs-1" for="input_kcode">Kコード</label>
      <div class="col-xs-8">
        <input type="text" name="kcode" placeholder="Kコード" id="kcode" class="form-control" value="{{Request::old('kcode') ?: $item->kcode}}" />
        @if ($errors->has('kcode'))
        <span class="help-block"><strong>{{ $errors->first('kcode') }}</strong></span>
        @endif
      </div>
    </div>

    <div class="form-group{{ $errors->has('remark') ? ' has-error' : '' }}">
      <label class="control-label col-xs-1" for="input_remark">備考</label>
      <div class="col-xs-8">
        <input type="text" name="remark" placeholder="備考" id="name" class="form-control" value="{{Request::old('remark') ?: $item->remark}}" />
        @if ($errors->has('remark'))
        <span class="help-block"><strong>{{ $errors->first('remark') }}</strong></span>
        @endif
      </div>
    </div>
