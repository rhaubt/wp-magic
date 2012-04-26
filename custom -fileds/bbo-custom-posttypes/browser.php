<?php

add_action( 'admin_init', 'fileSmilla_admin_init' );

function fileSmilla_admin_init() {

	wp_enqueue_script("jquery");
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-sortable");

	// check if jquery should be loaded via http och https
	$http = "http";
	if (is_ssl()) {
		$http = "https";
	}

	wp_enqueue_script("jquery-ui-effects-core", "$http://jquery-ui.googlecode.com/svn/tags/1.7.3/ui/effects.core.js");
	wp_enqueue_script("jquery-ui-effects-highlight", "$http://jquery-ui.googlecode.com/svn/tags/1.7.3/ui/effects.highlight.js");
	wp_enqueue_script("thickbox");
	wp_enqueue_style("thickbox");
}

// now lets get that file dialog working!
add_filter( 'media_send_to_editor', 'simple_fields_media_send_to_editor', 15, 2 );
add_filter( 'media_upload_tabs', 'simple_fields_media_upload_tabs', 15);
add_filter( 'media_upload_form_url', 'simple_fields_media_upload_form_url');
add_filter( 'attachment_fields_to_edit', 'simple_fields_attachment_fields_to_edit', 10, 2 );
add_action( 'admin_head', 'simple_fields_admin_head_select_file' );
add_action( 'admin_init', 'simple_fields_post_admin_init' );

/**
 * Change "insert into post" to something better
 * Code inspired by/gracefully stolen from
 * http://mondaybynoon.com/2010/10/12/attachments-1-5/#comment-27524
 */
function simple_fields_post_admin_init() {
	if ($_GET["fileSmilla_action"] == "select_file") {
		add_filter('gettext', 'simple_fields_hijack_thickbox_text', 1, 3);
	}
}
function simple_fields_hijack_thickbox_text($translated_text, $source_text, $domain) {
	if ($_GET["fileSmilla_action"] == "select_file") {
		if ('Insert into Post' == $source_text) {
			return __('Select', 'simple_fields' );
		}
	}
	return $translated_text;
}


/*
	hide some stuff in the file browser
*/
function simple_fields_admin_head_select_file() {
	if (isset($_GET["fileSmilla_actiona"]) && $_GET["fileSmilla_action"] == "select_file") {
		?>
		<style type="text/css">
			.wp-post-thumbnail,
			tr.image_alt,
			tr.post_title,
			tr.align,
			tr.image-size
			 {
				display: none;
			}
	
		</style>
		<?php
	}
}

// remove some fields in the file select dialogue, since simple fields don't use them anyway
function simple_fields_attachment_fields_to_edit($form_fields, $post) {
	if (isset($_GET["fileSmilla_action"]) && $_GET["fileSmilla_action"] == "select_file") {
		unset(
			$form_fields["post_excerpt"],
			$form_fields["post_content"],
			$form_fields["url"],
			$form_fields["image_url"],
			$form_fields["image_alt"],
			$form_fields["menu_order"]
		);
		#bonny_d($form_fields);
	}
	return $form_fields;
}

// if we have simple fields args in GET, make sure our simple fields-stuff are added to the form
function simple_fields_media_upload_form_url($url) {
	// $url:
	// http://localhost/wp-admin/media-upload.php?type=file&tab=library&post_id=0
	/*
	Array
	(
	    [simple_fields_dummy] => 1
	    [simple_fields_action] => select_file
	    [simple_fields_file_field_unique_id] => simple_fields_fieldgroups_8_4_0
	    [tab] => library
	)
	*/
	foreach ($_GET as $key => $val) {
		if (strpos($key, "fileSmilla_") === 0) {
			$url = add_query_arg($key, $val, $url);
		}
	}
	return $url;
}

// remove gallery and remote url tab in file select
function simple_fields_media_upload_tabs($arr_tabs) {
	if ($_GET["fileSmilla_action"] == "select_file" || $_GET["fileSmilla_action"] == "select_file_for_tiny") {
		unset($arr_tabs["gallery"], $arr_tabs["type_url"]);
	}
	return $arr_tabs;
}

// send the selected file to simple fields
function simple_fields_media_send_to_editor($html, $id) {
	/*
	post_id	1060, -1 since dda17 October, 2
	tab	library
	type	file
	
	POST
	_wp_http_referer=/wp-admin/media-upload.php?simple_fields_action=select_file&simple_fields_file_field_unique_id=simple_fields_fieldgroups_8_4_new0&tab=library
	*/
	parse_str($_POST["_wp_http_referer"], $arr_postinfo);
	#bonny_d($arr_url);
	/*
	Array
	(
	    [/wp-admin/media-upload_php?simple_fields_dummy] => 1
	    [simple_fields_action] => select_file
	    [simple_fields_file_field_unique_id] => simple_fields_fieldgroups_8_4_new1
	    [tab] => library
	)
	*/
	// only act if file browser is initiated by simple fields
	if (isset($arr_postinfo["fileSmilla_action"]) && $arr_postinfo["fileSmilla_action"] == "select_file") {

		// add the selected file to input field with id simple_fields_file_field_unique_id
		$simple_fields_file_field_unique_id = $arr_postinfo["simple_fields_file_field_unique_id"];
		$file_id = (int) $id;
		
		$image_thumbnail = wp_get_attachment_image_src( $file_id, 'thumbnail', true );
		$image_thumbnail = $image_thumbnail[0];
		$image_html = "<img src='$image_thumbnail' alt='' />";
		$file_name = rawurlencode(get_the_title($file_id));
		?>
		<script type="text/javascript">
			var win = window.dialogArguments || opener || parent || top;
			win.jQuery("#<?php echo $simple_fields_file_field_unique_id ?>").val(<?php echo $file_id ?>);
			win.jQuery("#<?php echo $simple_fields_file_field_unique_id ?>").closest(".simple-fields-metabox-field-file").find(".simple-fields-metabox-field-file-selected-image").html("<?php echo $image_html ?>");
			win.jQuery("#<?php echo $simple_fields_file_field_unique_id ?>").closest(".simple-fields-metabox-field-file").closest(".simple-fields-metabox-field").find(".simple-fields-metabox-field-file-selected-image-name").text(unescape("<?php echo $file_name?>"));
			win.tb_remove();
		</script>
		<?php
		exit;
	} else {
		return $html;
	}

}

?>
