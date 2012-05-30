<?php
/* Custom Post Type: Contact filetype */
add_action( 'init', 'bbo_contact_register_post_types' );

function bbo_contact_register_post_types() {
	$args = array(
		'label' => __('Eposts'),
			'singular_label' => __('Email'),
			'public' => false,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => "email"), // Permalinks
			'query_var' => "email", // This goes to the WP_Query schema
			'supports' => array('title','author', 'editor'/*'excerpt', ,'custom-fields'*/) // Let's use custom fields for debugging purposes only
	);
	register_post_type( 'email', $args );
}

function getAttachmentSetting(){
	 return true;	
}

function getPostSetting(){
	return true;
}

function getAutoResponder(){
	 return "";		
}

function getMessage(){
	return "";		
}

function getRecipient(){
	return "";		
}

function getFileTypes(){
	
}


?>