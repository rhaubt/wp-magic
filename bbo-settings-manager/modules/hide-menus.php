<?php

$restricted =   array();
$nodes = array();
$theme_sub_menus = array();
$settings_sub_menus = array();
$menu_nodes = array();

if("true" == get_option("rename-post")){
	add_action( 'init', 'change_post_object_label' );
	add_action( 'admin_menu', 'change_post_menu_label' );
}




if("true" == get_option("hide-profile")){
 $menu_nodes[] ="edit-profile";
}

if("true" == get_option("hide-dashbord")){
  $restricted[] = __('Dashboard');
}

if("true" == get_option("hide-posts")){
  $restricted[] = __('Posts');
  $nodes[] = "new-post";
}


if("true" == get_option("hide-media"))
{
  $restricted[] = __('Media');
  $nodes[] = "new-media";
}

if("true" == get_option("hide-links")){
	$restricted[] = __('Links');
	$nodes[] = "new-link";
}
if("true" == get_option("hide-pages")){
	$restricted[] = __('Pages');
	$nodes[] = "new-page";
	
}
if("true" == get_option("hide-comments")){
	$restricted[] = __('Comments');
}
if("true" == get_option("hide-appearence")){
	$restricted[] = ('Appearance');
}
if("true" == get_option("hide-appearence-themes")){
$theme_sub_menus[] = "themes.php";
}
if("true" == get_option("hide-appearence-widgets")){
$theme_sub_menus[] = "widgets.php";
}
if("true" == get_option("hide-appearence-menus")){
$theme_sub_menus[] = "nav-menus.php";
}
if("true" == get_option("hide-appearence-editor")){
$theme_sub_menus[] = "theme-editor.php";
}
if("true" == get_option("hide-plugins")){
$restricted[] = __('Plugins');
}
if("true" == get_option("hide-users"))
{
$restricted[] = __('Users');
$nodes[] = "new-user";
}
if("true" == get_option("hide-tools")){
$restricted[] = __('Tools');}
if("true" == get_option("hide-settings")){
$restricted[] = __('Settings');}
if("true" == get_option("hide-settings-general")){
$settings_sub_menus[] ="options-general.php";
}
if("true" == get_option("hide-settings-writing")){
$settings_sub_menus[] ="options-writing.php";
}
if("true" == get_option("hide-settings-reading")){
$settings_sub_menus[] ="options-reading.php";
}
if("true" == get_option("hide-settings-discussion")){
$settings_sub_menus[] ="options-discussion.php";
}
if("true" == get_option("hide-settings-media")){
$settings_sub_menus[] ="options-media.php";
}
if("true" == get_option("hide-settings-privacy")){
$settings_sub_menus[] ="options-privacy.php";
}
if("true" == get_option("hide-settings-permalink")){
$settings_sub_menus[] ="options-permalink.php";
}
if("true" == get_option("hide-bbosettings")){}


function delete_themes_submenu_items() {
 global $theme_sub_menus;
			foreach($theme_sub_menus as $id)
			{
			 remove_submenu_page('themes.php', $id);
			}
}
add_action( 'admin_init', 'delete_themes_submenu_items');

function delete_settings_submenu_items() {
 global $settings_sub_menus;

			foreach($settings_sub_menus as $id)
			{
			 remove_submenu_page('options-general.php', $id);
			}
}
add_action( 'admin_init', 'delete_settings_submenu_items');


function remove_nodes()
		{
	       global $nodes;
	       global $wp_admin_bar;
			foreach($nodes as $id)
			{
			 $wp_admin_bar->remove_node($id);
			}
		}
add_action('wp_before_admin_bar_render','remove_nodes');

function remove_menu_nodes()
		{
	       global $menu_nodes;
	       global $wp_admin_bar;
			foreach($menu_nodes as $id)
			{
			 $wp_admin_bar->remove_menu($id);
			}
		}
add_action('wp_before_admin_bar_render','remove_menu_nodes');




function remove_menus () {
global $menu;
global $restricted;
		end ($menu);
		while (prev($menu)){
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
		}
}
add_action('admin_menu', 'remove_menus');


function change_post_menu_label() {
	global $menu;
	global $submenu;
        $newname = get_option("rename-post-name");

	$menu[5][0] = $newname;
	$submenu['edit.php'][5][0] = $newname;
	$submenu['edit.php'][10][0] = 'Add '.$newname;
	$submenu['edit.php'][16][0] = $newname.' Tags';
	echo '';
}
function change_post_object_label() {
        
	global $wp_post_types;
        $newname = get_option("rename-post-name");
	$labels = &$wp_post_types['post']->labels;
	$labels->name = $newname;
	$labels->singular_name = $newname;
	$labels->add_new = 'Add '.$newname;
	$labels->add_new_item = 'Add '.$newname;
	$labels->edit_item = 'Edit '.$newname;
	$labels->new_item = $newname;
	$labels->view_item = 'View '.$newname;
	$labels->search_items = 'Search '.$newname;
	$labels->not_found = 'No '.$newname.' found';
	$labels->not_found_in_trash = 'No '.$newname.' found in Trash';
}

?>
