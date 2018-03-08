<script type="text/javascript">
@if($errorMessages)
  @php
    $errorMsg = '';
    foreach ($errorMessages->get('ef_file') as $message) {
      $errorMsg .= $message . "。";
    }
    foreach ($errorMessages->get('h_file') as $message) {
      $errorMsg .= $message . "。";
    }
    foreach ($errorMessages->get('code') as $message) {
      $errorMsg .= $message . "。";
    }
    foreach ($errorMessages->get('end_date') as $message) {
      $errorMsg .= $message . "。";
    }
  @endphp
  alert('{{$errorMsg}}');
  parent.$('#loading').removeClass('show');
@else
  @if($code)
  if (window.localStorage) {
    window.localStorage.setItem('{{ $strageKey }}', {{ $code }});
    //window.localStorage.getItem('test_1_2018-11-11');
  }
  @endif

  parent.location.href = "/results";
@endif
</script>