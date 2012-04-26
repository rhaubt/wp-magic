<?php
add_action( 'template_redirect', 'bbo_template_redirect' );
     	function bbo_template_redirect()
		{
			if ( is_author() ||  is_archive() || is_tag() || is_category()) {
				include (TEMPLATEPATH . '/404.php');
				exit;		
			}
		}
?>