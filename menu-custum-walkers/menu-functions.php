<?php
function submenu_limit( $items, $args ) {

    if (empty($args->submenu) )
        return $items;

    $parent_id = array_pop( wp_filter_object_list( $items, array( 'title' => $args->submenu ), 'and', 'ID' ) );
    $children  = submenu_get_children_ids( $parent_id, $items );
    //$tt = get_post_meta($parent_id, '_menu_item_menu_item_parent', true );
    //echo $tt;
    foreach ( $items as $key => $item ) {

        if ( ! in_array( $item->ID, $children ) ) 
            unset($items[$key]);
    }

    return $items;
}

function submenu_get_children_ids( $id, $items ) {

    $ids = wp_filter_object_list( $items, array( 'menu_item_parent' => $id ), 'and', 'ID' );

    foreach ( $ids as $id ) {
        $ids = array_merge( $ids, submenu_get_children_ids( $id, $items ) );
    }

    return $ids;
}

function get_menu_id_for_post($post_id) {

  global $wpdb;
  $query = $wpdb->prepare(
    "
      SELECT post_id 
      FROM $wpdb->postmeta 
      WHERE meta_key = '_menu_item_object_id' 
      AND meta_value = %s
    ", 
    $post_id
  );
  $menu_id = $wpdb->get_var($query);

  return $menu_id;
}


function get_post_id_for_menu($menu_id) {

  global $wpdb;
  $query = $wpdb->prepare(
    "
      SELECT meta_value
      FROM $wpdb->postmeta 
      WHERE meta_key = '_menu_item_object_id' 
      AND post_id = %s
    ", 
    $menu_id
  );
  $post_id = $wpdb->get_var($query);

  return $post_id;
}


function get_menu_top_post($id) {

  global $wpdb;
  while($id != 0){
    echo $id;
    $query = $wpdb->prepare(
      "
        SELECT post_id 
        FROM $wpdb->postmeta 
        WHERE meta_key = '_menu_item_menu_item_parent' 
        AND meta_value = %s
      ", 
      $id
    );

    $id = $wpdb->get_var($query);
  }

  return $id;
}

/*
To displaying the corrent menu!
*/

function getMenuMap($menu_name = 'main_menu'){

    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
    $menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );

   
      $count = 0;
      $array = array();
      $topParrent = 0;
      $parent_id = 0;
      foreach( $menuitems as $item ):
          // get page id from using menu item object id
          $id        = get_post_meta( $item->ID, '_menu_item_object_id', true );
        $parent    = get_post_meta( $item->menu_item_parent, '_menu_item_object_id', true ); 

          // set up a page object to retrieve page data
          $page = get_page( $id );
          $link = get_page_link( $id );
          // item does not have a parent so menu_item_parent equals 0 (false)
          if ( !$item->menu_item_parent ):
        $parent_id = $item->ID;
        $topParrent = $id; 
        $parent  = 0;

          endif;
               $array[$id]['item'] = array('menuid'=>$item->ID, 'title'=> $page->post_title, "parent" => $parent, "topparrent" => $topParrent);

      
    
  $count++;
  endforeach;

return $array;
}


function getTopLeverMenuPage($id, $menu_name = 'main_menu'){
    global $post;
    $array = getMenuMap('main_menu');
    if(isset($array[$id])){
    $topPost = get_post($array[$id]['item']['topparrent']);
      return $topPost;
    }else{
      return $post;
    }
}

?>