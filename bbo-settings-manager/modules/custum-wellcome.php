<?php


//Hiding wellcome panel so we can replace it with out own
add_action( 'load-index.php', 'hide_welcome_panel' );

function hide_welcome_panel() {
    $user_id = get_current_user_id();

    if ( 1 == get_user_meta( $user_id, 'show_welcome_panel', true ) )
        update_user_meta( $user_id, 'show_welcome_panel', 0 );
}


function remove_dashboard_widgets(){
  global$wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); 
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']); 
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);

}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function remove_some_wp_widgets(){
  unregister_widget('WP_Widget_Calendar');
  unregister_widget('WP_Widget_Search');
  unregister_widget('WP_Widget_Recent_Comments');
}

add_action('widgets_init','remove_some_wp_widgets', 1);


//Adding custum widget content
add_action('wp_dashboard_setup', 'bbo_add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.

function bbo_add_dashboard_widgets() {
	wp_add_dashboard_widget('bbo_dashboard_widget', 'BBO Dashboard Widget', 'bbo_dashboard_widget_function');
	
	// Globalize the metaboxes array, this holds all the widgets for wp-admin

	global $wp_meta_boxes;
	
	// Get the regular dashboard widgets array 
	// (which has our new widget already but at the end)

	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	
	// Backup and delete our new dashbaord widget from the end of the array

	$example_widget_backup = array('bbo_dashboard_widget' => $normal_dashboard['bbo_dashboard_widget']);
	unset($normal_dashboard['bbo_dashboard_widget']);

	// Merge the two arrays together so our widget is at the beginning

	$sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);

	// Save the sorted array back into the original metaboxes 

	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
} 


function bbo_dashboard_widget_function() {
	// Display whatever it is you want to show
	echo "Hello World, I'm a great Dashboard Widget";
} 




?>

