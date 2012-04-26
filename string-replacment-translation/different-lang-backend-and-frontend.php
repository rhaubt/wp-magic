<?php

//Hooks in to the gettext hook, and simply returns the original land (eng) insted of the lang settings
function remove_frontend_translation( $translated, $original, $domain ) {
      return $original;
   // return $translated;
}
//UOnly applay this hook if we are on the frontend!
 if(!is_admin()){
add_filter( 'gettext', 'remove_frontend_translation', 10, 3 );
 }
 
 ?>