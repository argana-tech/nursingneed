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

$(function(){
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