<?php
function remove_menus () {
global $menu;
		$restricted = array(__('Dashboard'), __('Media'), __('Settings'),__('Posts'), __('Links'), __('Appearance'), __('Tools'), __('Users'), __('Comments'), __('Plugins'));
		end ($menu);
		while (prev($menu)){
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
		}
}
add_action('admin_menu', 'remove_menus');
?>