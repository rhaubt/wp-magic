<?php
/**
*Preventing all admin accsess
*/
function prevent_admin_access() {
    if (strpos(strtolower($_SERVER['REQUEST_URI']), '/wp-admin') !== false && !current_user_can('Administrator')) {
        wp_redirect(get_option('siteurl'));
    }
}
add_action('init', 'prevent_admin_access', 0);

//Hiding admin bar
show_admin_bar( false );
?>