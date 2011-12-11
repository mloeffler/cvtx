(function($){
	$('ul#antraege li.overview a').click(function() {
		var pathname = $(this).attr("href");
		if (location.pathname.replace(/^\//,") == this.pathname.replace(/^\//,") && location.hostname == this.hostname) {
			var $target = $(this.hash);
			$target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
			if ($target.length) {
				var targetOffset = $target.offset().top;
				$('html,body')
				.animate({scrollTop: targetOffset}, 1000);
				return false;
			}
		}
	});
	$('body').delegate('a.close','click', function() {
		$(".ae_antraege_overlay").fadeOut().remove();
		return false;
	});
	$('body').delegate('a.print','click', function() {
		$('#ae_window .result').printElement();
		return false;
	});
	$('a.ae_antraege_overview').click(function() {
		var target = $(this).attr("href");
		create_Overlay();
		$("#ae_window .result").load(target+' #ae_antraege');
		return false;
	});
	$(".cvtx_antrag_form .submit").click(function() {
		var ret = true;
		var i = 0;
		$("#message").remove();
		$(".cvtx_antrag_form .required").each(function() {
			var id = $(this).attr("id");
			if(!$(this).val() || 
			   id == 'cvtx_antrag_email' && !check_mail($(this).val()) ||
			   id == 'cvtx_antrag_phone' && !check_phone($(this).val())) {
				$(this).addClass("error");
				if(i==0)
					$(".cvtx_antrag_form").prepend('<p id="message" class="error">Bitte f&uuml;lle alle Felder aus die mit einem '+
					 						'<span class="form-required">*</span> gekennzeichnet sind!</p>');
				ret = false;
				if(id == 'cvtx_antrag_email' && $(this).val()) $("#message").append('<ul><li>Bitte gib eine g&uuml;ltige E-Mail-Adresse an!</li></ul>');
				if(id == 'cvtx_antrag_phone' && $(this).val()) $("#message").append('<ul><li>Bitte gib eine g&uuml;ltige Telefonnummer an!</li></ul>');
			}
			else {
				$(this).removeClass("error");
			}
			i++;
		})
		return ret;
	});
/*	$('a.add_ae_antraeg').click(function() {
		var target = $(this).attr("href");
		create_Overlay();
		$("#ae_window .result").load(target+' #add_aeantrag');
		return false;
	});*/
 })(jQuery);
 
function create_Overlay() {
	var height = jQuery(document).height();
	var width = jQuery(document).width();
	var rheight = jQuery(window).height()-100;
	var r2height = rheight-65;
	var r2width = rwidth-65;
	var rwidth = width-100;
	var scroll = getPageScroll();
	var top = scroll[1]+35;
	var output = '<div class="ae_antraege_overlay" style="width:'+width+'px;height:'+height+'px;z-index:10090;display:none">'+
				 '<div id="ae_window" style="width:'+rwidth+'px;height:'+rheight+'px;top:'+top+'px">'+
				 '</div></div>';
	jQuery("body").append(output);
	var navi = '<div class="navi"><span class="replace"><a href="#" class="close">Close</a><a href="#" class="print">Print</a></span></div>';
	var result = '<div class="result" style="height:'+r2height+'px;width:'+r2width+'px"></div>';
	jQuery("#ae_window").append(navi);
	jQuery("#ae_window").append(result);
	jQuery(".ae_antraege_overlay").fadeIn();
}

function check_mail(text) {
	if(text.search('@') <= 0) return false;
	else return true;
}

function check_phone(text) {
	for(i=0; i<text.length; i++)
		if(!(parseInt(text[i]) || text[i] == '/' || text[i] == ' ')) return false;
	return true;
}

// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.com.
function getPageScroll() {

    var xScroll, yScroll;

    if (self.pageYOffset || self.pageXOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    }
    else if (document.documentElement && (document.documentElement.scrollTop || document.documentElement.scrollLeft)) {  // Explorer 6 Strict.
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    }
    else if (document.body) {// All other Explorers.
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;
    }

    arrayPageScroll = [xScroll,yScroll];
    return arrayPageScroll;
  }