(function($){
	$('div.toggler a').click(function() {
		$('#filter').slideToggle();
		if($('div.toggler a').text().indexOf("anzeigen") >= 0)
			$('div.toggler a').text('Filter verbergen');
		else $('div.toggler a').text('Filter anzeigen');
	});
})(jQuery);