// scroll
$(function(){
	var $anchors = $("a.scroll[href^='#']");
	$anchors.each(function(){
		var $anchor   = $(this);
		var anchorID  = $anchor.attr("href");
		var $target   = $(anchorID);
		$anchor.click(function(e){
			var targetPositionTop = $target.offset().top;			 
			$('html, body').stop().animate({
				scrollTop: targetPositionTop
			}, 500);
			return false;
		});
	});
});

// 識別IDの復号キー取得
var code = 0;
var strageKey = localStrageKey;
if (window.localStorage) {
  code = window.localStorage.getItem(strageKey);
}

// paging
var loading_funcs = new Array;

var loadingBoxId = '#loading_box';
var fetchNextPageFuncName = 'fetchNextPage';
var checkNextPageFuncName = 'checkNextPage';
var itemsClassName = '.infinite-scroll-items';

loading_funcs[fetchNextPageFuncName] = function() {
    if ($(loadingBoxId).is(':hidden')) {
        return;
    }
    var $scroll = $('.table-type-outer');
    var loadingBoxTop = $(loadingBoxId).position().top;
    var scrollBottom = $scroll.height() + 10;
    var nextPageUrl = $(loadingBoxId).attr('data-next-page-url');

    if (scrollBottom > loadingBoxTop) {
        $scroll.off('scroll resize load', loading_funcs[fetchNextPageFuncName]);
        $(loadingBoxId + '_image').show();

        $
            .ajax({
                type: 'GET',
                url: nextPageUrl,
                dataType: 'html'
            })
            .done(function(res) {
                $(loadingBoxId).remove();
                $(itemsClassName).append(
                    $(res).find(itemsClassName).children()
                );
                $(loadingBoxId + '_parent').html($(res).find(loadingBoxId + '_parent').html());
            })
            .always(function(res) {
                loading_funcs[checkNextPageFuncName]();
            })
        ;
    }
}

loading_funcs[checkNextPageFuncName] = function() {
    var $scroll = $('.table-type-outer');
    if ($(loadingBoxId).length && $(loadingBoxId).attr('data-next-page-url')) {
        $scroll.on('scroll resize load click', loading_funcs[fetchNextPageFuncName]);
    }
    else {
        $(loadingBoxId).remove();
        $scroll.off('scroll resize load click', loading_funcs[fetchNextPageFuncName]);
    }

    $(".decryption_identification_id").each(function(i, obj){
      var id = $(obj).attr('data-iid');
      if (code) {
        id = parseInt(id) - parseInt(code);
      }
      $(obj).text(id);
    });
}

$(function(){
    // 識別IDの復号
    $(".decryption_identification_id").each(function(i, obj){
      var id = $(obj).attr('data-iid');
      if (code) {
        id = parseInt(id) - parseInt(code);
      }
      $(obj).text(id);
    });

    // 検索時に 識別IDの復号
    $('input[name=identification_id_work]').change(function(){
      var id = $(this).val();
      if (id != '' && code) {
        id = parseInt(id) + parseInt(code);
      }
      $('input[name=identification_id]').val(id);
    });
    var identificationId = $('input[name=identification_id]').val();
    if (identificationId != '') {
      if (code) {
        identificationId = parseInt(identificationId) - parseInt(code);
      }
      $('input[name=identification_id_work]').val(identificationId);
    }

    // paging
    loading_funcs[checkNextPageFuncName]();

    /*$(document).on('click', '.upload-btn', function(event){
        $('#loading').addClass('show');

       return true;
    });*/

    $('.file-upload-form').submit(function(){
       $('#loading').addClass('show');
       return true;
    });

    dpcToggleCode();
    $('.dpc_chk_code').change(function(){
       dpcToggleCode();
       return true;
    });
});

function dpcToggleCode() {
    if ($('.dpc_chk_code:checked').val() == '1') {
        $('.dpc_chk_area').show();
    } else {
        $('.dpc_chk_area').hide();
    }
}