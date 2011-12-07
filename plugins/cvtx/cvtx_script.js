jQuery(document).ready(function($){
	var target1 = window.location.hash.replace("#","");
	if(target1 != ''){
		showTarget(target1);
	}
	
	$("#cvtx_navi a").click(function() {
		var target = $(this).attr("href").replace("#","");
		if(target == '') {
			$("li#cvtx_aeantraege").show();
			$("h2.nav-tab-wrapper a.first").addClass("nav-tab-active");
		}
		else {
			showTarget(target);
		}
	});
});

function showTarget(target) {
	jQuery("h2.nav-tab-wrapper a").each(function() {
		jQuery(this).removeClass("nav-tab-active");
	});
	jQuery("ul#cvtx_options li.active").hide();
	jQuery("#cvtx_navi a."+target).addClass("nav-tab-active");
	jQuery("#"+target).fadeIn();
	jQuery("#"+target).addClass("active");
	jQuery('html,body').animate({scrollTop: 0}, 1);
}