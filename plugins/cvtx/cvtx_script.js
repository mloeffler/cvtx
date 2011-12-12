var cvtx_types = {"top"     : {"post_type"  : "cvtx_top",
                               "meta_fields": Array({"key": "cvtx_top_ord", "empty": false, "unique": true},
                                                    {"key": "cvtx_top_short", "empty": false, "unique": true})},
                  "antrag"  : {"post_type"  : "cvtx_antrag",
                               "meta_fields": Array({"key": "cvtx_antrag_ord", "empty": false, "unique": true})},
                  "aeantrag": {"post_type"  : "cvtx_aeantrag",
                               "meta_fields": Array({"key": "cvtx_aeantrag_zeile", "empty": false, "unique": true})}};

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
    $("#cvtx_top_ord_field").keyup(function() { cvtx_validate(cvtx_types.top, "ord"); });
    $("#cvtx_top_short_field").keyup(function() { cvtx_validate(cvtx_types.top, "short"); });
    
    // edit antrag
    $("#cvtx_antrag_top_select").change(function() { cvtx_get_top_short(); cvtx_validate(cvtx_types.antrag, "ord"); });
    $("#cvtx_antrag_ord_field").keyup(function() { cvtx_validate(cvtx_types.antrag, "ord"); });

    // edit aeantrag
    $("#cvtx_aeantrag_antrag_select").change(function() { cvtx_validate(cvtx_types.aeantrag, "zeile"); });
    $("#cvtx_aeantrag_zeile_field").keyup(function() { cvtx_validate(cvtx_types.aeantrag, "zeile"); });
    
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
    function cvtx_validate(type, meta_key) {
        // get value of post_meta field
        meta_value = $("#" + type.post_type + "_" + meta_key + "_field").val().trim();
        
        // update status
        for (var i = 0; i < type.meta_fields.length; i++) {
            if (type.meta_fields[i].key == type.post_type + "_" + meta_key) {
                type.meta_fields[i].empty  = !(meta_value && meta_value.length > 0);
                type.meta_fields[i].unique = !(meta_value && meta_value.length > 0) || type.meta_fields[i].unique;
            }
        }
        
        // value specified? check for unique
        if (meta_value && meta_value.length > 0) {
            query = {"action"    : "cvtx_validate",
                     "cookie"    : encodeURIComponent(document.cookie),
                     "post_type" : type.post_type,
                     "post_id[0]": $("#post_ID").val(),
                     "args"      : Array({"key"    : type.post_type + "_" + meta_key,
                                          "value"  : meta_value,
                                          "compare": "="})
                    };
            
            // special arguments for post_types
            if (type.post_type == "cvtx_antrag" && meta_key == "ord") {
                query.args.push({"key"     : "cvtx_antrag_top",
                                 "value"   : $("#cvtx_antrag_top_select").val(),
                                 "compare" : "="});
            } else if (type.post_type == "cvtx_aeantrag" && meta_key == "zeile") {
                query.args.push({"key"     : "cvtx_aeantrag_antrag",
                                 "value"   : $("#cvtx_aeantrag_antrag_select").val(),
                                 "compare" : "="});
            }
            
            // fetch info
            $.post("/conventix_wp/wp-admin/admin-ajax.php", query,
                   function (str) {
                       for (var i = 0; i < type.meta_fields.length; i++) {
                           if (type.meta_fields[i].key == type.post_type + "_" + meta_key) type.meta_fields[i].unique = (str == "+OK");
                       }
                       cvtx_toggle_buttons(type);
                   });
        }
        
        // update buttons
        cvtx_toggle_buttons(type);
    }
    
    /**
     * updates error messages and save/publish buttons
     */
    function cvtx_toggle_buttons(type) {
        empty = 0; notunique = 0;
        
        // fetch status and show/hide errors
        for (var i = 0; i < type.meta_fields.length; i++) {
            // field empty?
            if (type.meta_fields[i].empty) {
                empty++;
                $("#empty_error_" + type.meta_fields[i].key).css("display", "block");
            } else {
                $("#empty_error_" + type.meta_fields[i].key).css("display", "none");
            }
            
            // input unique?
            if (!type.meta_fields[i].unique) {
                notunique++;
                $("#" + type.meta_fields[i].key + "_field").addClass("error");
                $("#preview-action").hide();
                $("#unique_error_" + type.meta_fields[i].key).css("display", "block");
            } else {
                $("#" + type.meta_fields[i].key + "_field").removeClass("error");
                $("#preview-action").show();
                $("#unique_error_" + type.meta_fields[i].key).css("display", "none");
            }
        }
        
        // update buttons
        $("#save-post").attr("disabled", notunique > 0);
        $("#save").attr("disabled", notunique > 0);
        if (notunique > 0 || empty > 0) {
            $("#publish").attr("disabled", true);
            $("#message").fadeIn();
        } else {
            $("#publish").attr("disabled", false);
            $("#message").fadeOut();
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