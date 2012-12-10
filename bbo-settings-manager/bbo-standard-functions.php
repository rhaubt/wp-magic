<?php

add_action('init', 'bbo_add_textblock_posttype');
function bbo_add_textblock_posttype() {
	 // Register custom post types
		register_post_type('textblock', array(
			'label' => __('textblocks'),
			'singular_label' => __('textblock'),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'page',
			'hierarchical' => false,
			'rewrite' => array("slug" => "textblocks"), // Permalinks
			'query_var' => "textblock", // This goes to the WP_Query schema
			'supports' => array('title', 'editor' /*'author', 'excerpt',,'custom-fields'*/) // Let's use custom fields for debugging purposes only
		));
		
		
		register_taxonomy(
		'textblocks_cats',		// internal name = machine-readable taxonomy name
		'textblocks',		// object type = post, page, link, or custom post-type
		array(
			'hierarchical' => true,
			'label' => 'Categories',	// the human-readable taxonomy name
			'query_var' => true,	// enable taxonomy-specific querying
			'rewrite' => array( 'slug' => 'textblocks' ),	// pretty permalinks for your taxonomy?
		)
	);

};

?>