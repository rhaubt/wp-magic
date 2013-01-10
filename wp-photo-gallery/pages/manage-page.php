<?php
if( ! class_exists( 'BBO_Media_List_Table' ) ) {
    require_once(ABSPATH . '/wp-content/plugins/' . BBO_MEDIA_SETTINGS_FOLDER .'/classes/BBO_Media_List_Table.php' );
}


echo '<div class="wrap"><h2>BBO Media</h2>';

settings_fields('bbo_gallery_settings');
$options = get_option('bbo_gallarys');

foreach ($options as $key => $value){

  if(isset($_GET['id']) && $key != $_GET['id'])
    continue;

  echo "<h2>{$key}</h2>";
  ?>
  <a href="upload.php?bbogallery=<?php echo $key; ?>">Add Photos</a>
  <?php
  $example_data = generateMediaArray(getGallaryById($key),$key);
  $myListTable  = new BBO_Media_List_Table();
  $myListTable->setData($example_data);
  $myListTable->prepare_items(); 
  $myListTable->display(); 
}
  echo '</div>'; 

function generateMediaArray($mediaArray,$albumname){
  
  $returner = array();
  foreach ($mediaArray as $albumsvalue)
  {
    $returner[] = array('ID' => $albumsvalue->ID, 'thumbnail' => '<img width="60" height="60" src="'.$albumsvalue->guid.'">', 'gallerytitle' => '<div>'.$albumsvalue->post_title.'</div>', 'numbers' => '', 'extra' => '<span class="gallery-delete"><a href="admin.php?page=create-page&bbo-photo-ajax-gallery=bbogallery-'.$albumname.'&bbo-photo-ajax-delete-id='.$albumsvalue->ID.'">Delete</a></span>');
  }
  return $returner;
}

?>

<script type="text/javascript">
  
      //deleatingajax code
    jQuery('.gallery-delete a').click(function(){

        var aUrl = jQuery(this).attr('href');
        jQuery.get(aUrl, function(data) {
        });
        jQuery(this).parents("tr").remove();
        return false;

    });


</script>
