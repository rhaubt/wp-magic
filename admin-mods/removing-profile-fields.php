<?php
 //Removing unwanted fields
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
function add_twitter_contactmethod( $contactmethods ) {
  unset($contactmethods['aim']);
  unset($contactmethods['jabber']);
  unset($contactmethods['yim']);
  unset($contactmethods['yim']);
  unset($contactmethods['website']);
  unset($contactmethods['url']);
  unset($contactmethods['description']);
  return $contactmethods;
}
add_filter('user_contactmethods','add_twitter_contactmethod',10,1);
//Remove some more
add_action( 'admin_print_styles-profile.php', 'remove_profile_fields' );
add_action( 'admin_print_styles-user-edit.php', 'remove_profile_fields' );

?>