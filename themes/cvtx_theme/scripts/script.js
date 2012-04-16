/**
 * cvtx-Script
 *
 * @package WordPress
 * @subpackage cvtx
 */
(function($){
	$('ul.menu li').each(function() {
	    var width = $(this).width();
	    var id = '#'+$(this).attr('id');
	    var width2 = $(id+' ul.sub-menu.depth-0').width();
	    var width3 = width/2-width2/2;
	    $(id+' ul.sub-menu.depth-0').css('margin-left',width3+'px');
	    $(id+' span.arrow').css('margin-left',(width2/2-10)+'px');
	});
	$('ul.menu li').hover(function() {
			$(this).find('ul.depth-0').slideDown('fast');
		}, function () {
			$(this).find('ul.depth-0').slideUp('fast');
	});
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
	$('a.extern').attr('target','_blank');
	$('body').delegate('a.close','click', function() {
		remove_Overlay();
		return false;
	});
	$('body').delegate('.ae_antraege_overlay', 'click', function() {
		remove_Overlay();
		return false;
	});
	$('body').delegate('a.print','click', function() {
		$('#ae_window .result').printElement();
		return false;
	});
	$('body').delegate('#ae_antraege td.verfahren', 'hover', function() {
        $(this).parent().find('span.procedure').toggle('fast');
    });
	$('a.ae_antraege_overview').click(function() {
		var target = $(this).attr("href");
		create_Overlay();
		$("#ae_window .result").load(target+' #ae_antraege');
		return false;
	});
    $(".cvtx_antrag_form .submit").click(function() {
        var ret = true;
        $("#message").remove();
        
        $(".cvtx_antrag_form .required").each(function() {
            var id = $(this).attr("id");
            
            if (!$(this).val() || ((id == 'cvtx_antrag_email' || id == 'cvtx_aeantrag_email') && !check_mail($(this).val()))
             || ((id == 'cvtx_antrag_phone' || id == 'cvtx_aeantrag_phone') && !check_phone($(this).val()))) {
                $(this).addClass("error");
                
                if (ret) {
                    ret = false;
                    $(".cvtx_antrag_form").prepend('<p id="message" class="error">Bitte f&uuml;lle alle Felder aus die mit einem '
                                                   + '<span class="form-required">*</span> gekennzeichnet sind!</p>');
                }
                
                if ($(this).val() && (id == 'cvtx_antrag_email' || id == 'cvtx_aeantrag_email'))
                    $("#message").append('<ul><li>Bitte g&uuml;ltige E-Mail-Adresse angeben!</li></ul>');
                if ($(this).val() && (id == 'cvtx_antrag_phone' || id == 'cvtx_aeantrag_phone'))
                    $("#message").append('<ul><li>Bitte g&uuml;ltige Telefonnummer angeben!</li></ul>');
                    var message_offset = $("#message").offset().top;
                    $('html,body').animate({scrollTop: message_offset}, 100);
            } else {
                $(this).removeClass("error");
            }
        })
        return ret;
    });
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
	jQuery("body").addClass('overlay');
}

function remove_Overlay() {
	jQuery(".ae_antraege_overlay").fadeOut().remove();
	jQuery('body').removeClass('overlay');
}

function check_mail(text) {
	if(text.search('@') <= 0) return false;
	else return true;
}

function check_phone(text) {
    var matched = text.match(/^[0-9\s\-\+\(\)\/]*$/);
    if (matched) return true;
    else         return false;
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