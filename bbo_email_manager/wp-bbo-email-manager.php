<?php
/*
Plugin Name: BBO Email Manager
Plugin URI: http://bebetteronline.com/
Description: Contact form plugin, flexible but need theme integration.
Author: Robin Wesrerlundh
Author URI: http://thisoneisgreen.com/
Version: 0.1
*/

if ( ! defined( 'BBOMM_UPLOAD_PATH' ) )
	define( 'BBOMM_UPLOAD_PATH', '/bbo_mailer/');
	
if ( ! defined( 'BBOMM_PLUGIN_BASENAME' ) )
	define( 'BBOMM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'BBOMM_PLUGIN_NAME' ) )
	define( 'BBOMM_PLUGIN_NAME', trim( dirname( BBOMM_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'BBOMM_PLUGIN_DIR' ) )
	define( 'BBOMM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BBOMM_PLUGIN_NAME );

if ( ! defined( 'BBOMM_PLUGIN_URL' ) )
	define( 'BBOMM_PLUGIN_URL', WP_PLUGIN_URL . '/' . BBOMM_PLUGIN_NAME );

if ( ! defined( 'BBOMM_AUTOP' ) )
	define( 'BBOMM7_AUTOP', true );

if ( ! defined( 'BBOMM_USE_PIPE' ) )
	define( 'BBOMM_USE_PIPE', true );

require_once BBOMM_PLUGIN_DIR . '/settings.php';
require_once BBOMM_PLUGIN_DIR . '/includes/classes.php';
require_once BBOMM_PLUGIN_DIR . '/includes/functions.php';
require_once BBOMM_PLUGIN_DIR . '/tasks/file.php';

	function bboEmailManager()
	{
		$mailer = new  BBO_MailManager();
		$message = array();
		
	        $results = bbo_upload('file');

			$mail = array(
		   "subject" => "Tetsing",
		   "sender" => "robin@westerlundh.se",
		   "recipient" => "entropin@gmail.com",
		   "additional_headers" => "",
		   "body" => "tetsing wp mail function",
		   "files" => array($results['uri'])
			);
            bboSaveUploadToPost(bboMakePostOfEmail(), $results['filename']);
	        $mailer->compose_mail($mail);		
	}

	function echoBaseForm($action = '', $extra = '')
	{
	?>
    <form method="post" action="<?php echo $action; ?>"> 
    <?php echo $extra; ?>
    </form>
    <?php	
	}

?>