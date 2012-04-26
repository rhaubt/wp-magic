<?php
/*
Plugin Name: bbo-custum-posttypes	
Plugin URI: http://thisoneisgreen.com/
Description: Lägger till de extra posttyperna vi behöver
Version: 0.0.1
Author: Robin Westerlundh
Author URI: http://thisoneisgreen.com/
*/
?>
<?php
define( "BBO_POSTS_URL", WP_PLUGIN_URL . '/bbo-custom-posttypes/');
add_action('admin_head', 'add_admin_styles');
add_action( 'dbx_post_sidebar', 'smille_fields_post_dbx_post_sidebar' );

function smille_fields_post_dbx_post_sidebar() {
	?>
	<input type="hidden" name="smille_fields_nonce" id="smille_fields_nonce" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" />
	<?php
}

// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
function checkSaveCall()
{	if ( !wp_verify_nonce( $_POST['smille_fields_nonce'], plugin_basename(__FILE__) )) {
		return false;
	}
	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) { return false; }
	
	    return true;
}
	
/*
 * Broswes add hooks and behavior of the file picker
 * */
include("browser.php");
include("pdf.php");
include("video.php");
// Fire this during init
function add_admin_styles(){
	?>	
	<script type="text/javascript" src="<?php echo BBO_POSTS_URL ?>scripts.js"></script>
	<script type="text/javascript" src="<?php echo BBO_POSTS_URL ?>smille-script.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo BBO_POSTS_URL ?>styles.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BBO_POSTS_URL ?>simple-styles.css" />
	<?php
}

function array_remove_empty($arr){
    $narr = array();
    while(list($key, $val) = each($arr)){
        if (is_array($val)){
            $val = array_remove_empty($val);
            // does the result array contain anything?
            if (count($val)!=0){
                // yes :-)
                $narr[$key] = $val;
            }
        }
        else {
            if (trim($val) != ""){
                $narr[$key] = $val;
            }
        }
    }
    unset($arr);
    return $narr;
}


?>
