<?php

add_action('init', 'smille_field_add_video_types');



function smille_field_add_video_types() {
	 // Register custom post types
		register_post_type('video', array(
			'label' => __('videos'),
			'singular_label' => __('video'),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => "video"), // Permalinks
			'query_var' => "video", // This goes to the WP_Query schema
			'supports' => array('title','author',  'editor'/*'excerpt', ,'custom-fields'*/) // Let's use custom fields for debugging purposes only
		));
		
		register_taxonomy(
		'medlemsfiler_cats',		// internal name = machine-readable taxonomy name
		'medlemsfiler',		// object type = post, page, link, or custom post-type
		array(
			'hierarchical' => true,
			'label' => 'Categories',	// the human-readable taxonomy name
			'query_var' => true,	// enable taxonomy-specific querying
			'rewrite' => array( 'slug' => 'video' ),	// pretty permalinks for your taxonomy?
		)
	);

};

?>
