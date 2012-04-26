<?php
/*
Plugin Name: Flator wp bridge
Plugin URI: http://www.bebetteronline.com
Description: Integrates flator.se and wp
Author: Robin Westerlundh
Version: 1.0
Author URI: http://www.thisoneisgreen.com
*/
?>
<?php 

add_filter('wp_head','ShowTinyMCE');
    function ShowTinyMCE() {
        // conditions here
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'jquery-color' );
        wp_print_scripts('editor');
        if (function_exists('add_thickbox')) add_thickbox();
        wp_print_scripts('media-upload');
        if (function_exists('wp_tiny_mce')) wp_tiny_mce();
        //wp_admin_css();
        wp_enqueue_script('utils');
        //do_action("admin_print_styles-post-php");
        //do_action('admin_print_styles');
	//wp_admin_css('thickbox');
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
add_filter( 'media_upload_tabs', 'wpse42068_remove_additional_tabs' );
function wpse42068_remove_additional_tabs( $_default_tabs ) {
return array( 'type' => __('From Computer'),
	          'type_url' => __('From URL'),
		      'gallery' => __('Gallery')
		 );
}

add_filter( 'get_media_item_args', 'force_send' );
function force_send($args){
	$args['send'] = true;
	return $args;
}

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

add_filter('_upload_iframe_src', 'filter_media_upload_iframe_src');
function filter_media_upload_iframe_src($src) {
global $post_ID;
return add_query_arg( 'post_id', $post_ID, admin_url('media-upload.php') );
}

function bbo_wp_signout(){
	wp_logout();
}

function bbo_wp_signin(){
	
	if(!isset($_SESSION["userId"])){
		wp_logout();
	}else{
		if (!is_user_logged_in() ) 
		{
			$creds = array();
			$creds['user_login'] = 'flator';
			$creds['user_password'] = 'sx53gmQ9';
			$creds['remember'] = true;
			$user = wp_signon( $creds, false );
			if ( is_wp_error($user) ){
			  // echo $user->get_error_message();
			}

		} 
		
	}
	
}



function prevent_admin_access() {
    if (strpos(strtolower($_SERVER['REQUEST_URI']), '/wp-admin') !== false && !current_user_can('Administrator')) {
        wp_redirect(get_option('siteurl'));
    }
}
 
//add_action('init', 'prevent_admin_access', 0);

show_admin_bar( false );


function isBlogVisible($id = 0){
global  $DB;
$q = "SELECT * FROM fl_blog_privacy WHERE userid = ".$id;
$privacy =  $DB->GetRow($q, FALSE, TRUE );
if($privacy['visible'] == 1){
	return true;
}else{
	return false;
}

}

function setBlogVisible($id = 0, $visiblue = true){
global  $DB;
$q = "SELECT * FROM fl_blog_privacy WHERE userid = ".$id;

$ex = $DB->execute($q);                                                                                                   
$privacy_count =  $ex->RecordCount();
	if($privacy_count > 0){
	//Update
	 $record = array();
	 $record["visible"] = (int)$visiblue;
	 $DB->AutoExecute( "fl_blog_privacy", $record, 'UPDATE', 'userid = '.(int)$id); 
	}else{
	//Insert
    $record["userid"]  = (int)$id; 
    $record["visible"] = (int)$visiblue; 
    $DB->AutoExecute("fl_blog_privacy", $record, 'INSERT'); 	
	}

}

function isFlataLogedin(){
	
	if($_SESSION["userId"] > 0){
	 	return true;	
	}else{
		return false;	
	}
}


function getFlataId(){
	
	return (int)$_SESSION["userId"];
	 
}


