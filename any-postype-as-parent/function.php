<?php
  add_action('admin_menu', function() { remove_meta_box('pageparentdiv', 'coor_konf', 'normal');});
  add_action('add_meta_boxes', function() { add_meta_box('chapter-parent', 'coor_konf', 'chapter_attributes_meta_box', 'coor_konf', 'side', 'high');});
  function chapter_attributes_meta_box($post) {
    $post_type_object = get_post_type_object($post->post_type);
    if ( $post_type_object->hierarchical ) {
      $pages = wp_dropdown_pages(array('post_type' => 'page', 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('(no parent)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
      if ( ! empty($pages) ) {
        echo $pages;
      } // end empty pages check
    } // end hierarchical check.
  }
  

?>