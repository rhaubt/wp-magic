<?php

//require_once( '/var/www/coorkonferens/wp-admin/admin.php' );
print_r($GLOBALS);
?>
<?php
if( ! class_exists( 'WP_Media_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-media-list-table.php' );
}
?>

<?php
class WP_Media_List_Table2 extends WP_Media_List_Table {
}

?>

<?php
  $myListTable = new WP_Media_List_Table2();
  echo '<div class="wrap"><h2>My List Table Test</h2>'; 
  $myListTable->prepare_items(); 

  $views = $myListTable->get_views();
  $myListTable->views();
  $myListTable->display(); 
  echo '</div>'; 
  echo "buu";
?>