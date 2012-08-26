<?php
	// main picture
	add_theme_support( 'post-thumbnails' );
	
		// size thumbnail
	//add_image_size( 'trumb', 259, 89, true ); 

	// register menus
	if(function_exists('register_nav_menus')) {
		register_nav_menus( array(
			'menu_main' => __('mian navigation'),
			'menu_top' => __('locations navigation'),
		));
	}
	function my_wp_nav_menu_args($args=''){  
    $args['container'] = '';  
    return $args;  
} // function  
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' ); 


function improved_trim_excerpt($text) {
	global $post;
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
		$text = strip_tags($text, '<p>');
		$excerpt_length = 50;
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words)> $excerpt_length) {
			array_pop($words);
			array_push($words, '');
			$text = implode(' ', $words);
		}
	}
	return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');


if(!function_exists('getPageContent'))
	{
		function getPageContent($pageId)
		{
			if(!is_numeric($pageId))
			{
				return;
			}

			global $wpdb;
			$sql_query = 'SELECT DISTINCT * FROM ' . $wpdb->posts .
			' WHERE ' . $wpdb->posts . '.ID=' . $pageId;
			$posts = $wpdb->get_results($sql_query);

			if(!empty($posts))
			{
				foreach($posts as $post)
				{
					$content = apply_filters('the_content', nl2br($post->post_content));
					return $content;
				}
			}
		}
}


if(!function_exists('getPlanePageContent'))
	{
		function getPlanePageContent($pageId)
		{
			if(!is_numeric($pageId))
			{
				return;
			}

			global $wpdb;
			$sql_query = 'SELECT DISTINCT * FROM ' . $wpdb->posts .
			' WHERE ' . $wpdb->posts . '.ID=' . $pageId;
			$posts = $wpdb->get_results($sql_query);

			if(!empty($posts))
			{
				foreach($posts as $post)
				{
					$content = nl2br($post->post_content);
					return $content;
				}
			}
		}
}

	
	

if(!function_exists('getPageTitle'))
	{
		function getPageTitle($pageId)
		{
			if(!is_numeric($pageId))
			{
				return;
			}
			
			global $wpdb;
			$sql_query = 'SELECT DISTINCT * FROM ' . $wpdb->posts .
			' WHERE ' . $wpdb->posts . '.ID=' . $pageId;
			$posts = $wpdb->get_results($sql_query);

			if(!empty($posts))
			{
				foreach($posts as $post)
				{
					$content = apply_filters('the_title', nl2br($post->post_title));
					return $content;
				}
			}
		}
	}
	
	function topPageName(){
		if (is_page()) {
			global $post;
 		 if (!empty($post->post_parent)) {
			 global $wpdb;
  			 $extradata = $wpdb->get_row("select post_title from wp_posts where ID = " . $post->post_parent);
  			 echo utf8_encode(strtolower($extradata->post_title));
  		} else {
   			 echo utf8_encode(strtolower($post->post_title));
        }
     }else{
             echo "none";
     }

		
	}

add_action( 'wp_nav_menu_objects', 'custum_permalink' );
	function custum_permalink($sorted_menu_items){
		global $post;
		$id = 0;
		foreach ($sorted_menu_items as $item) {
		$URL = parse_url($item->url);
		// 	// print_r($URL);
		// 		 
		 if($post->post_parent == 16 || $post->ID == 16 || $post->post_parent == 96 || $post->ID == 96 )
		 {
		 	$id = $post->ID;
		 }
		$base = getCustumRewriteSlug(getDynamicID($id));
		$item->url = 'http://'.$URL['host'].$base.$URL['path'];
		//print_r($item->url);
		}
    return $sorted_menu_items;
}

	
	
	   add_action( 'template_redirect', 'bbo_template_redirect' );
        
		function bbo_template_redirect()
		{
			if ( is_author() ||  is_archive() || is_tag() || is_category()) {
				include (TEMPLATEPATH . '/404.php');
				exit;		
			}
		}

function tele_func( $atts )
				{
				     $theID = 0;
					 global $post;

					 //Let check if we need to load cookie
					 if($post->post_parent == 16 || $post->ID == 16 || $post->post_parent == 96 || $post->ID == 96 ){
					 		if($post->post_parent > 0){
					 			return getNumber($post->post_parent);
					 		}else{
					 			return getNumber($post->ID);
					 		}
					 		
					 }

					 if(isset($_COOKIE["MobileAreaCode"])) {
						 $theID = $_COOKIE["MobileAreaCode"];
						  $p = get_post($theID);
						 if(isset($p->post_parent) && is_numeric($p->post_parent) && $p->post_parent > 0) {
							$theID = $p->post_parent;		 
						 }else{
							$theID =  $p->ID;
						 }

					 }else{
					 	return getNumber($post->post_parent);
					 }

				  return getNumber($theID);
				}
add_shortcode( 'tele', 'tele_func' );


function getNumber($id){
	//16 = göteborg
	//96 = skövde
	switch ((int)$id) {
		case 16:
			return "031-701 00 40";
			break;

		case 96:
			return "0500-46 11 10";
			break;
		
		default:
			return "031-701 00 40";
			break;
	}
	
	
}
add_shortcode('ort', 'getArea' );
function getArea($atts){
    //16 = göteborg
	//96 = skövde
	global $post;
	 if($post->post_parent == 16 || $post->ID == 16 || $post->post_parent == 96 || $post->ID == 96 )
	 {
		return $post->post_title;
	 }

	if(isset($_COOKIE["MobileAreaCode"]))
	{
		return getPageTitle($_COOKIE["MobileAreaCode"]);
	}else{
		return getPageTitle(16); 
	}
	
	
}


