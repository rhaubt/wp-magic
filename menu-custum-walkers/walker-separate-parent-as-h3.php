<?php
class Coor_Footer_Walker_Nav_Menu extends Walker_Nav_Menu {
    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param int $current_page Menu item ID.
     * @param object $args
     */
    
    protected $open = false;

    function start_el(&$output, $item, $depth, $args) {
        global $wp_query;           

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
        

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

                // new addition for active class on the a tag
                if(in_array('current-menu-item', $classes)) {
                    $attributes .= ' class="active"';
                }

        $item_output ="";
        if(!$this->open && $depth == 0){
            $item_output .= "<div class='small-list'>";
            $this->open = true;
        }

        //If custum class no-link exists only return html
        if(in_array('no-link', $classes))
        {
                $item_output .= $args->before;
                $item_output .= '<li>';
                $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                $item_output .= '</li>';
                $item_output .= $args->after;
        }else{
            if($depth < 1){
                $item_output .= $args->before;
                $item_output .= '<h3'. $attributes .'>';
                $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                $item_output .= '</h3>';
                $item_output .= $args->after;
            }else{
                $item_output .= $args->before;
                $item_output .= '<li><a'. $attributes .'>';
                $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                $item_output .= '</a></li>';
                $item_output .= $args->after;
            }

        }

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

     function end_el(&$output, $item, $depth, $args){
        $item_output ="";
        if($depth == 0 && $this->open){
            $item_output .= "</div>";
            $this->open = false;
        }

        $output .= apply_filters( 'walker_nav_menu_end_el', $item_output, $item, $depth, $args );
     }


    // function start_lvl(&$output, $depth=0, $args=array()) {

   
    // }

    // function end_lvl(&$output, $depth=0, $args=array()) {

    // }
}

?>