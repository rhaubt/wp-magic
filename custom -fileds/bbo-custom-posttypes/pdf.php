<?php
add_action('admin_init', 'smille_field_press_filds');
add_action('save_post',"smille_insert_press");
add_action('init', 'smille_field_add_press_types');



function smille_field_add_press_types() {
	 // Register custom post types
		register_post_type('medlemsfiler', array(
			'label' => __('Medlems pdf'),
			'singular_label' => __('medlemsfiler'),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => "medlemsfiler"), // Permalinks
			'query_var' => "medlemsfiler", // This goes to the WP_Query schema
			'supports' => array('title','author',  'editor'/*'excerpt', ,'custom-fields'*/) // Let's use custom fields for debugging purposes only
		));
		
		
		register_taxonomy(
		'medlemsfiler_cats',		// internal name = machine-readable taxonomy name
		'medlemsfiler',		// object type = post, page, link, or custom post-type
		array(
			'hierarchical' => true,
			'label' => 'Categories',	// the human-readable taxonomy name
			'query_var' => true,	// enable taxonomy-specific querying
			'rewrite' => array( 'slug' => 'medlemsfiler' ),	// pretty permalinks for your taxonomy?
		)
	);

};

// When a post is inserted or updated
   $meta_press_fields = array("press_info","ingredienser","smille-field-1");


	function smille_insert_press($post_id, $post = null)
	{
	
	if(!checkSaveCall()){return $post_id;}
	
		global $post;
		global $meta_press_fields;
		$debugger;
       //print_r($_POST['press_info']);
      // echo"////";
      // print_r($_POST['ingredienser']);
     // break;
       	// Loop through the POST data
		if ($post->post_type == "medlemsfiler")
		{
			
			 foreach($meta_press_fields as $key)
			{
				$debugger[] = "kör loopen";
				$debugger[] = $key;
				$value = @$_POST[$key];
				if (empty($value))
				{
					delete_post_meta($post_id, $key);
					continue;
				}

				// If value is a string it should be unique
				if (!is_array($value))
				{
					$debugger[] = "är ingen array";
					// Update meta
					if (!update_post_meta($post_id, $key, $value))
					{
						// Or add the meta data
						add_post_meta($post_id, $key, $value);
					}
				}
				else
				{
					
					$debugger[] = "är en array";
					// If passed along is an array, we should remove all previous data
					delete_post_meta($post_id, $key);
					$value = array_remove_empty($value);
					$value = serialize($value);
					if (!update_post_meta($post_id, $key, $value))
					{
						// Or add the meta data
						add_post_meta($post_id, $key, $value);
						
					}
					$debugger[] = "latill datan";
					$debugger[] = $value;
					// Loop through the array adding new values to the post meta as different entries with the same name
					//foreach ($value as $entry)
						//add_post_meta($post_id, $key, $entry);
				}
			}
			//print_r($debugger);
			//break;
		}
	}
	


function smille_field_press_filds()
{
	add_meta_box("medlemsfiler_fields-meta", "Extra Info", "smille_field_add_press_filds", "medlemsfiler", "normal", "high");

}

function smille_field_add_press_filds()
{
	global $post;
	global $wpdb; 
	echo '<style type="text/css">#postdivrich{display:none;}</style>';
		
		$custom   =  get_post_custom($post->ID);
		//print_r($custom);
		
		$press_info  =  $custom["press_info"][0];
		//print_r($press_info );
		$press_info  = unserialize(unserialize($press_info));
		
		//print_r($press_info);
		
		$file_id  =  $custom["smille-field-1"][0];
		$bild_poss  =  $custom["post-bild-poss"][0];
		$field_unique_id = "smille-field-1";
		$field_name      = "smille-field-1";
		$image_thumbnail = wp_get_attachment_image_src( $file_id, 'thumbnail', true );
		$image_thumbnail = $image_thumbnail[0];
		$image_html = "<img src='$image_thumbnail' alt='' />";
		$file_name = rawurlencode(get_the_title($file_id));
		echo "<h2>Ladda upp PDF</h2>";
		echo "<div id='admin-mat-thumb'>";
		echo "<div class='simple-fields-metabox-field-file'>";
						echo "<label style='display:none;'>{$file_name}</label>";
						echo "<div class='simple-fields-metabox-field-file-col1'>";
							echo "<div class='simple-fields-metabox-field-file-selected-image'>$image_html</div>";
						echo "</div>";
						echo "<div class='simple-fields-metabox-field-file-col2'>";
							echo "<input type='hidden' class='text simple-fields-metabox-field-file-fileID' name='$field_name' id='$field_unique_id' value='$file_id' />";
							echo "<div class='simple-fields-metabox-field-file-selected-image-name'>$image_name</div>";

							$field_unique_id_esc = rawurlencode($field_unique_id);
							#$file_url = "media-upload.php?simple_fields_dummy=1&simple_fields_action=select_file&simple_fields_file_field_unique_id=$field_unique_id_esc&post_id=$post_id&TB_iframe=true";
							// xxx
							$file_url = "media-upload.php?fileSmillaDummy=1&fileSmilla_action=select_file&simple_fields_file_field_unique_id=$field_unique_id_esc&post_id=".$post->ID."&TB_iframe=true";
							//$file_url = "media-upload.php?simple_fields_dummy=1&simple_fields_action=select_file&simple_fields_file_field_unique_id=$field_unique_id_esc&post_id=-1&TB_iframe=true";
							echo "<a class='thickbox simple-fields-metabox-field-file-select' href='$file_url'>Select file</a>";
							
							echo " | <a href='#' class='simple-fields-metabox-field-file-clear'>Clear</a>";
						echo "</div>";
		echo "</div>";
		echo "</div>";
		}

?>
