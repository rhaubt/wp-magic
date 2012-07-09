<?php
/*Här kan vi lägga till nya content kopplingar */

$contentBoxes = array();
$imageBoxes   = array();
$textBoxes    = array();

?>
<br>
<h2>Be Better Online - Content Matching</h2>
<form id="bbo-admin-form" action="" method="post">
<input type="hidden" name="page" value="content-page">
<?php

	foreach($contentBoxes as $contentBox )
	{
		$contentBox->generate_dropdown();
	}	

 ?>

<?php

	foreach($imageBoxes as $imagebox)
	{
		$imagebox->generate_imagebox();
	}
	
 ?>


<?php

	foreach($textBoxes as $textbox)
	{
		$textbox->generate_textbox();
	}
	
 ?>

<input class="submit-btn" type="submit" name="save" value="Save">
</form>

<?php
class ContentBox
{

    var $name ="name";
    var $id = "id"; 
    var $type ="type";
	
    public function __construct($n,$i) 
    { 
     $this->name = $n;
     $this->id = $i;
    }

    function generate_dropdown()
    {
        $mypages = get_pages( array( 'sort_column' => 'post_date', 'sort_order' => 'desc' ) );
	?>
	<?php if(isset($_POST['save']))
		{ 
		    if(isset($_POST[$this->id])){
		   	 update_option($this->id,$_POST[$this->id]);
                    }
		}
         ?>
        <div class="line">
        <label class="label thick" fore="<?php echo $this->id; ?>"><?php echo $this->name; ?> : </label>
	<select class="selectbox" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
	
	<?php 
	$options = get_option($this->id);
	foreach( $mypages as $page ) { ?>
	<option <?php if($options == $page->ID){echo 'selected="selected"';} ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
      
	
	<?php } ?>
  </select>
  <div style="clear:both;"></div>
  </div>

    <?php
    }
 
}

class ImageBox
{

    var $name ="name";
    var $id = "id"; 
	
    public function __construct($n,$i) 
    { 
     $this->name = $n;
     $this->id = $i;
    }

    function generate_imagebox()
    {
	global $wpdb; 
	$field_unique_id = "bbo_img_id_".$this->id;
	echo '<style type="text/css">#postdivrich{display:none;}</style>';
       
	 if(isset($_POST['save']))
		{ 
		  if(isset($_POST[$field_unique_id]))
		  {
		   update_option($field_unique_id,$_POST[$field_unique_id]);
                  }else{
		   update_option($field_unique_id,"");
		  }
		}
             

		$custom          =  get_option($field_unique_id);
		$file_id         =  $custom;
		$image_thumbnail =  wp_get_attachment_image_src($file_id, 'thumbnail', true );
		$image_thumbnail =  $image_thumbnail[0];
		$image_html      =  "<img src='$image_thumbnail' alt='' />";
		$file_name       =  rawurlencode(get_the_title($file_id));
		$image_name      =  $file_name;

               

		echo "<h2>Ladda upp ".$this->name."</h2>";
		echo "<div id='admin-mat-thumb'>";
		echo "<div class='simple-fields-metabox-field-file'>";
						echo "<label style='display:none;'>{$file_name}</label>";
						echo "<div class='simple-fields-metabox-field-file-col1'>";
							echo "<div class='simple-fields-metabox-field-file-selected-image'>$image_html</div>";
						echo "</div>";
						echo "<div class='simple-fields-metabox-field-file-col2'>";
							echo "<input type='hidden' class='text simple-fields-metabox-field-file-fileID' name='$field_unique_id' id='$field_unique_id' value='$file_id' />";
							echo "<div class='simple-fields-metabox-field-file-selected-image-name'>$image_name</div>";

							$field_unique_id_esc = rawurlencode($field_unique_id);
							#$file_url = "media-upload.php?simple_fields_dummy=1&simple_fields_action=select_file&simple_fields_file_field_unique_id=$field_unique_id_esc&post_id=$post_id&TB_iframe=true";
							// xxx
							$file_url = "media-upload.php?fileSmillaDummy=1&fileSmilla_action=select_file&simple_fields_file_field_unique_id=$field_unique_id_esc&post_id=0&TB_iframe=true&width=640&height=824";
							//$file_url = "media-upload.php?simple_fields_dummy=1&simple_fields_action=select_file&simple_fields_file_field_unique_id=$field_unique_id_esc&post_id=-1&TB_iframe=true";
							echo "<a class='thickbox simple-fields-metabox-field-file-select' href='$file_url'>Select file</a>";

							echo " | <a href='#' class='simple-fields-metabox-field-file-clear'>Clear</a>";
						echo "</div>";
		echo "</div>";
		echo "</div>";

		}
}


class TextBox
{

    var $name ="";
    var $id   = ""; 
    var $type ="";
	
    public function __construct($n,$i) 
    { 
     $this->name = $n;
     $this->id = $i;
    }

    function generate_textbox()
    {
	if(isset($_POST['save']))
		{ 
                  if(isset($_POST[$this->id])){
		  	update_option("bbo_text_".$this->id,$_POST[$this->id]);
                  }else{
			update_option("bbo_text_".$this->id,"");
		  }
		}
    ?>
   <div class="line">
   <label class="label thick"><?php  echo $this->name; ?></label>
   <input class="textfield" type="textfield" name="<?php  echo $this->id; ?>" placeholder="<?php echo $this->name; ?>" value="<?php echo get_option('bbo_text_'.$this->id)?>">
   <div style="clear:both;"></div>
   </div>
    <?php
    }
}


?>
<script type="text/javascript">
jQuery(".simple-fields-metabox-field-file-clear").live("click", function() {
		var jQueryli = jQuery(this).closest(".simple-fields-metabox-field-file");
		jQueryli.find(".simple-fields-metabox-field-file-fileID").val("");
		jQueryli.find(".simple-fields-metabox-field-file-selected-image").text("");
		jQueryli.find(".simple-fields-metabox-field-file-selected-image-name").text("");
		return false;
});
</script>
	
