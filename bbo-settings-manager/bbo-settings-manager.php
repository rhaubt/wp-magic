<?php
/*
Plugin Name: BBO Settings Manager
Plugin URI: http://bebetteronline.com
Description: Be Better Onlines, wordpress frameworke.
Version: 1.0
Author: Robin Westerlundh
Author URI: http://robinwesterlundh.se
License: GPL2
*/
?>
<?php

$siteurl = get_option('siteurl');
define('BBO_SETTINGS_FOLDER', dirname(plugin_basename(__FILE__)));
define('BBO_SETTINGS_URL', $siteurl.'/wp-content/plugins/' . BBO_SETTINGS_FOLDER);
include("bbo-styling.php");
include("modules/custum-wellcome.php");
include("modules/hide-menus.php");

include("modules/browser.php");

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
		"settings-page",
		"bbo_admin_settings"/*,
		BBO_SETTINGS_URL."/img/skruv-small.png"*/
	); 
	add_submenu_page('settings-page','Content Matching','Content Matching','8','content-page','bbo_admin_content_matching');
}

function bbo_admin_settings(){
	include("pages/settings-page.php");
}

function bbo_admin_content_matching(){
	include("pages/content-page.php");
}
