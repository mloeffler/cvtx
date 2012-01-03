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
    
    // edit top
    if ($("#post_type").val() == "cvtx_top") {
        cvtx_fields = Array({"key": "cvtx_top_ord", "empty": false, "unique": true},
                            {"key": "cvtx_top_short", "empty": false, "unique": true});
        cvtx_validate("cvtx_top_ord");
        cvtx_validate("cvtx_top_short");
        $("#cvtx_top_ord_field").keyup(function() { cvtx_validate("cvtx_top_ord"); });
        $("#cvtx_top_short_field").keyup(function() { cvtx_validate("cvtx_top_short"); });
    }
    
    // edit antrag
    if ($("#post_type").val() == "cvtx_antrag") {
        cvtx_fields = Array({"key": "cvtx_antrag_ord", "empty": false, "unique": true});
        cvtx_validate("cvtx_antrag_ord");
        $("#cvtx_antrag_top_select").change(function() { cvtx_get_top_short(); cvtx_validate("cvtx_antrag_ord"); });
        $("#cvtx_antrag_ord_field").keyup(function() { cvtx_validate("cvtx_antrag_ord"); });
    }

    // edit aeantrag
    if ($("#post_type").val() == "cvtx_aeantrag") {
        cvtx_fields = Array({"key": "cvtx_aeantrag_zeile", "empty": false, "unique": true});
        cvtx_validate("cvtx_aeantrag_zeile");
        $("#cvtx_aeantrag_antrag_select").change(function() { cvtx_validate("cvtx_aeantrag_zeile"); });
        $("#cvtx_aeantrag_zeile_field").keyup(function() { cvtx_validate("cvtx_aeantrag_zeile"); });
    }
    
    /**
     * requests the shortcut for antraege and tops
     */
    function cvtx_get_top_short() {
        $.post("/conventix_wp/wp-admin/admin-ajax.php",
               {"action"   : "cvtx_get_top_short",
                "cookie"   : encodeURIComponent(document.cookie),
                "post_id"  : $("#cvtx_antrag_top_select").val()},
               function (str) { $("#cvtx_top_kuerzel").text(str); }
              );
    }
    
    /**
     * checks wheater field is empty and/or input is unique
     */
    function cvtx_validate(meta_key) {
        // get value of post_meta field
        meta_value = $("#" + meta_key + "_field").val().trim();
        
        // update status
        for (var i = 0; i < cvtx_fields.length; i++) {
            if (cvtx_fields[i].key == meta_key) {
                cvtx_fields[i].empty  = !(meta_value && meta_value.length > 0);
                cvtx_fields[i].unique = !(meta_value && meta_value.length > 0) || cvtx_fields[i].unique;
            }
        }
        
        // value specified? check for unique
        if (meta_value && meta_value.length > 0) {
            query = {"action"    : "cvtx_validate",
                     "cookie"    : encodeURIComponent(document.cookie),
                     "post_type" : $("#post_type").val(),
                     "post_id[0]": $("#post_ID").val(),
                     "args"      : Array({"key"    : meta_key,
                                          "value"  : meta_value,
                                          "compare": "="})
                    };
            
            // special arguments for post_types
            if (meta_key == "cvtx_antrag_ord") {
                query.args.push({"key"     : "cvtx_antrag_top",
                                 "value"   : $("#cvtx_antrag_top_select").val(),
                                 "compare" : "="});
            } else if (meta_key == "cvtx_aeantrag_zeile") {
                query.args.push({"key"     : "cvtx_aeantrag_antrag",
                                 "value"   : $("#cvtx_aeantrag_antrag_select").val(),
                                 "compare" : "="});
            }
            // fetch info
            $.post("/wp-admin/admin-ajax.php", query,
                   function (str) {
                       for (var i = 0; i < cvtx_fields.length; i++) {
                           if (cvtx_fields[i].key == meta_key) cvtx_fields[i].unique = (str == "+OK");
                       }
                       cvtx_toggle_buttons();
                   });
        }
        
        // update buttons
        cvtx_toggle_buttons();
    }
    
    /**
     * updates error messages and save/publish buttons
     */
    function cvtx_toggle_buttons() {
        empty = 0; notunique = 0;
        
        // fetch status and show/hide errors
        for (var i = 0; i < cvtx_fields.length; i++) {
            // field empty?
            if (cvtx_fields[i].empty) {
                empty++;
                $("#empty_error_" + cvtx_fields[i].key).css("display", "block");
            } else {
                $("#empty_error_" + cvtx_fields[i].key).css("display", "none");
            }
            
            // input unique?
            if (!cvtx_fields[i].unique) {
                notunique++;
                $("#" + cvtx_fields[i].key + "_field").addClass("error");
                $("#preview-action").hide();
                $("#unique_error_" + cvtx_fields[i].key).css("display", "block");
            } else {
                $("#" + cvtx_fields[i].key + "_field").removeClass("error");
                $("#preview-action").show();
                $("#unique_error_" + cvtx_fields[i].key).css("display", "none");
            }
        }
        
        // update buttons
        $("#save-post").attr("disabled", notunique > 0);
        $("#save").attr("disabled", notunique > 0);
        if (notunique > 0 || empty > 0) {
            $("#publish").attr("disabled", true);
            $("#admin_message").fadeIn();
        } else {
            $("#publish").attr("disabled", false);
            $("#admin_message").fadeOut();
        }
    }

	function showTarget(target) {
		$("h2.nav-tab-wrapper a").each(function() {
			$(this).removeClass("nav-tab-active");
		});
		$("ul#cvtx_options li.active").hide();
		$("#cvtx_navi a."+target).addClass("nav-tab-active");
		$("#"+target).fadeIn();
		$("#"+target).addClass("active");
		$('html,body').animate({scrollTop: 0}, 1);
	}
});

