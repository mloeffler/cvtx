/**
 * Jquery, used for the ae_antraege-Template
 *
 * @package WordPress
 * @subpackage cvtx
 */
 
(function($){
	$('div.toggler a').click(function() {
		$('#filter').slideToggle();
		if($('div.toggler a').text().indexOf("anzeigen") >= 0) {
			$('div.toggler .ui-btn-text').text('Filter verbergen');
			$('div.toggler a .ui-icon').addClass("ui-icon-arrow-d").removeClass("ui-icon-arrow-u");
		}
		else {
			$('div.toggler .ui-btn-text').text('Filter anzeigen');
			$('div.toggler a .ui-icon').addClass("ui-icon-arrow-u").removeClass("ui-icon-arrow-d");
		}
	});
})(jQuery);