jQuery(document).ready(function($){
	var target1 = window.location.hash.replace("#","");
	if(target1 != ''){
		showTarget(target1);
	}
	else{
		showTarget('cvtx_tool');
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
	
    $("#cvtx_antrag_top_select").change(function() {
        $.post("/conventix_wp/wp-admin/admin-ajax.php",
               {"action": "get_short",
                "cookie": encodeURIComponent(document.cookie),
                "top_id": $("#cvtx_antrag_top option:selected").val()},
               function (str) { $("#cvtx_top_kuerzel").text(str); }
              );
        check_unique_antrag_ord();
    });
	
    $("#cvtx_antrag_ord_field").keyup(check_unique_antrag_ord);
    
    function check_unique_antrag_ord() {
        $.post("/conventix_wp/wp-admin/admin-ajax.php",
               {"action": "check_unique",
                "cookie": encodeURIComponent(document.cookie),
                "post_id": $("#post_ID").val(),
                "post_type": "cvtx_antrag",
                "top_id": $("#cvtx_antrag_top option:selected").val(),
                "antrag_ord": $("#cvtx_antrag_ord_field").val()},
               function (str) {
                   if (str == "+OK") {
                       $("#cvtx_antrag_ord_field").css("background-color", "lightgreen");
                       $("#save-post").attr("disabled", false);
                       $("#save").attr("disabled", false);
                       $("#publish").attr("disabled", false);
                       $("#preview-action").show();
                   } else {
                       $("#cvtx_antrag_ord_field").css("background-color", "red");
                       $("#save-post").attr("disabled", true);
                       $("#save").attr("disabled", true);
                       $("#publish").attr("disabled", true);
                       $("#preview-action").hide();
                   }
               });
    }
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