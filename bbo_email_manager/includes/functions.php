<?php
function bboSaveUploadToPost($id, $filename){
  $wp_upload_dir = wp_upload_dir();
  $wp_filetype = wp_check_filetype(basename($filename), null );
  $path = $wp_upload_dir['baseurl'] . _wp_relative_upload_path(BBOMM_UPLOAD_PATH. $filename );
  $attachment = array(
     'guid' =>  $path, 
     'post_mime_type' => $wp_filetype['type'],
     'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
     'post_content' => '',
     'post_status' => 'inherit'
  );
  $attach_id = wp_insert_attachment( $attachment, $path, $id );
  // you must first include the image.php file
  // for the function wp_generate_attachment_metadata() to work
  require_once(ABSPATH . 'wp-admin/includes/image.php');
  $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
  wp_update_attachment_metadata( $attach_id, $attach_data );
}


function bboMakeAttatcment(){
	$attach_id = wp_insert_attachment( $attachment, $file['file'], (int)$post_id );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	wp_update_attachment_metadata( $attach_id, $attach_data );
	clean_post_cache( (int)$post_id ); //Added this trying to debug
	// Also tried wp_update_post() on parent_post, but it did not fix
}


function bboMakePostOfEmail($email = ''){
$new_post = array(
    'post_title' => 'My New Post',
    'post_content' => 'Lorem ipsum dolor sit amet...',
    'post_status' => 'publish',
    'post_date' => date('Y-m-d H:i:s'),
    'post_author' => 0,
    'post_type' => 'email',
    'post_category' => array(0)
);
$post_id = wp_insert_post($new_post);	
return $post_id;
}

?>