function getDynamicID($id = 0){

    global $post;
    //16 = göteborg
	//96 = skövde

     if($post->post_parent == 16 || $post->ID == 16 || $post->post_parent == 96 || $post->ID == 96 )
	 {
		return $post->ID;
	 }

	if(isset($_COOKIE["MobileAreaCode"]))
	{
		return $_COOKIE["MobileAreaCode"];
	}

	
	
	return $id; 
	
}

function getDynamicContent($id){

    $startpageID = 57;
    // echo $id;
    // exit();
    $post = get_post($id);
	if(isset($post->post_parent) && is_numeric($post->post_parent) && $post->post_parent > 0 && $post->post_type ="page" && $post->post_parent != $startpageID) 
	{
		$theID = $post->post_parent;

        $content = $post->post_content;
		if(strlen($content) < 5){
			 $pContent = getPageContent($theID);
			 return $pContent;
		}
				 
	}
      		return getPageContent($id);
}


// function getDynamicTitle($id){

//     $post = get_post($id);
	
// 	$theID = $post->post_parent;

//     $content = $post->post_content;

// 	if(strlen($content) < 5){
// 			 return getPageTitle($theID);
// 	}else{
      
//       		return $post->post_title;
// 	}
	
// }


// function setAreaCookie(){

//     global $post;
//     $theID = 0;
//     if(isset($post->post_parent) && is_numeric($post->post_parent) && $post->post_parent > 0) {
// 		$theID = $post->post_parent;		 
// 	}else{
// 		$theID =   $post->ID;
// 	}

// 	//16 = göteborg
// 	//96 = skövde
// 	if($theID == 16 || $theID == 96){
// 		$expire=time()+60*60*24*30;
//         setcookie("MobileAreaCode", $post->ID, $expire,COOKIEPATH, COOKIE_DOMAIN, false);
// 	}

// }

function pre_display( $query ) {
	 // print_r($query);
	 // print_r($query->query_vars[ort]);
	 // exit
	 if(!isset($query->queried_object)){
       return;
	 }
	  
	    $theID = 0;
    if(isset($query->queried_object->post_parent) && is_numeric($query->queried_object->post_parent) && $query->queried_object->post_parent > 0) {
		$theID = $query->queried_object->post_parent;		 
	}else{
		
		$theID =   $query->queried_object->ID;
	}

	//16 = göteborg
	//96 = skövde
	if($theID == 16 || $theID == 96){
		$expire=time()+60*60*24*30;
        setcookie("MobileAreaCode", $query->queried_object->ID, $expire,COOKIEPATH, COOKIE_DOMAIN, false);
	}


//print_r($query->queried_object);

//
//query->queried_object->post_parent
}
add_action( 'pre_get_posts', 'pre_display' );


function createRewriteRules() {
	global $wp_rewrite;
 

 $new_rules = array(
		'(.?.+?)/(stenskott)$' => 'index.php?pagename='.$wp_rewrite->preg_index(2).'&ort='.
		$wp_rewrite->preg_index(1),
		'(.?.+?)/(bilglas)$' => 'index.php?pagename='.$wp_rewrite->preg_index(2).'&ort='.
		$wp_rewrite->preg_index(1),
		'(.?.+?)/(inbrott-i-bil)$' => 'index.php?pagename='.$wp_rewrite->preg_index(2).'&ort='.
		$wp_rewrite->preg_index(1),
		'(.?.+?)/(om-mobilglas)$' => 'index.php?pagename='.$wp_rewrite->preg_index(2).'&ort='.
		$wp_rewrite->preg_index(1),
		'(.?.+?)/(kontakt)$' => 'index.php?pagename='.$wp_rewrite->preg_index(2).'&ort='.
		$wp_rewrite->preg_index(1)
	);
       // Always add your rules to the top, to make sure your rules have priority
	return $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

	// // add rewrite tokens
	// $keytag = '%ort%';
	// $wp_rewrite->add_rewrite_tag($keytag, '(.+?)', 'ort=');
 
	// $keywords_structure = $wp_rewrite->root . "ort/$keytag/";
	// $keywords_rewrite = $wp_rewrite->generate_rewrite_rules($keywords_structure);
 
	// $wp_rewrite->rules = $keywords_rewrite + $wp_rewrite->rules;
	// return $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'createRewriteRules');

function query_vars($public_query_vars) {
 
	$public_query_vars[] = "ort";
  
	return $public_query_vars;
}
add_filter('query_vars', 'query_vars');

add_action('init', 'wpse42279_add_endpoints');
function wpse42279_add_endpoints()
{
    add_rewrite_endpoint('ort', EP_PAGES);
}





//add_action( 'get_header', 'setAreaCookie' );



function replace_empty_content($content) {


    global $post;
	if(isset($post->post_parent) && is_numeric($post->post_parent) && $post->post_parent > 0 && $post->post_type ="page") 
	{
		$theID = $post->post_parent;

       
		if(strlen($content) < 5){
			 $pContent = getPlanePageContent($theID);
			 return $pContent;
		}
				 
	}

     return $content;
}

add_action('the_content','replace_empty_content');

	
	function getCustumRewriteSlug($id){
		 if($id == 0){
		 	return '';
		 }


		 $post = get_post($id);
		 if($post->post_parent > 0){
		 	$parent = get_post($post->post_parent);
		 	return '/'.$parent->post_name.'/'.$post->post_name;

		 }else{
		 	return '/'.$post->post_name;
		 }

	}
?>