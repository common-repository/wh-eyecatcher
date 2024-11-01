jQuery(document).ready(function( $ ){
	$('select#wh_eyecatcher_style').on('change', function () {
	    $("textarea#wh_eyecatcher_css").val(this.value);
	    $("div#wh-eyecatcher").attr("style",this.value);
	});
	$('textarea#wh_eyecatcher_css').on('change keyup paste', function () {
		$("div#wh-eyecatcher").attr("style",this.value);
	});
	$('input#wh_eyecatcher_slogan').on('change keyup paste', function () {
		$("div#wh-eyecatcher").html($("input#wh_eyecatcher_slogan").attr("value"));
	});
});
