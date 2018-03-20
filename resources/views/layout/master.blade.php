<!DOCTYPE HTML>
<html lang="ja"><head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<link rel="shortcut icon" href="{{ asset ('img/favicon.ico') }}">
<meta name="description" content="">
<meta name="keywords" content="" lang="ja">
<title>{{ $layout['title'] ? $layout['title'] . '｜' : '' }} 看護必要度チェッカ―</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css" >
<link rel="stylesheet" href="{{ asset('css/stylesheet.css') }}?180320" type="text/css" >
<link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}" type="text/css" >

<script type="text/javascript" src="{{ asset('js/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/datepicker-ja.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
<!--[if lt IE 9]> 
<script type="text/javascript" src="{{ asset('js/html5shiv.js') }}"></script>
<![endif]-->
<script type="text/javascript" src="{{ asset('js/footerFixed.js') }}"></script>

<script type="text/javascript">
$(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
	$(".datepicker").datepicker();	
});
</script>
</head>
<body>
<noscript>
<div>
  <p id="title">-&nbsp;利用者様へのお願い&nbsp;-</p>
  <p>当ホームページでは、お客さまにより便利にご利用いただけるよう、JavaScriptを使用しております。<br />
    JavaScriptを無効にして使用された場合、コンテンツが正常に表示されない場合がございます。<br />
    JavaScriptを有効にしてご覧いただきますようお願いいたします。</p>
</div>
</noscript>
<a id="top"></a>
<header>
  <nav class="navbar navbar-default">
    <div class="inner"> <a class="navbar-brand" href="{{ route('root.index') }}">
      <h1><img src="{{ asset('img/logo.png') }}" alt="看護必要度チェックモジュール"></h1>
      </a>
      @if (Auth::guard('web')->check())
      <div class="navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="{{ route('dpc.index') }}">チェック</a></li>
          <li><a href="{{ route('a_items.index') }}">マスタ管理</a></li>
          <li><a href="{{ route('system.index') }}">システム設定</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="{{ route('auth.signout') }}">ログアウト</a></li>
        </ul>
      </div>
      @endif
    </div>
    <!-- /. inner -->
  </nav>
</header>
<div class="wrapper">
  @if (Auth::guard('web')->check() && Auth::guard('web')->user()->isDpcLoading())
  <div class="whileimport on"> ファイル取込中 <span class="loader"><img src="{{ asset('img/icon_loader.gif') }}" alt=""></span></div>
  <script type="text/javascript">
  $(function(){
    checkImportDpcStatus();
  });

  function checkImportDpcStatus() {
    // .phpファイルへのアクセス
    $.ajax('{{ route('api.dpc.get_import_status', ['id' => Auth::guard('web')->user()->id]) }}',
      {
        type: 'get',
        dataType: 'json'
      }
    )
    .done(function(data) {
        console.log(data);

        if (data['is_dpc_loading']) {
          setTimeout(function(){
            checkImportDpcStatus()
          },5000);

        } else {
          $('.whileimport').remove();
          $('.alert-success').remove();

          setTimeout(function(){
            if (data['dpc_import_status'] == 'NG') {
                alert('ファイル取込処理に失敗しました。');
            } else {
                alert('ファイル取込処理が完了しました。');
                location.reload();
            }
          },100);
        }
    })
    .fail(function() {
        console.log('failed to import dpc status.');
    });
  }
  </script>

  @endif
  <!-- / .whileimport -->
  <div class="container">
    <div class="row">

      @if (session('info'))
      <div @if (Auth::guard('web')->check() && Auth::guard('web')->user()->isDpcLoading()) class="w80"@endif>
        <div class="alert alert-success alert-dismissible" role="alert">{{ session('info') }}</div>
      </div>
      @endif

      @if (session('error'))
      <div @if (Auth::guard('web')->check() && Auth::guard('web')->user()->isDpcLoading()) class="w80" class="w80"@endif>
        <div class="alert alert-danger alert-dismissible" role="alert">{{ session('error') }}</div>
      </div>
      @endif

      @yield('content')

    </div>
    <!-- / .row -->
  </div>
  <!-- / .container -->
</div>
<!-- / .wrapper -->
<div class="pageTop"> <a href="#top" class="scroll"></a></div>
<!-- / .pageTop -->
<footer id="footer">
  <div class="container">
    <div class="copyright">Copyright &copy; <a href="http://jp.ebase-solutions.com/" target="_blank">eBase solutions laboratory</a> All Rights Reserved.</div>
  </div>
  <!-- / .inner -->
</footer>

@if (isset($layout['js']))
@foreach ($layout['js'] as $js)
<script src="{{ asset('js/' . $js . '.js') }}"></script>
@endforeach
@endif

<script>
  var localStrageKey = '{{ Auth::guard('web')->check()? Auth::guard('web')->user()->strageKey() : '' }}';
</script>

<script type="text/javascript" src="{{ asset('js/script.js') }}?180320"></script>

</body>
</html>
