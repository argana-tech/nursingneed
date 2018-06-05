@extends('layout.master')

<?php

    $layout = [
        'title' => '結果',
    ];

?>

@section('content')
      <div class="flowNav steps">
        <ul>
          <li><a href="{{ route('dpc.index') }}" class="flow">ファイル取込</a></li>
          <li class="current"><a href="{{ route('results.index') }}" class="flow">算定</a></li>
        </ul>
      </div>
      <!-- / .steps -->
      <form class="form">
        <div class="panel panel-default">
          <div class="panel-body clearfix">
            <div class="row">
              <div class="col-xs-2">
                <label>データ識別番号</label>
                @php $identificationIdOld = $search['identification_id'] ?? ''; @endphp
                <input value="" name="identification_id_work" placeholder="" type="text" class="form-control">
                <input value="{{ $identificationIdOld }}" name="identification_id" type="hidden">
              </div>

              <div class="col-xs-2">
                <div class="form-group">
                  <label>出力区分</label>
                  @php $selectOld = $search['select'] ?? ''; @endphp
                  <select name="select" class="form-control">
                    <option value="" selected="selected">産科小児以外</option>
                    <option value="child" @if($selectOld == 'child') selected="selected"@endif>小児のみ</option>
                    <option value="obstetrics" @if($selectOld == 'obstetrics') selected="selected"@endif>産科のみ</option>
                  </select>
                </div>
              </div>

              <div class="col-xs-2">
                <div class="form-group">
                  <label>病棟</label>
                  @php $wardOld = $search['ward'] ?? ''; @endphp
                  <select name="ward" class="form-control">
                    <option value="" selected="selected">全て</option>
                    @foreach(\App\ResultTargetDay::getCreatedWards() as $ward)
                    <option value="{{ $ward }}" @if($wardOld == $ward) selected="selected"@endif>{{ $ward }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-xs-4">
              </div>

              <div class="col-xs-2">
                @if(!empty($wardOld))
                @php
                  $hCount = Auth::guard('web')->user()->resultTargetDayHCount($wardOld);
                  $efCount = Auth::guard('web')->user()->resultTargetDayEFCount($wardOld);
                  $matchCount = Auth::guard('web')->user()->resultTargetDayMatchCount($wardOld);
                @endphp
                  <div class="panel panel-default" style="margin-bottom:0;">
                    <div class="panel-heading" style="padding:5px;font-weight:bold;">{{ $wardOld }} の入力割合</div>
                    <div class="panel-body" style="padding:5px;">
                      <table>
                        <tr><td>Hファイル</td><td>：@if($hCount){{ floor($matchCount / $hCount * 10000) / 100 }}% @else なし @endif</td></tr>
                        <tr><td>EFファイル</td><td>：@if($efCount){{ floor($matchCount / $efCount * 10000) / 100 }}% @else なし @endif</td></tr>
                      </table>
                    </div>
                  </div>
                @endif
              </div>

            </div>
            <!-- / .row -->
          </div>
          <!-- / .panel-body -->
          <div class="panel-footer clearfix">
            <input type="hidden" name="{{ $month }}" value="{{ $month }}">
            <div class="pull-left"><a href="{{ route('results.index') }}" class="btn btn-default">抽出条件をクリア</a>
              <button type="submit" class="btn btn-primary">検索</button>
            </div>
            <div class="pull-right"><a href="{{ route('results.download') }}" class="btn btn-success" onclick="return confirm('データによっては数分程度の時間がかかることがあります')">病棟別入力割合ダウンロード</a></div>
          </div>
          <!-- / .panel-footer -->
        </div>
        <!-- / .panel panel-default -->
      </form>

      <div class="m_u10">
        <div class="clearfix">
          <div class="pull-left"><span class="glyphicon glyphicon-calendar"></span> {{ Carbon\Carbon::parse($month . "-01")->format('Y') }}年<span class="h3">{{ Carbon\Carbon::parse($month . "-01")->format('m') }}</span>月</div>
          <div class="pull-left" style="margin-left:15px;">
            @if(Carbon\Carbon::parse($resultMinDate)->format('Ym') < Carbon\Carbon::parse($month . "-01")->format('Ym'))<a href="{{ route('results.index') }}?identification_id={{ $identificationIdOld }}&select= {{ $selectOld }}&ward={{ $wardOld }}&month={{ Carbon\Carbon::parse($month . "-01")->subMonth()->format('Y-m') }}" class="btn btn-default btn-xs" type="submit">前月</a>@endif
            @if(Carbon\Carbon::parse($resultMaxDate)->format('Ym') > Carbon\Carbon::parse($month . "-01")->format('Ym'))<a href="{{ route('results.index') }}?identification_id={{ $identificationIdOld }}&select= {{ $selectOld }}&ward={{ $wardOld }}&month={{ Carbon\Carbon::parse($month . "-01")->addMonth()->format('Y-m') }}" class="btn btn-default btn-xs" type="submit">次月</a>@endif
          </div>
          <!-- / .pull-left -->
          <div class="pull-right">@if($updatedResult) 更新日 : {{ Carbon\Carbon::parse($updatedResult)->format('Y-m-d H:i') }}@endif</div>
          <div class="pull-right">
            <div class="description-box clearfix">
              <div class="white">一致</div>
              <!-- / .white -->
              <div class="red">EFファイルのみ</div>
              <!-- / .red -->
              <div class="yellow">Hファイルのみ</div>
              <!-- / .yellow -->
              <div class="purple">対象外</div>
              <!-- / .purple -->
            </div>
          </div>
          <!-- / .description-box -->
        </div>
        <!-- / .clearfix -->
      </div>
      <!-- / .m_u10 -->
      @php
        $firstDay = Carbon\Carbon::parse($month . "-01")->format('1');
        $endDay = Carbon\Carbon::parse($month . "-01")->endOfMonth()->format('d');
      @endphp

      <table class="deco-tb w100 tc data-table fixed header">
        <thead class="head">
          <tr>
            <th rowspan="2" style="width: 175px;" class="head">データ識別番号</th>
            <th>&nbsp;</th>
            @for($d=$firstDay;$d<=$endDay;$d++)
            <th> {{ $d }} </th>
            @endfor
          </tr>
          <tr>
            <th> </th>
            @php $weeks = ['日', '月', '火', '水', '木', '金', '土']; @endphp
            @for($d=$firstDay;$d<=$endDay;$d++)
              @php
                Carbon\Carbon::setlocale('ja');
                $w = Carbon\Carbon::parse($month.'-'.$d)->dayOfWeek;
              @endphp
            <th> {{ $weeks[$w] }} </th>
            @endfor
          </tr>
        </thead>
      </table>

      <div class="table-type-outer m_u40">
        <table class="deco-tb w100 tc data-table fixed m_u80">
          <tbody class="infinite-scroll-items">
            @foreach($results as $result)
              @php
                $resultADays = $result->resultADays($month, $firstDay, $endDay, $search);
                $resultCDays = $result->resultCDays($month, $firstDay, $endDay, $search);
              @endphp
            <tr>
              <td rowspan="3" class="head">
                <a href="{{ route('results.show', $result->id) }}?month={{ $month }}" target="_blank" class="decryption_identification_id" data-iid="{{ $result->identification_id }}">
                  <img src="{{ asset('img/icon_loading-m.gif') }}" alt="{{ $result->identification_id }}">
                </a>
              </td>
              <td>棟</td>
              @foreach($result->resultDays($month, $firstDay, $endDay) as $day => $resultDay)
                @php
                  $resultADay = @$resultADays[$day];
                  $resultCDay = @$resultCDays[$day];
                @endphp
                @if (!count($resultADay) && !count($resultCDay))
                  <td class="color-gray"></td>
                @else
                  <td class="
                    @if (@$resultADay->is_syutyu || @$resultCDay->is_syutyu) hcu @else ippan @endif
                    @if (@$resultADay->status == 'not checked' || @$resultCDay->status == 'not checked') color-red @else color-white @endif
                  "></td>
                @endif
              @endforeach
            </tr>
            <tr>
              <td>A</td>
              @foreach($resultADays as $resultDay)
                @if (!count($resultDay))
                  <td class="color-gray"></td>
                @else
                  @if ($resultDay->status == 'checked')
                  <td class="color-white"></td>
                  @elseif ($resultDay->status == 'h_only')
                  <td class="color-yellow hover">
                    <div class="details">
                      <div class="ope"> {{ $resultDay->h_name }}</div>
                      <div class="count"></div>
                    </div>
                  </td>
                  @elseif ($resultDay->status == 'not checked')
                  <td class="color-red hover">
                    <div class="details">
                      <div class="ope"> {{ $resultDay->ef_name }}</div>
                      <div class="count">1日目</div>
                    </div>
                  </td>
                  @else
                  <td class="color-white"></td>
                  @endif 
                @endif
              @endforeach
            </tr>
            <tr>
              <td>C</td>
              @foreach($resultCDays as $resultDay)
                @if (!count($resultDay))
                  <td class="color-gray"></td>
                @else
                  @if ($resultDay->status == 'syutyu')
                  <td class="color-purple"></td>
                  @elseif ($resultDay->status == 'checked')
                  <td class="color-white"></td>
                  @elseif ($resultDay->status == 'h_only')
                  <td class="color-yellow hover">
                    <div class="details">
                      <div class="ope"> {{ $resultDay->h_name }}</div>
                      <div class="count"></div>
                    </div>
                  </td>
                  @elseif ($resultDay->status == 'not checked')
                  <td class="color-red hover">
                    <div class="details">
                      <div class="ope"> {{ $resultDay->ef_name }}</div>
                      <div class="count">{{ $resultDay->count_days }}日目</div>
                    </div>
                  </td>
                  @else
                  <td class="color-white"></td>
                  @endif 
                @endif
              @endforeach
            </tr>
            @endforeach
          </tbody>
        </table>

        <span id="loading_box_parent">
        @if ($results->nextPageUrl())
        <div id="loading_box" data-next-page-url="{{ $results->nextPageUrl() }}&identification_id={{ $identificationIdOld }}&select= {{ $selectOld }}&ward={{ $wardOld }}&month={{ $month }}"></div>
        <div id="loading_box_image"><img src="{{ asset('img/icon_loader_27.gif') }}" alt=""></div>
        @endif
        </span>

      </div>
      <!-- / .table-type-outer -->

@endsection
