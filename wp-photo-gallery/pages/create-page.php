<h2>Be Better Online - Admin Settings</h2>
<?php
if(isset($_GET['bbo-photo-ajax-id'])){
	  if(isset($_GET['bbo-photo-ajax-id']) && is_numeric($_GET['bbo-photo-ajax-id'])){
	  	if(isset($_GET['bbo-photo-ajax-gallery'])){
	  		$id   = $_GET['bbo-photo-ajax-id'];
	  		$name = $_GET['bbo-photo-ajax-gallery'];
	  		addGallertMeta($id,$name);
	  	}
	  } 	
	  exit();
	  return;
}
?>
<?php
if(isset($_GET['bbo-photo-ajax-delete-id'])){
	  if(isset($_GET['bbo-photo-ajax-delete-id']) && is_numeric($_GET['bbo-photo-ajax-delete-id'])){
	  	if(isset($_GET['bbo-photo-ajax-gallery'])){
	  		$id   = $_GET['bbo-photo-ajax-delete-id'];
	  		$name = $_GET['bbo-photo-ajax-gallery'];
	  		removeGallertMeta($id,$name);
	  	}
	  } 	
	  exit();
	  return;
}
?>
 <div class="wrap">
        <h2>Ozh's Sample Options</h2>
        <form method="post" action="options.php">
            <?php settings_fields('bbo_gallery_settings'); ?>
            <?php $options = get_option('bbo_gallarys'); ?>

<?php 
foreach ($options as $key => $value) {

}
?>
            <table class="form-table show-gallery-table">
				<?php 
				foreach ($options as $key => $value) {
				?>
			             <tr valign="top">
				            <th scope="row"><span class="gallery-puff"><a href="admin.php?page=manage-page&id=<?php echo $key ?>"><?php echo $value ?></a></span></th>
		                    <td><input type="hidden" name="bbo_gallarys[<?php echo $key; ?>]" value="<?php echo $value ?>" /> <a href="upload.php?bbogallery=<?php echo $key ?>">Add Photos<a> </td>
							<td><span class="delete_gallery gallery-delete">Delete</span></td>
		                </tr>
						

				<?php
				}
				?>

				<tr>
					<th>
						 <div>
						 Create Gallery</div> <div><input class="gallery-value" type="text" placeholder="name"> <input class="add-gallery button-primary" type="button" value="Add">
						 </div>
					</th>		
				</tr>
            </table>

            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> <input class="button-primary" type="button" value="Reload All Gallerys">
            </p>
        </form>
        
       <div>
      

<script type="text/javascript">	

jQuery(function(){
 jQuery('.delete_gallery').click(function(){
 	jQuery(this).parents("tr").remove();
 }); 

 jQuery('.gallery-puff').click(function(){

 });
 jQuery('.add-gallery').click(function(){
 	var newVal = jQuery('.gallery-value').val();
 	var newId  = newVal.replace(/ /g, '_');


 	var newHtml = '<tr valign="top"><th scope="row">'+newVal+'</th><td><input type="hidden" name="bbo_gallarys['+newId+']" value="'+newVal+'" /> Save First! </td><td><span class="delete_gallery gallery-delete">Delete</span></td></tr>';
 	jQuery('.form-table').append(newHtml);
 });

});
</script>
    </div>
<?



?>
