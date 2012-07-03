
<form action="" method="post">
<input type="hidden" name="page" value="bbo-settings-manager/bbo-settings-manager.php">
<?php 
$contentBoxes = array(new ContentBox("ccbo1","ccbo1"), new ContentBox("asss","asasd"));
?>
<?php
	foreach($contentBoxes as $contentBox )
	{
		$contentBox->generate_dropdown();
	}	
 ?>
	
<input type="submit" name="save" value="Save">
</form>

<?php if(isset($_POST['save'])){ ?>
saving private data

<?php
 }
?>
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
        $mypages = get_pages( array( 'child_of' => $post->ID, 'sort_column' => 'post_date', 'sort_order' => 'desc' ) );
	?>
        <label fore="<?php echo $this->id; ?>"><?php echo $this->name; ?> : </label>
	<select name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
	<?php 
	update_option($this->id,$_POST[$this->id]);
	$options = get_option($this->id);
	foreach( $mypages as $page ) { ?>
	<option <?php if($options == $page->ID){echo 'selected="selected"';} ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
      
	
	<?php } ?>
  </select><br>
    <?php
    }
 
}
?>
