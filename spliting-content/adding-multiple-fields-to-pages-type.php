<?php
//Start Page ID
		$targetPostType = "personal";
		$fields = array( array('id'=>"name",'titel'=>"Display Namn", 'height'=>"400" , 'extras'=>"extra"),
                    array('id'=>"tagline",'titel'=>"Tagline", 'height'=>"100" , 'extras'=>"extra"),
                    array('id'=>"tele",'titel'=>"Tele", 'height'=>"200" , 'extras'=>"extra"),
					array('id'=>"fax",'titel'=>"Fax", 'height'=>"200" , 'extras'=>"extra"),
					array('id'=>"epost",'titel'=>"Epost", 'height'=>"200" , 'extras'=>"extra")
					
             ); 
add_action('admin_init','bbo_post_tinymce');			 
function bbo_post_tinymce($post) {
	    global $targetPostType;
		global $fields;
			foreach($fields as $fields){
 				add_meta_box('bbo_post_tinymce'.$fields['id'], $fields['titel'], 'bbo_post_tinymce_setup', $targetPostType, 'normal', 'high', $fields);
				}
}
 
		function bbo_post_tinymce_setup($post, $metabox) {
			global $targetPostType;
			$fields = $metabox['args'];
			if($post->post_type == $targetPostType){
				
$content =  get_post_meta( $post->ID, '_bbo_meta_'.$fields['id'], false );
			  ?>
              <input name="<?php echo '_bbo_meta_'.$fields['id']; ?>" id="<?php echo '_bbo_meta_'.$fields['id']; ?>"  value="<?php echo $content[0]; ?>"/>
              <?php
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

  // OK, we're authenticated: we need to find and save the data
  
    global $fields;		
	foreach($fields as $fields){
		if ( isset ( $_POST['_bbo_meta_'.$fields['id']] ) ) {
			update_post_meta( $post_id,  '_bbo_meta_'.$fields['id'], $_POST['_bbo_meta_'.$fields['id']] );
		}
	}


}

?>