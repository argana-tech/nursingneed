
    <div class="form-group{{ $errors->has('intensive_ward') ? ' has-error' : '' }}">
      <label class="control-label col-xs-2" for="input_name">集中病棟名 </label>
      <div class="col-xs-10">
        <textarea name="intensive_ward" class="form-control" rows="4" placeholder="複数ある場合は改行で区切ってください">{{ Request::old('intensive_ward') ?: $system->intensive_ward }}</textarea>
        @if ($errors->has('intensive_ward'))
        <span class="help-block"><strong>{{ $errors->first('intensive_ward') }}</strong></span>
        @endif
      </div>
    </div>

    <div class="form-group{{ $errors->has('obstetrics_ward') ? ' has-error' : '' }}">
      <label class="control-label col-xs-2" for="input_name">産科病棟名 </label>
      <div class="col-xs-10">
        <textarea name="obstetrics_ward" class="form-control" rows="2" placeholder="複数ある場合は改行で区切ってください">{{ Request::old('obstetrics_ward') ?: $system->obstetrics_ward }}</textarea>
        @if ($errors->has('obstetrics_ward'))
        <span class="help-block"><strong>{{ $errors->first('obstetrics_ward') }}</strong></span>
        @endif
      </div>
    </div>

    <div class="form-group{{ $errors->has('child_operation_name') ? ' has-error' : '' }}">
      <label class="control-label col-xs-2" for="input_name">子供の手術名 </label>
      <div class="col-xs-10">
        <textarea name="child_operation_name" class="form-control" rows="2" placeholder="複数ある場合は改行で区切ってください">{{ Request::old('child_operation_name') ?: $system->child_operation_name }}</textarea>
        @if ($errors->has('child_operation_name'))
        <span class="help-block"><strong>{{ $errors->first('child_operation_name') }}</strong></span>
        @endif
      </div>
    </div>
