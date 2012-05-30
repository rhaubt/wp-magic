<?php

class BBO_MailManager {

	/* Mail */
	function compose_mail($mail,$send = true) {
	    $use_html			= false;
		$subject			= $mail['subject'];
		$sender 			= $mail['sender'];
		$recipient 			= $mail['recipient'];
		$additional_headers = $mail['additional_headers'];

		if ( $use_html ) {
			$body = $mail['body'];
			$body = wpautop( $body );
		} else {
			$body = $mail['body'];
		}

		$attachments = array();
		//print_r($mail['files']);
		foreach ( (array) $mail['files'] as $path ) {
			if (empty( $path ))
				continue;
				
			$attachments[] = $path;
		}
		
		$headers = "From: $sender\n";

		if ( $use_html )
			$headers .= "Content-Type: text/html\n";

		$headers .= trim( $additional_headers ) . "\n";

		if ( $send ){
			return @wp_mail( $recipient, $subject, $body, $headers, $attachments );
		}
		return compact( 'subject', 'sender', 'body', 'recipient', 'headers', 'attachments' );
	}
}

?>