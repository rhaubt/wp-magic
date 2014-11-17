<?php
add_action( 'wpcf7_before_send_mail', 'my_conversion' );
 
function my_conversion( $cf7 )
{
 //$data['your_phone'];
$submission = WPCF7_Submission::get_instance();
$data =& $submission->get_posted_data();

$cf7->skip_mail = true;
//Your codes go here
}


add_filter( 'wpcf7_validate_text', 'your_validation_filter_func', 10, 2 );
add_filter( 'wpcf7_validate_text*', 'your_validation_filter_func', 10, 2 );

add_filter( 'wpcf7_validate_tel', 'your_validation_filter_func', 10, 2 );
add_filter( 'wpcf7_validate_tel*', 'your_validation_filter_func', 10, 2 );

function your_validation_filter_func( $result, $tag ) {
	$type = $tag['type'];
	$name = $tag['name'];


	//if ( 'your-id-number-field' == $name ) {
		if ( $result['valid'] ) {
			$result['valid'] = false;
			$number = fixNumber($_POST['your_phone']);
			$result['reason'][$name] = "Error message here:{$number}";
			
		}
	//}

	return $result;
}



function fixNumber($string) {

	$string = str_replace("+46", "0", $string);
	$string = str_replace("0046", "0", $string);
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "", $string);
    return $string;
}
?>
