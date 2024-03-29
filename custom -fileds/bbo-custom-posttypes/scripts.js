(function($) {
	// add new field to the field group
	function simple_fields_field_group_add_field() {
		simple_fields_highest_field_id++;
		var data = {
			action: 'simple_fields_field_group_add_field',
			simple_fields_highest_field_id: simple_fields_highest_field_id
		}
		$.post(ajaxurl, data, function(response) {
			var ul = $("#simple-fields-field-group-existing-fields ul:first");
			$response = $(response);
			ul.append($response);
			ul.find(".simple-fields-field-group-one-field:last").effect("highlight").find(".simple-fields-field-group-one-field-name").focus();
			//$response.effect("highlight").find(".simple-fields-field-group-one-field-name").focus();
		});		
	}
	
	function simple_fields_metabox_tinymce_attach() {
		if (typeof( tinyMCE ) == "object" && typeof( tinyMCEPreInit ) == "object" ) {
			var tiny_init = tinyMCEPreInit.mceInit;
			tiny_init.mode = "exact";
			tiny_init.theme_advanced_resizing = true;
			var elms_to_convert = jQuery(".simple-fields-metabox-field-textarea-tinymce");
			var str_elms_to_convert = "";
			for (var i=0; i<elms_to_convert.length; i++) {
				var one_elm = elms_to_convert[i];
				// check if this element id already is a tiny editor
				if (tinyMCE.get(one_elm.id)) {
					// exists, do nada
				} else {
					// does not exist, not a tiny editor, so add to the list of ids to convert to editors
					str_elms_to_convert += one_elm.id + ",";
				}
				
			}
			str_elms_to_convert = str_elms_to_convert.replace(/,$/, "");
			if (str_elms_to_convert != "") {
				tiny_init.elements = str_elms_to_convert;
				tinyMCE.init( tiny_init );
			}
		}
	}
	
	function simple_fields_metabox_tinymce_detach() {
		for( edId in tinyMCE.editors ) {
			if ( /simple_fields/.test(edId) ) {
				tinyMCE.execCommand('mceRemoveControl', false, edId);
			}
		}
	}
	
	// switch-buttons
	$(".simple_fields_editor_switch_visual").live("click", function() {
		$this = $(this);
		$parent = $this.closest(".simple-fields-metabox-field")
		$parent.find(".simple_fields_editor_switch a").removeClass("selected");
		$this.addClass("selected");
		
		$parent.find(".simple-fields-metabox-field-textarea-tinymce-media").show();
		
		var tiny_id = $parent.find(".simple-fields-metabox-field-textarea-tinymce").attr("id");
		var tiny_init = tinyMCEPreInit.mceInit;
		tiny_init.mode = "exact";
		tiny_init.theme_advanced_resizing = true;
		tiny_init.elements = tiny_id;
		tinyMCE.init( tiny_init );
		return false;
	});
	$(".simple_fields_editor_switch_html").live("click", function() {
		$this = $(this);
		$parent = $this.closest(".simple-fields-metabox-field")
		$parent.find(".simple_fields_editor_switch a").removeClass("selected");
		$this.addClass("selected");
		
		$parent.find(".simple-fields-metabox-field-textarea-tinymce-media").hide();

		var tiny_id = $parent.find(".simple-fields-metabox-field-textarea-tinymce").attr("id");
		tinyMCE.execCommand('mceRemoveControl', false, tiny_id);
		return false;

	});

	function simple_fields_get_fieldID_from_this(t) {
		var $t = $(t);
		return $t.closest(".simple-fields-field-group-one-field").find(".simple-fields-field-group-one-field-id").val();
	}

	/* radiobuttons */
	function simple_fields_field_type_options_radiobutton_values_add(fieldID, fieldRadiobuttonID) {
		var $html = $("<li>\n<div class='simple-fields-field-type-options-radiobutton-handle'></div>\n<input class='regular-text' name='field["+fieldID+"][type_radiobuttons_options][radiobutton_num_"+fieldRadiobuttonID+"][value]' type='text' />\n<input class='simple-fields-field-type-options-radiobutton-deleted' name='field["+fieldID+"][type_radiobuttons_options][radiobutton_num_"+fieldRadiobuttonID+"][deleted]' type='hidden' value='0' />\n<input class='simple-fields-field-type-options-radiobutton-checked-by-default-values' type='radio' name='field["+fieldID+"][type_radiobuttons_options][checked_by_default_num]' value='radiobutton_num_"+fieldRadiobuttonID+"' />\n <a class='simple-fields-field-type-options-radiobutton-delete' href='#' style='display: none;'>Delete</a> </li>");
		var $fieldLI = $(".simple-fields-field-group-one-field-id-"+fieldID);
		$fieldLI.find(".simple-fields-field-type-options-radiobutton-values-added").append($html);
		$html.effect("highlight");
		$html.find("input:first").focus();
		$fieldLI.find(".simple-fields-field-type-options-radiobutton-values-added").sortable({
			axis: 'y',
			containment: "parent",
			handle: ".simple-fields-field-type-options-radiobutton-handle"
		});
	}


	function simple_fields_field_type_options_dropdown_values_add(fieldID, fieldDropdownID) {
		var $html = $("<li>\n<div class='simple-fields-field-type-options-dropdown-handle'></div>\n<input class='regular-text' name='field["+fieldID+"][type_dropdown_options][dropdown_num_"+fieldDropdownID+"][value]' type='text' />\n<input class='simple-fields-field-type-options-dropdown-deleted' name='field["+fieldID+"][type_dropdown_options][dropdown_num_"+fieldDropdownID+"][deleted]' type='hidden' value='0' />\n <a class='simple-fields-field-type-options-dropdown-delete' href='#' style='display: none;'>Delete</a> </li>");
		var $fieldLI = $(".simple-fields-field-group-one-field-id-"+fieldID);
		$fieldLI.find(".simple-fields-field-type-options-dropdown-values-added").append($html);
		$html.find("input:first").focus();
		$html.effect("highlight");
		$("ul.simple-fields-field-type-options-dropdown-values-added").sortable({
			axis: 'y',
			containment: "parent",
			handle: ".simple-fields-field-type-options-dropdown-handle"
		});
	}


	$("select.simple-fields-field-type").live("change", function() {
		// look for simple-fields-field-type-options-<type> and show if
		var $t = $(this);
		var selectedFieldType = $t.val();
		var $li = $t.closest("li");
		$li.find(".simple-fields-field-type-options").hide("slow");
		$li.find(".simple-fields-field-type-options-" + selectedFieldType).show("slow");
	});
	
	$("li.simple-fields-field-group-one-field").live("mouseenter", function() {
		$(this).find("div.delete").show();
	});
	$("li.simple-fields-field-group-one-field").live("mouseleave", function() {
		$(this).find("div.delete").hide();
	});

	$("li.simple-fields-field-group-one-field div.delete a").live("click", function(){
		if (confirm("Delete this field?")) {
			$(this).closest("li").find(".hidden_deleted").attr("value", 1);
			$(this).closest("li").hide("slow");
		} else {							
		}
		return false;
	});

	$(".simple-fields-field-group-delete a").live("click", function() {
		if (confirm("Delete this group?")) {
			return true;
		} else {							
		}
		return false;
	});
	
	$(".simple-fields-post-connector-delete a").live("click", function() {
		if (confirm("Delete this post connector?")) {
			return true;
		} else {							
		}
		return false;
	});

	$("a.simple-fields-field-type-options-radiobutton-values-add").live("click", function() {
		// finds the highest existing button id
		var $fieldRadiobuttonHighestID = $(this).closest(".simple-fields-field-group-one-field").find(".simple-fields-field-group-one-field-radiobuttons-highest-id");
		var fieldRadiobuttonHighestID = $fieldRadiobuttonHighestID.val();
		fieldRadiobuttonHighestID++;
		// add it
		simple_fields_field_type_options_radiobutton_values_add(simple_fields_get_fieldID_from_this(this), fieldRadiobuttonHighestID);
		$fieldRadiobuttonHighestID.val(fieldRadiobuttonHighestID);
		return false;
	});
	$("ul.simple-fields-field-type-options-radiobutton-values-added li").live("mouseenter", function() {
		$(this).find(".simple-fields-field-type-options-radiobutton-delete").show();
	});
	$("ul.simple-fields-field-type-options-radiobutton-values-added li").live("mouseleave", function() {
		$(this).find(".simple-fields-field-type-options-radiobutton-delete").hide();
	});
	$(".simple-fields-field-type-options-radiobutton-delete").live("click", function() {
		if (confirm("Delete radio button?")) {
			$(this).closest("li").hide("slow").find(".simple-fields-field-type-options-radiobutton-deleted").val("1");
		}
		return false;
	});

	$("a.simple-fields-field-type-options-dropdown-values-add").live("click", function() {
		// finds the highest existing button id
		var $fieldDropdownHighestID = $(this).closest(".simple-fields-field-group-one-field").find(".simple-fields-field-group-one-field-dropdown-highest-id");
		var fieldDropdownHighestID = $fieldDropdownHighestID.val();
		fieldDropdownHighestID++;
		// add it
		simple_fields_field_type_options_dropdown_values_add(simple_fields_get_fieldID_from_this(this), fieldDropdownHighestID);
		$fieldDropdownHighestID.val(fieldDropdownHighestID);
		return false;
	});
	$("ul.simple-fields-field-type-options-dropdown-values-added li").live("mouseenter", function() {
		$(this).find(".simple-fields-field-type-options-dropdown-delete").show();
	});
	$("ul.simple-fields-field-type-options-dropdown-values-added li").live("mouseleave", function() {
		$(this).find(".simple-fields-field-type-options-dropdown-delete").hide();
	});
	$(".simple-fields-field-type-options-dropdown-delete").live("click", function() {
		if (confirm("Delete dropdown value?")) {
			$(this).closest("li").hide("slow").find(".simple-fields-field-type-options-dropdown-deleted").val("1");
		}
		return false;
	});
	
	// get a field group from the server and add it to the page (aka "the add button")
	// what we need:
	// - field group id
	// - post id
	// - num in (new) set
	var simple_fields_new_fields_count = 0;
	$(".simple-fields-metabox-field-add").live("click", function() {

		var $t = $(this);
		//var $a = $(this).find("a");
		$t.text("Adding...");
		var $wrapper = $(this).parents(".simple-fields-meta-box-field-group-wrapper");
		var field_group_id = $wrapper.find("input[name=simple-fields-meta-box-field-group-id]").val();
		var post_id = $("#post_ID").val();

		var data = {
			"action": 'simple_fields_metabox_fieldgroup_add',
			"simple_fields_new_fields_count": simple_fields_new_fields_count,
			"field_group_id": field_group_id,
			"post_id": post_id
		};
	
		$.post(ajaxurl, data, function(response) {
			// alert('Got this from the server: ' + response);
			$ul = $wrapper.find("ul.simple-fields-metabox-field-group-fields");
			$response = $(response);
			$response.hide();
			$ul.prepend($response);
			$response.slideDown("slow", function() {
				simple_fields_metabox_tinymce_attach();
				$response.effect("highlight", 1000);
			});
			$t.html("<a href='#'>+ Add</a>");

		});
		
		simple_fields_new_fields_count++;

		return false;
	});

	$(".simple-fields-post-connector-addded-fields-delete").live("click", function() {
		if (confirm("Remove field group from post connector?")) {
			$(this).closest("li").hide("slow").find(".simple-fields-post-connector-added-field-deleted").val("1");
		}
		return false;
	});

	$("ul.simple-fields-metabox-field-group-fields-repeatable li").live("hover", function(e) {
		if (e.type == "mouseover") {
			$(this).addClass("hover");
		} else if (e.type == "mouseout") {
			$(this).removeClass("hover");
		}
	});
	// on click on any input in a repeatable field group: highlight whole group
	$("ul.simple-fields-metabox-field-group-fields-repeatable li input").live("focus", function() {
		$(this).closest("li").addClass("active");
	}).live("blur", function() {
		$(this).closest("li").removeClass("active");
	});
	
	$(".simple-fields-metabox-field-group").live("mouseenter", function() {
		$(this).find(".simple-fields-metabox-field-group-delete").show();
	});
	$(".simple-fields-metabox-field-group").live("mouseleave", function() {
		$(this).find(".simple-fields-metabox-field-group-delete").hide();
	});
	$(".simple-fields-metabox-field-group-delete").live("click", function() {
		if (confirm("Remove this field group?")) {
			var li = $(this).closest("li");
			li.hide("slow", function() { li.remove(); });
		}
		return false;
	});

	$(".simple-fields-metabox-field-file-select").live("click", function() {
		var input = $(this).closest(".simple-fields-metabox-field").find(".simple-fields-metabox-field-file-fileID");
		simple_fields_metabox_field_file_select_input_selectedID = input;
	});
	
	$(".simple-fields-file-browser-file-select").live("click", function() {
		var file_id = $(this).closest("li").find("input[name='simple-fields-file-browser-list-file-id']").val();
		var file_thumb = $(this).closest("li").find(".thumbnail img").attr("src");
		var file_name = $(this).closest("li").find("h3").text();
		self.parent.simple_fields_metabox_file_select(file_id, file_thumb, file_name);
		self.parent.tb_remove();
	});

	$(".simple-fields-metabox-field-file-clear").live("click", function() {
		var $li = $(this).closest(".simple-fields-metabox-field-file");
		$li.find(".simple-fields-metabox-field-file-fileID").val("");
		$li.find(".simple-fields-metabox-field-file-selected-image").text("");
		$li.find(".simple-fields-metabox-field-file-selected-image-name").text("");
		return false;
	});

	// media buttons
	$(".simple_fields_tiny_media_button").live("click", function(){
		var id = $(this).closest(".simple-fields-metabox-field").find("textarea").attr("id");
		simple_fields_focusTextArea(id);
		simple_fields_thickbox($(this).get(0));
		return false;
	});
	
	
	/**
	 * ondomready stuff
	 */
	$(function() {

		$("#simple-fields-field-group-existing-fields ul:first").sortable({
			distance: 10,
			axis: 'y',
			handle: ".simple-fields-field-group-one-field-handle"
		});
		
		// radiobutton
		$(".simple-fields-field-type-options-radiobutton-values-added").sortable({
			axis: 'y',
			containment: "parent",
			handle: ".simple-fields-field-type-options-radiobutton-handle"
		});

		$("#simple-fields-field-group-add-field").click(function() {
			simple_fields_field_group_add_field();
			return false;
		});

		/* dropdown */
		$("ul.simple-fields-field-type-options-dropdown-values-added").sortable({
			axis: 'y',
			containment: "parent",
			handle: ".simple-fields-field-type-options-dropdown-handle"
		});

		/**
		 * post connector
		 */
		$("#simple-fields-post-connector-add-fields").change(function() {
			var selectedVal = $(this).val();
			var selectedValName = $(this).find(":selected").text();
			$(this).val("");
			
			var str_html = "";
			str_html += "<li>";
			
			str_html += "<div class='simple-fields-post-connector-addded-fields-handle'></div>";
			str_html += "<div class='simple-fields-post-connector-addded-fields-field-name'>" + selectedValName + "</div>";
			str_html += "<input type='hidden' name='added_fields["+selectedVal+"][id]' value='"+selectedVal+"' />";
			str_html += "<input type='hidden' name='added_fields["+selectedVal+"][name]' value='"+selectedValName+"' />";
			str_html += "<input type='hidden' name='added_fields["+selectedVal+"][deleted]' value='0' />";

			str_html += "<div class='simple-fields-post-connector-addded-fields-options'>";
			str_html += "Context";
			str_html += "<select class='simple-fields-post-connector-addded-fields-option-context' name='added_fields["+selectedVal+"][context]'>";
			str_html += "<option value='normal'>normal</option>";
			str_html += "<option value='advanced'>advanced</option>";
			str_html += "<option value='side'>side</option>";
			str_html += "</select>";
			
			str_html += "Priority";
			str_html += "<select class='simple-fields-post-connector-addded-fields-option-priority' name='added_fields["+selectedVal+"][priority]'>";
			str_html += "<option value='low'>low</option>";
			str_html += "<option value='high'>high</option>";
			str_html += "</select>";
			str_html += "</div>";

			str_html += "<a href='#' class='simple-fields-post-connector-addded-fields-delete'>Delete</a>";

			str_html += "</li>";
			
			var $html = $(str_html);
			
			$("#simple-fields-post-connector-added-fields").append($html);
			
			$html.effect("highlight");
			
			
		});
		$("#simple-fields-post-connector-added-fields").sortable({
			axis: 'y',
			xcontainment: "parent",
			handle: ".simple-fields-post-connector-addded-fields-handle"
		});
		$("ul#simple-fields-post-connector-added-fields li").hover(function() {
			$(this).find(".simple-fields-post-connector-addded-fields-delete").show();
		}, function() {
			$(this).find(".simple-fields-post-connector-addded-fields-delete").hide();
		});


		/**
		 * edit posts
		 */
		$("#simple-fields-post-edit-side-field-settings-select-connector").change(function() {
			$("#simple-fields-post-edit-side-field-settings-select-connector-please-save").show("fast");
		});
		$("#simple-fields-post-edit-side-field-settings-show-keys").click(function() {
			$(".simple-fields-metabox-field-custom-field-key").toggle();
			return false;
		});

		$("ul.simple-fields-metabox-field-group-fields-repeatable").sortable({
			distance: 10,
			axis: 'y',
			handle: ".simple-fields-metabox-field-group-handle",
			start: function(event, ui) {
				// detach tinymce, or there will be errors
				simple_fields_metabox_tinymce_detach();
			},
			stop: function(event, ui) {
				simple_fields_metabox_tinymce_attach();
			}
		});

		
		// attach TinyMCE to textareas
		simple_fields_metabox_tinymce_attach();
		
		// Media browser: make sure search and filter works by adding hidden inputs
		// would have been best to do this in PHP, but I can't find any filter for it
		if ( pagenow == "media-upload-popup" && window.location.search.match(/simple_fields_dummy=/) ) {

			var frm_filter = $("form#filter");
			
			// http://localhost/wp-admin/media-upload.php?simple_fields_dummy=1&simple_fields_action=select_file&simple_fields_file_field_unique_id=simple_fields_fieldgroups_12_1_0&post_id=-1&
			// get these
			// simple_fields_dummy=1
			// simple_fields_action=select_file
			// simple_fields_file_field_unique_id=simple_fields_fieldgroups_12_1_0
			var params = {
				"simple_fields_dummy": 1,
				"simple_fields_action": "select_file"
			}
			
			var match = window.location.search.match(/simple_fields_file_field_unique_id=([\w]+)/);
			params.simple_fields_file_field_unique_id = match[1];
			
			// all params that start with "simple_fields_"
			$.each(params, function(key, val) {
				frm_filter.append("<input type='hidden' name='"+key+"' value='"+val+"' />");
			});	

		}
		
	});


}(jQuery));


