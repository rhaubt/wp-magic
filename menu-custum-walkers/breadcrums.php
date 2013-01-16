<?php
class SH_BreadCrumbWalker extends Walker{
    /**
     * @see Walker::$tree_type
     * @var string
     */
    var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

    /**
     * @see Walker::$db_fields
     * @var array
     */
    var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

    /**
     * delimiter for crumbs
     * @var string
     */
    var $delimiter = ' / ';

    /**
     * @see Walker::start_el()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item.
     * @param int $current_page Menu item ID.
     * @param object $args
     */
    function start_el(&$output, $item, $depth, $args) {

        //Check if menu item is an ancestor of the current page
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $current_identifiers = array( 'current-menu-item', 'current-menu-parent', 'current-menu-ancestor' ); 
        $ancestor_of_current = array_intersect( $current_identifiers, $classes );     


        if( $ancestor_of_current ){
            $title = apply_filters( 'the_title', $item->title, $item->ID );

            //Preceed with delimter for all but the first item.
            if( 0 != $depth )
                $output .= $this->delimiter;

            //Link tag attributes
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

            //Add to the HTML output
            if(!in_array('current-menu-item', $classes)){
                  $output .= '<a'. $attributes .'>'.$title.'</a>';
              }else{
                  $output .= $title;
              }       
        }
    }
}

?>