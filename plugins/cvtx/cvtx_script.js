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

    // TOP bearbeiten
    $("#cvtx_top_ord_field").keyup(function() { check_unique("cvtx_top_ord"); });
    $("#cvtx_top_short_field").keyup(function() { check_unique("cvtx_top_short"); });
    
    // Antrag bearbeiten
    $("#cvtx_antrag_top_select").change(function() { get_short("cvtx_antrag"); check_unique("cvtx_antrag_ord"); });
    $("#cvtx_antrag_ord_field").keyup(function() { check_unique("cvtx_antrag_ord"); });

    // Änderungsantrag bearbeiten
    $("#cvtx_aeantrag_antrag_select").change(function() { get_short("cvtx_aeantrag"); check_unique("cvtx_aeantrag_zeile"); });
    $("#cvtx_aeantrag_zeile_field").keyup(function() { check_unique("cvtx_aeantrag_zeile"); });
    
    function get_short(type) {
        if (type == "cvtx_antrag") {
            $.post("/conventix_wp/wp-admin/admin-ajax.php",
                   {"action" : "get_short",
                    "cookie" : encodeURIComponent(document.cookie),
                    "post_id": $("#cvtx_antrag_top_select option:selected").val()},
                   function (str) { $("#cvtx_top_kuerzel").text(str); }
                  );
        } else if (type == "cvtx_aeantrag") {
            $.post("/conventix_wp/wp-admin/admin-ajax.php",
                   {"action" : "get_short",
                    "cookie" : encodeURIComponent(document.cookie),
                    "post_id": $("#cvtx_aeantrag_antrag_select option:selected").val()},
                   function (str) { $("#cvtx_antrag_kuerzel").text(str); }
                  );
        }
    }
    
    function check_unique(type) {
        query = null;
        if (type == "cvtx_antrag_ord") {
            query = {"post_type"       : "cvtx_antrag",
                     "post_id[0]"      : $("#post_ID").val(),
                     "args[0][key]"    : "cvtx_antrag_top",
                     "args[0][value]"  : $("#cvtx_antrag_top_select option:selected").val(),
                     "args[0][compare]": "=",
                     "args[1][key]"    : "cvtx_antrag_ord",
                     "args[1][value]"  : $("#cvtx_antrag_ord_field").val(),
                     "args[1][compare]": "="};
        } else if (type == "cvtx_aeantrag_zeile") {
            query = {"post_type"       : "cvtx_aeantrag",
                     "post_id[0]"      : $("#post_ID").val(),
                     "args[0][key]"    : "cvtx_aeantrag_antrag",
                     "args[0][value]"  : $("#cvtx_aeantrag_antrag_select option:selected").val(),
                     "args[0][compare]": "=",
                     "args[1][key]"    : "cvtx_aeantrag_zeile",
                     "args[1][value]"  : $("#cvtx_aeantrag_zeile_field").val(),
                     "args[1][compare]": "="};
        } else if (type == "cvtx_top_ord") {
            query = {"post_type"       : "cvtx_top",
                     "post_id[0]"      : $("#post_ID").val(),
                     "args[0][key]"    : "cvtx_top_ord",
                     "args[0][value]"  : $("#cvtx_top_ord_field").val(),
                     "args[0][compare]": "="};
        } else if (type == "cvtx_top_short") {
            query = {"post_type"       : "cvtx_top",
                     "post_id[0]"      : $("#post_ID").val(),
                     "args[0][key]"    : "cvtx_top_short",
                     "args[0][value]"  : $("#cvtx_top_short_field").val(),
                     "args[0][compare]": "="};
        }
        
        if (query != null) {
            query.action = "check_unique";
            query.cookie = encodeURIComponent(document.cookie);
            $.post("/conventix_wp/wp-admin/admin-ajax.php", query, function (str) { toggle_buttons((str == "+OK"), type); });
        }
    }
    
    function toggle_buttons(show, type) {
        $("#save-post").attr("disabled", !show);
        $("#save").attr("disabled", !show);
        $("#publish").attr("disabled", !show || $("#" + type + "_field").val().length == 0);
        if (show) {
            $("#" + type + "_field").css("background-color", "lightgreen");
            $("#preview-action").show();
            $("#unique_error_" + type).css("display", "none");
        } else {
            $("#" + type + "_field").css("background-color", "red");
            $("#preview-action").hide();
            $("#unique_error_" + type).css("display", "block");
        }
        if ($("#" + type + "_field").val().length == 0) {
            $("#empty_error_" + type).css("display", "block");
        } else {
            $("#empty_error_" + type).css("display", "none");
        }
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