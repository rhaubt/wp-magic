<?php
/*
Plugin Name: Name Of The Plugin
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Name Of The Plugin Author
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
?>
<?php

$siteurl = get_option('siteurl');
define('BBO_SETTINGS_FOLDER', dirname(plugin_basename(__FILE__)));
define('BBO_SETTINGS_URL', $siteurl.'/wp-content/plugins/' . BBO_SETTINGS_FOLDER);
include("bbo-styling.php");
include("modules/custum-wellcome.php");

/*
//Helper for future editing
For Options

add_options_page(page_title, menu_title, access_level/capability, file, [function]);
For Management

add_management_page(page_title, menu_title, access_level/capability, file, [function]);
For Presentation

add_theme_page(page_title, menu_title, access_level/capability, file, [function]);

Write: add_submenu_page(‘post-new.php’,…)
Manage: add_submenu_page(‘edit.php’,…)
Design: add_submenu_page(‘themes.php’,…)
Comments: add_submenu_page(‘edit-comments.php’,…)
Settings: add_submenu_page(‘options-general.php’,…)
Plugins: add_submenu_page(‘plugins.php’,…)
Users: add_submenu_page(‘users.php’,…)
*/


add_action('admin_menu','bbo_admin_menu');

function bbo_admin_menu() { 
	add_menu_page(
		"BBO Settings",
		"BBO Settings",
		8,
		__FILE__,
		"bbo_admin_settings"/*,
		BBO_SETTINGS_URL."/img/skruv-small.png"*/
	); 
	add_submenu_page(__FILE__,'Content Matching','Content Matching','8','content-matching','bbo_admin_content_matching');
}

function bbo_admin_settings(){
	include("pages/settings-page.php");
}

function bbo_admin_content_matching(){
	include("pages/content-page.php");
}
