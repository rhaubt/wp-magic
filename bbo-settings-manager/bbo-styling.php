<?php
 /**
     * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
     */

    add_action('admin_enqueue_scripts','bbo_admin_stylesheet');

    /**
     * Enqueue plugin style-file
     */
    function bbo_admin_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'bbo-style', plugins_url('bbo-style.css', __FILE__) );
        wp_enqueue_style( 'bbo-style','' ,array('colors'), 'version', true);
    }

    //Dont want em to remove my nice bbo style
    if(is_admin())
    {
	  remove_action("admin_color_scheme_picker", "admin_color_scheme_picker");
    }

//Lets hide the about wordpress page, and give em an nice way home to the dashbord
add_action('wp_before_admin_bar_render','change_wp_logo');
function change_wp_logo()
	{
	     global $wp_admin_bar;
	     $logo = $wp_admin_bar->get_node('wp-logo');
	     $logo->href = get_bloginfo('url')."/wp-admin/";
	     $logo->meta = array('title' => 'BBO Admin') ;
	     $wp_admin_bar->add_node($logo);
	}


?>
