@extends('layout.master')

<?php

    $layout = [
        'title' => '結果詳細',
    ];

?>

@section('content')
@php
  $weeks = ['日', '月', '火', '水', '木', '金', '土'];

  $firstDay = Carbon\Carbon::parse($month . "-01")->format('1');
  $endDay = Carbon\Carbon::parse($month . "-01")->endOfMonth()->format('d');

  $resultADays = $result->resultADays($month, $firstDay, $endDay);
  $resultCDays = $result->resultCDays($month, $firstDay, $endDay);

  $resultDays = [];
  $inWardDay = $result->inWardDay();
  foreach($result->resultDays($month, $firstDay, $endDay) as $day => $resultDay) {
    $resultADay = @$resultADays[$day];
    $resultCDay = @$resultCDays[$day];

    if (count($resultADay) || count($resultCDay)) {
      $resultDays[$day] = [
        'a' => $resultADay,
        'c' => $resultCDay,
      ];
    }
  }

@endphp
      <h2>詳細結果</h2>
      <div class="panel panel-default">
        <table class="table table-bordered table-striped" style="table-layout: fixed;">
          <thead>
            <tr>
              <th>データ識別番号</th>
              <th>入院日</th>
              <th>在院日数</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="decryption_identification_id" data-iid="{{ $result->identification_id }}"><img src="{{ asset('img/icon_loading-m.gif') }}" alt="{{ $result->identification_id }}"></td>
              <td>{{ $inWardDay->format('Y年m月d日') }}</td>
              <td>{{ count($resultDays) }}日</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- / . -->
      <div class="scroll_div m_u60">
        <table _fixedhead="rows:2; cols:3; div-auto-size: none;" class="deco-tb detail-table">
          <thead class="head">
            <tr>
              <th class="head">実施日</th>
@foreach ($resultDays as $day => $data)
@php $date = Carbon\Carbon::parse($month . "-" . $day); @endphp
              <th>{{ $date->format('Y年m月d日') }}({{ $weeks[$date->dayOfWeek] }})</th>
@endforeach
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="vt">A / モニタリング及び処置等 </td>
@foreach ($resultDays as $day => $data)
@php
$resultDay = $data['a'];
@endphp
@if ($resultDay && $resultDay->status == 'checked')
<td class="color-white"> @if($resultDay->is_syutyu) 集中 @else 一般@endif </td>
@elseif ($resultDay && $resultDay->status == 'not checked')
<td class="color-red"> @if($resultDay->is_syutyu) 集中 @else 一般@endif :差異あり </td>
@else
<td class="color-gray"> </td>
@endif
@endforeach
            </tr>
            <tr>
              <td class="vt">C / 手術等の医学的状況 </td>
@foreach ($resultDays as $day => $data)
@php
$resultDay = $data['c'];
@endphp
@if ($resultDay && $resultDay->is_syutyu)
<td class="hcu color-purple"> 集中 </td>
@elseif ($resultDay && $resultDay->status == 'checked')
<td class="ippan color-white"> 一般 </td>
@elseif ($resultDay && $resultDay->status == 'not checked')
<td class="ippan color-red"> 一般:差異あり </td>
@else
<td class="color-gray"> </td>
@endif
@endforeach
            </tr>
          </tbody>
        </table>
      </div>
      <!-- / .scroll_div -->
@endsection
