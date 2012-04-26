<?php
function string_replace( $translated, $original, $domain ) {
    // This is an array of original strings
    // and what they should be replaced with
    $strings = array(
       'username %s' => 'Usernames %s',
       'Howdy, %1$s' => 'Greetings, %1$s!',
        // Add some more strings here
    );

    // See if the current string is in the $strings array
    // If so, replace it's translation
    if ( isset( $strings[$original] ) ) {
        // This accomplishes the same thing as __()
        // but without running it through the filter again
        $translations = &get_translations_for_domain( $domain );
        $translated = $translations->translate( $strings[$original] );
    }
    return $translated;
}

add_filter( 'gettext', 'string_replace', 10, 3 );
 
 ?>