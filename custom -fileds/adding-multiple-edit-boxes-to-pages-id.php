<?php
//Start Page ID
		$targetPageId = 28;
		$editors = array( array('id'=>1,'titel'=>"Name", 'height'=>"400" , 'extras'=>"extra"),
                    array('id'=>2,'titel'=>"Name2", 'height'=>"100" , 'extras'=>"extra"),
                    array('id'=>3,'titel'=>"Name3", 'height'=>"200" , 'extras'=>"extra")
             ); 
add_action('admin_init','bbo_post_tinymce');			 
function bbo_post_tinymce($post) {
	    global $targetPageId;
		global $editors;
	    if($_GET['post'] == $targetPageId){
			foreach($editors as $editor){
 				add_meta_box('bbo_post_tinymce'.$editor['id'], $editor['titel'], 'bbo_post_tinymce_setup', 'page', 'normal', 'high', $editor);
				}
		}
}
 
		function bbo_post_tinymce_setup($post, $metabox) {
			global $targetPageId;
			$editor = $metabox['args'];
			if($post->ID == $targetPageId){
		
				$content =  get_post_meta( $post->ID, '_wp_editor_'.$editor['id'], false );
				$settings = array(
					'quicktags' => array(
					'buttons' => 'em,strong,link', 
				),
				    'editor_css' => '<style>.bbo_edit_'.$editor['id'].'{height:'.$editor['height'].'px;}</style>',
					'editor_class' => 'bbo_edit_'.$editor['id'],
					'quicktags' => true,
					'tinymce' => true
				);
		
				wp_editor($content[0], '_wp_editor_'.$editor['id'], $settings);	
			}
		}
		
/* Do something with the data entered */
add_action( 'save_post', 'bbo_tinymce_save_postdata' );

/* When the post is saved, saves our custom data */
function bbo_tinymce_save_postdata( $post_id ) {

	// verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // Check permissions
  if ( ( isset ( $_POST['post_type'] ) ) && ( 'page' == $_POST['post_type'] )  ) {
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}		
  }
	else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

  // OK, we're authenticated: we need to find and save the data
  
    global $editors;		
	foreach($editors as $editor){
		if ( isset ( $_POST['_wp_editor_'.$editor['id']] ) ) {
			update_post_meta( $post_id, '_wp_editor_'.$editor['id'], $_POST['_wp_editor_'.$editor['id']] );
		}
	}
	
}

?>