@extends('layout.master')

<?php

    $layout = [
        'title' => '結果詳細',
    ];

?>

@section('content')
@php
$weeks = ['日', '月', '火', '水', '木', '金', '土'];
$inWardDays = [];

$inWardDay = $result->inWardDay();
$outWardDay = $result->outWardDay();

$dayCounter = clone $inWardDay;

while ($dayCounter->lte($outWardDay)):
  if (
    $month == $dayCounter->format('Y-m')
      && (
        $result->resultTargetADays()->where('date', $dayCounter->format('Y-m-d'))->first()
        || $result->resultTargetCDays()->where('date', $dayCounter->format('Y-m-d'))->first()
      )
  ) $inWardDays[] = clone $dayCounter;

  $dayCounter->addDay();
endwhile;

@endphp
      <h2>詳細結果</h2>
      <div class="panel panel-default">
        <table class="table table-bordered table-striped" style="table-layout: fixed;">
          <thead>
            <tr>
              <th>データ識別番号</th>
              <th>入院日</th>
              <!-- th>退院日</th -->
              <th>在院日数</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $result->identification_id }}</td>
              <td>{{ $inWardDay->format('Y年m月d日') }}</td>
              <!-- td>{{ $outWardDay->format('Y年m月d日') }}</td -->
              <td>{{ $inWardDay->diffInDays($outWardDay) + 1 }}日</td>
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
@foreach ($inWardDays as $date)
              <th>{{ $date->format('Y年m月d日') }}({{ $weeks[$date->dayOfWeek] }})</th>
@endforeach
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="vt">A / モニタリング及び処置等 </td>
@foreach ($inWardDays as $date)
@php
$resultDay = $result->resultTargetADays()->where('date', $date->format('Y-m-d'))->first();
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
@foreach ($inWardDays as $date)
@php
$resultDay = $result->resultTargetCDays()->where('date', $date->format('Y-m-d'))->first();
@endphp
@if ($resultDay && $resultDay->is_syutyu)
<td class="hcu color-purple"> 集中 </td>
@elseif ($resultDay && $resultDay->status == 'checked')
<td class="ippan color-white"> </td>
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
