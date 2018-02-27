@extends('layout/master')

<?php
$layout = [
    'title' => 'ページが見つかりませんでした',
];
?>

@section('content')
指定されたページが見つかりませんでした。<br>
URLを直接入力した場合，入力ミスがないかご確認ください。
@endsection