// for media selectors
// code from custom field template by Hiroaki Miyashita
// 
var simple_fields_tmpFocus = undefined; // will contain the id of the tinymce field we are adding a file to
var simple_fields_isTinyMCE = false;
// when click the thickbox-link, "unset" our vars
jQuery(".thickbox").bind("click", function (e) {
	simple_fields_tmpFocus = undefined;
	simple_fields_isTinyMCE = false;
});
function simple_fields_focusTextArea(id) {
	if ( typeof tinyMCE != "undefined" ) {
		var elm = tinyMCE.get(id);
	}
	if (!elm || elm.isHidden()) {
		elm = document.getElementById(id);
		simple_fields_isTinyMCE = false;
	} else {
		simple_fields_isTinyMCE = true;
	}
	simple_fields_tmpFocus = elm;
	elm.focus();
	if (elm.createTextRange) {
		var range = elm.createTextRange();
		range.move("character", elm.value.length);
		range.select();
	} else if (elm.setSelectionRange) {
		elm.setSelectionRange(elm.value.length, elm.value.length);
	}
}
function simple_fields_thickbox(link) {
	var t = link.title || link.name || null;
	var a = link.href || link.alt;
	var g = link.rel || false;
	// alert(t); // title
	// alert(a); // http://localhost/wp-admin/media-upload.php?type=image&post_id=1060&TB_iframe=true
	// alert(g); // false
	tb_show(t,a,g);
	link.blur();
	return false;
}

// global js stuff; sorry about that...
var simple_fields_metabox_field_file_select_input_selectedID = null;
var simple_fields_is_simple_fields_popup = false;

// called when selecting file from tiny-area, if I remember correct
function simple_fields_metabox_file_select(file_id, file_thumb, file_name) {
	simple_fields_metabox_field_file_select_input_selectedID.val(file_id);
	$file_thumb_tag = jQuery("<img src='"+file_thumb+"' alt='' />");
	simple_fields_metabox_field_file_select_input_selectedID.closest(".simple-fields-metabox-field").find(".simple-fields-metabox-field-file-selected-image").html($file_thumb_tag);
	simple_fields_metabox_field_file_select_input_selectedID.closest(".simple-fields-metabox-field").find(".simple-fields-metabox-field-file-selected-image-name").text(file_name);
	simple_fields_metabox_field_file_select_input_selectedID.closest(".simple-fields-metabox-field").effect("highlight", 4000);
	
}
// simple-fields-metabox-field-file


