$(document).ready(function(){
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
	$('a.ae_antraege_overview').click(function() {
		var target = $(this).attr("href");
		var id = $(this).attr("meta-id");
		//var result1 = $(document).find('#result-'+id);
		var height = $(document).height();
		var width = $(document).width();
		var rheight = $(window).height()-100;
		var rwidth = width-100;
		var scroll = getPageScroll();
		var top = scroll[1]+35;
		var output = '<div class="ae_antraege_overlay" style="width:'+width+'px;height:'+height+'px;z-index:10090;display:none">'+
					 '<div id="ae_window" style="width:'+rwidth+'px;height:'+rheight+'px;top:'+top+'px">'+
					 '</div></div>';
		$("body").append(output);
		var navi = '<div class="navi"><span class="replace"><a href="#" class="close">Close</a><a href="#" class="print">Print</a></span></div>';
		var result = '<div class="result"></div>';
		$("#ae_window").append(navi);
		$("#ae_window").append(result);
		$(".ae_antraege_overlay").fadeIn();
		$("#ae_window .result").load(target+' #ae_antraege');
		return false;
	});
});

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