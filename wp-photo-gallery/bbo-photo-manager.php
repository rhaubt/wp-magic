<?php
/*
Plugin Name: BBO Photo Manager
Plugin URI: http://bebetteronline.com
Description: Be Better Onlines, wordpress frameworke.
Version: 1.0
Author: Robin Westerlundh
Author URI: http://robinwesterlundh.se
License: GPL2
*/
?>
<?php


    add_action('admin_enqueue_scripts','bbo_media_admin_stylesheet');

    function bbo_media_admin_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'bbo-photo-style', plugins_url('bbo-style.css', __FILE__) );
        wp_enqueue_style( 'bbo-photo-style','' ,array('colors'), 'version', true);
    }



add_action("admin_init", "bbo_gallery_settings_init" );

function bbo_gallery_settings_init(){
 register_setting("bbo_gallery_settings", "bbo_gallarys" );
}



$siteurl = get_option('siteurl');
define('BBO_MEDIA_SETTINGS_FOLDER', dirname(plugin_basename(__FILE__)));
define('BBO_MEDIA_SETTINGS_URL', $siteurl.'/wp-content/plugins/' . BBO_MEDIA_SETTINGS_FOLDER);
// include("bbo-styling.php");


add_action('admin_menu','bbo_photo_admin_menu');

function bbo_photo_admin_menu() { 
	add_menu_page(
		"BBO Photos",
		"BBO Photos",
		8,
		"manage-page",
		"bbo_photo_admin_manage"/*,
		BBO_MEDIA_SETTINGS_URL."/img/skruv-small.png"*/
	); 

	add_submenu_page('manage-page','Create Album','Create Album','8','create-page','bbo_photo_admin_create');
}

function bbo_photo_admin_manage(){
	include("pages/manage-page.php");
}

function bbo_photo_admin_create(){
	include("pages/create-page.php");
}



function bbo_media_col($cols)
{
    if(isset($_GET['bbogallery'])){
        $gallery = $_GET['bbogallery'];
        $cols['bbo_col'] = 'Gallery - '.$gallery;
    }

    return $cols;
}
add_filter('manage_media_columns', 'bbo_media_col');

function handle_bbo_media_col($name, $id)
{
    if(!isset($_GET['bbogallery']))
        return;

    if ($name !== 'bbo_col')
        return false;

    $gallery_id = "bbogallery-".$_GET['bbogallery'];

?>
<!-- Här har vi en knapp som kollar om den är del av galleriet -->
<span class="bbo-photo-ajax-link-wrapper"><a class="bbo-photo-ajax-link" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=create-page&bbo-photo-ajax-gallery=<?php echo $gallery_id; ?>&bbo-photo-ajax-id=<?php echo $id; ?>">Add to Gallery</a> <span class="id" style="display:none;"><?php echo $id; ?></span><span class="gallery_id" style="display:none;"><?php echo $gallery_id; ?></span>
<?php
    //http://preview.bebetteronline.com/coorkonferens/wp-admin/admin.php?page=create-page&bbo-photo-ajax-gallery=testing&bbo-photo-ajax-id=31
    $meta = get_post_meta($id);
    foreach ($meta as $key => $value) {

            //$pos = strrpos($key, "bbogallery");
            $name =  str_replace("bbogallery-","",$key);
            if ($key == $gallery_id) {
                echo "<div class='gallery-puffs'><span class='gallery-puff'><a href='admin.php?page=manage-page&id={$_GET['bbogallery']}'>{$name}</a></span> - <span class='gallery-delete'><a href='".get_option('siteurl')."/wp-admin/admin.php?page=create-page&bbo-photo-ajax-gallery=".$key."&bbo-photo-ajax-delete-id=".$id."'>Delete</a></span><div>";
            }
    }
?>

<?php
}
add_action('manage_media_custom_column', 'handle_bbo_media_col', 10, 2);


add_action('admin_init', 'init_bbo_media');
 
function init_bbo_media() {
    // Only activate plugin for the Media Library page
    if (strpos($_SERVER["REQUEST_URI"], "upload.php") === FALSE)
        return;
     
    add_action('admin_head', 'bbo_media_header', 51);
}

function bbo_media_header() {
?>
<script type="text/javascript">

jQuery(function(){

    var foo = 'bbogallery=<?php echo $_GET['bbogallery'] ?>';

    jQuery('.next-page').attr('href', function(index, attr) {
    return attr + '?' + foo;
    });

    jQuery('.last-page').attr('href', function(index, attr) {
    return attr + '?' + foo;
    });

    jQuery('.first-page').attr('href', function(index, attr) {
    return attr + '?' + foo;
    });

    jQuery('.prev-page').attr('href', function(index, attr) {
    return attr + '?' + foo;
    });

    //add to form
    jQuery('#posts-filter').append('<input type="hidden" name="album" value="<?php echo $_GET['bbogallery'] ?>">');



    //adding ajax code
    jQuery('.bbo-photo-ajax-link').click(function(){

        var aid = jQuery('.bbo-photo-ajax-link').parents('.bbo-photo-ajax-link-wrapper').find('.id').html();
        var gid = jQuery('.bbo-photo-ajax-link').parents('.bbo-photo-ajax-link-wrapper').find('.gallery_id').html();

        var html = '<div class="gallery-puffs"><span class="gallery-puff"><?php echo $_GET['bbogallery']; ?></span> - <span class="gallery-delete"><a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=create-page&bbo-photo-ajax-gallery='+gid+'&bbo-photo-ajax-delete-id='+aid+'">Delete</a></span><div>';
        jQuery(this).parents('td').append(html);

        var aUrl = jQuery(this).attr('href');
        jQuery.get(aUrl, function(data) {
        });
        return false;

    });

    //deleatingajax code
    jQuery('.gallery-delete a').click(function(){

        var aUrl = jQuery(this).attr('href');
        jQuery.get(aUrl, function(data) {
        });
        jQuery(this).parents(".gallery-puffs").remove();
        return false;

    });

});

</script>
<?php
}




// function save_bbo_col()
// {
//     if (!isset($_POST['in_footer']))
//         return false;

//     $in_footer = $_POST['in_footer'];
//     if (is_array($in_footer))
//         $in_footer = array_map('absint', $in_footer); // sanitize
//     else
//         $in_footer = array();

//     $in_footer = array_merge(get_option('in_footer', array()), $in_footer);
//     $in_footer = array_unique(array_filter($in_footer));
//     update_option('in_footer', $in_footer);
// }

// add_action('load-upload.php', 'save_bbo_col');
// 
// 
?>
<?php


        function addGallertMeta($attatcmentId,$galleryId)
        {
            update_post_meta($attatcmentId, $galleryId, '1');

        } 

        function removeGallertMeta($attatcmentId,$galleryId)
        {

            delete_post_meta($attatcmentId, $galleryId);
        }

?>

<?php

function getAllGalleryImages(){

    //List all!
   $querydetails = "
   SELECT wposts.*
   FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
   WHERE wposts.ID = wpostmeta.post_id
   AND wpostmeta.meta_key LIKE 'bbogallery-%'
   ORDER BY wposts.post_date DESC
 ";

  $pageposts = $wpdb->get_results($querydetails, OBJECT);

  return  $pageposts;
}



function getGallaryById($id){
    global $wpdb;
    //List all!
   $querydetails = "
   SELECT wposts.*
   FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
   WHERE wposts.ID = wpostmeta.post_id
   AND wpostmeta.meta_key LIKE 'bbogallery-{$id}'
   ORDER BY wposts.post_date DESC
 ";

  $pageposts = $wpdb->get_results($querydetails, OBJECT);

  return  $pageposts;

}

?>