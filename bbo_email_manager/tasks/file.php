<?php

function bbo_upload($name='file',$file_type_pattern = '') {
	$result = array();
	$result['uri'] = null;
	$result['filename'] = null;
	$file   = isset( $_FILES[$name] ) ? $_FILES[$name] : null;

	if ( $file['error'] && UPLOAD_ERR_NO_FILE != $file['error'] ) {
		$result['valid'] = false;
		$result['reason'][$name] = 'upload_failed_php_error';
		return $result;
	}

	if ( empty( $file['tmp_name'] )) {
		$result['valid'] = false;
		$result['reason'][$name] = 'invalid_required';
		return $result;
	}

	if ( ! is_uploaded_file( $file['tmp_name'] ) )
		return $result;

	$allowed_size = 1048576; // default size 1 MB

	/* File type validation */
	// Default file-type restriction
	if($file_type_pattern == ''){
    $file_type_pattern = 'jpg|jpeg|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv';
	}

	$file_type_pattern = trim( $file_type_pattern, '|' );
	$file_type_pattern = '(' . $file_type_pattern . ')';
	$file_type_pattern = '/\.' . $file_type_pattern . '$/i';

	if ( ! preg_match( $file_type_pattern, $file['name'] ) ) {
		$result['valid'] = false;
		$result['reason'][$name] = 'upload_file_type_invalid';
		return $result;
	}

	/* File size validation */

	if ( $file['size'] > $allowed_size ) {
		$result['valid'] = false;
		$result['reason'][$name] = "file to big";
		return $result;
	}

	$uploads_dir = bbomm_upload_tmp_dir();
	bbomm_init_uploads(); // Confirm upload dir

	$filename = $file['name'];

	// If you get script file, it's a danger. Make it TXT file.
	if ( preg_match( '/\.(php|pl|py|rb|cgi)\d?$/', $filename ) )
		$filename .= '.txt';

	$filename = wp_unique_filename( $uploads_dir, $filename );

	$new_file = trailingslashit( $uploads_dir ) . $filename;

	if ( false === @move_uploaded_file( $file['tmp_name'], $new_file ) ) {
		$result['valid'] = false;
		$result['reason'][$name] = 'upload_failed' ;
		return $result;
	}

	// Make sure the uploaded file is only readable for the owner process
	//@chmod( $new_file, 0400 );
    $result['uri'] = $new_file;
	$result['filename'] = $filename;
	return $result;
}

/* File uploading functions */

function bbomm_init_uploads() {
	
	$dir = bbomm_upload_tmp_dir();
	wp_mkdir_p( trailingslashit( $dir ) );
	return true;
	//We dont need to block the folder right now
	@chmod( $dir, 0733 );

	$htaccess_file = trailingslashit( $dir ) . '.htaccess';
	if ( file_exists( $htaccess_file ) )
		return;

	if ( $handle = @fopen( $htaccess_file, 'w' ) ) {
		fwrite( $handle, "Deny from all\n" );
		fclose( $handle );
	}
}

function bbomm_cleanup_upload_files() {

	$dir = trailingslashit( bbomm_upload_tmp_dir() );

	if ( ! is_dir( $dir ) )
		return false;
	if ( ! is_readable( $dir ) )
		return false;
	if ( ! is_writable( $dir ) )
		return false;

     //We dont need to block the folder right now
	if ( $handle = @opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( $file == "." || $file == ".." || $file == ".htaccess" )
				continue;

			$stat = stat( $dir . $file );
			if ( $stat['mtime'] + 60 < time() ) // 60 secs
				@unlink( $dir . $file );
		}
		closedir( $handle );
	}
}

if ( ! is_admin() && 'GET' == $_SERVER['REQUEST_METHOD'] )
	bbomm_cleanup_upload_files();
	
	
function bbomm_upload_tmp_dir(){

return $dir = WP_CONTENT_DIR . '/uploads'.BBOMM_UPLOAD_PATH;

}

?>