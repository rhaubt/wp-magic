<?php
/*
To add wp_editor(); to en external page, and allow media buttons på be shown we need to add styles and script
but we also need to user to be loged in!

*/
add_filter('wp_head','ShowTinyMCE');
//Adding styles
    function ShowTinyMCE() {
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'jquery-color' );
        wp_print_scripts('editor');
        if (function_exists('add_thickbox')) add_thickbox();
        wp_print_scripts('media-upload');
        if (function_exists('wp_tiny_mce')) wp_tiny_mce();
        wp_enqueue_script('utils');
		wp_enqueue_script('editor');
		wp_enqueue_script('editor-functions');
		add_thickbox();
		wp_enqueue_script('media-upload');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('tiny_mce');
		add_action( 'media_buttons', 'media_buttons' );
    }

/*
Funtion to specify what upload tabs to be present!
*/
add_filter( 'media_upload_tabs', 'remove_additional_tabs' );
function remove_additional_tabs( $_default_tabs ) {
return array( 'type' => __('From Computer'),
	          'type_url' => __('From URL'),
		      'gallery' => __('Gallery')
		 );
}

//Needed to display the "insert to post"
add_filter( 'get_media_item_args', 'force_send' );
function force_send($args){
	$args['send'] = true;
	return $args;
}

//Admin some custum styling
add_action( 'admin_head_media_upload_gallery_form', 'mfields_remove_gallery_setting_div' );
if( !function_exists( 'mfields_remove_gallery_setting_div' ) ) {
    function mfields_remove_gallery_setting_div() {
        print '
            <style type="text/css">
                #gallery-settings *{
                display:none;
                }
            </style>';
    }
}

//To attact media to a post we need to previde a post id.
//If you use this code, and set the global $post_ID variable bofore the wp_editor() call, we can connect media libs to a serten number.
add_filter('_upload_iframe_src', 'filter_media_upload_iframe_src');
function filter_media_upload_iframe_src($src) {
global $post_ID;
return add_query_arg( 'post_id', $post_ID, admin_url('media-upload.php') );
}

/* example */
/*

 <?php  
 		global $post_ID;
		$post_ID = 55;
  		wp_editor(stripslashes($editPost['content']), 'content', array( 'media_buttons' => true )); 
  ?>


*/