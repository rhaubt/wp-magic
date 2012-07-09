<br>
<h2>Be Better Online - Admin Settings</h2>
<form id="bbo-admin-form" action="" method="post">
<input type="hidden" name="page" value="settings-page">
<?php 
$checkBoxes = array(
new CheckBox("Hide Dashbord","hide-dashbord"),
new CheckBox("Hide Posts","hide-posts"),
new CheckBox("Hide Media","hide-media"), 
new CheckBox("Hide Links","hide-links"), 
new CheckBox("Hide Pages","hide-pages"),
new CheckBox("Hide Comments","hide-comments"), 
new CheckBox("Hide Appearence","hide-appearence"), 
new CheckBox("Hide Appearence - Themes","hide-appearence-themes"),
new CheckBox("Hide Appearence - Widgets","hide-appearence-widgets"),
new CheckBox("Hide Appearence - Menus","hide-appearence-menus"),
new CheckBox("Hide Appearence - Editor","hide-appearence-editor"),
new CheckBox("Hide Plugins","hide-plugins"), 
new CheckBox("Hide Users","hide-users"), 
new CheckBox("Hide Tools","hide-tools"), 
new CheckBox("Hide Settings","hide-settings"), 
new CheckBox("Hide Settings - General","hide-settings-general"), 
new CheckBox("Hide Settings - Writing","hide-settings-writing"), 
new CheckBox("Hide Settings - Reading","hide-settings-reading"), 
new CheckBox("Hide Settings - Discussion","hide-settings-discussion"),
new CheckBox("Hide Settings - Media","hide-settings-media"), 
new CheckBox("Hide Settings - Privacy","hide-settings-privacy"), 
new CheckBox("Hide Settings - Permalinks","hide-settings-permalink"), 
new CheckBox("Hide BBO Settings","hide-bbosettings"),
new CheckBox("Hide Profile","hide-profile"));
?> 
<?php
	foreach($checkBoxes as $checkBox )
	{
		$checkBox->generate_checkbox();
	}	
 ?>

<?php 
	if(isset($_POST['save'])){ 
	   update_option('rename-post',$_POST['rename-post']);
	   update_option('rename-post-name',$_POST['rename-post-name']);
	 }
?>
<div class="line">
<label class="label" fore="rename-post-name">Rename Post posttype : </label>	
<input type="textfield" placeholder="Post Name" name="rename-post-name" value="<?php echo get_option('rename-post-name');?>">  <input class="checkbox" <?php if(get_option('rename-post') == "true"){echo 'checked="checked"';} ?> type="checkbox" name="rename-post" value="true" />
</div>


<input  type="submit" name="save" value="Save">
</form>
<?php
class CheckBox
{

    var $name ="name";
    var $id = "id"; 
    var $type ="type";
	
    public function __construct($n,$i) 
    { 
     $this->name = $n;
     $this->id = $i;
    }

    function generate_checkbox()
    {
	?>
<div class="line">
        <label class="label" fore="<?php echo $this->id; ?>"><?php echo $this->name; ?> : </label>
	<?php 
	if(isset($_POST['save'])){ 
	   update_option($this->id,$_POST[$this->id]);
	 }
	
	$options = get_option($this->id);
	?>
        <input class="checkbox" <?php if($options == "true"){echo 'checked="checked"';} ?> type="checkbox" name="<?php echo $this->id; ?>" value="true" />
<div style="clear: both;"></div>
	</div>
    <?php
    }
 
}

if(isset($_POST['save']))
 { 
	   ?>
<script type="text/javascript">
location.reload(true);
</script>
         <?php
  }
?>
