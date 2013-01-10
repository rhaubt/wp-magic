<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
?>

<?php
class BBO_Media_List_Table extends WP_List_Table {


function __construct() {
  $this->printStyle();
  parent::__construct();
 
}


function printStyle(){
  echo '<style type="text/css">';
  echo '.wp-list-table .column-ID { width: 5%; }';
  echo '.wp-list-table .column-thumbnail { width: 5%; }';
  echo '.wp-list-table .column-gallerytitle { width: 40%; }';
  echo '.wp-list-table .column-numbers { width: 20%; }';
  echo '.wp-list-table .column-extra { width: 20%; }';
  echo '</style>';

}

  var $view_data = array();


function setData($array){

    if(is_array($array))
        $this->view_data = $array;
}



function get_columns(){
  $columns = array(
    'ID' => 'ID',
    'thumbnail' => '',
    'gallerytitle' => 'Title',
    'numbers'      => 'Number Of Photos',
    'extra'        => 'Extra'
  );
  return $columns;
}


function column_default( $item, $column_name ) {
  switch( $column_name ) { 
    case 'ID':
    case 'thumbnail':
    case 'gallerytitle':
    case 'numbers':
    case 'extra':
      return $item[ $column_name ];
    default:
      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
  }
}


function prepare_items() {
  $columns = $this->get_columns();
  $hidden = array();
  $sortable = array();
  $this->_column_headers = array($columns, $hidden, $sortable);
  $this->items = $this->view_data;
}



}
?>