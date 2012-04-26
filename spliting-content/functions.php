<?php

/*
Awsome plugin to to getting parts of content.
this perticuler code splits the content in to sections
using the <!--more--> tag!

*/
if(!function_exists('getPageSection'))
	{
		function getPageSection($pageId,$section = 0)
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
							$content = preg_split('/<!--more-->/i', apply_filters('the_content',$post->post_content)); 
						}
						
						$section = min($section,count($content));
						for($c = 0, $csize = count($content); $c < $csize; $c++) {  
							$content[$c] = apply_filters('the_content', $content[$c]);  
						}  
						return $content[$section];
					}
			return 'No content';
		}
}


// Simply get content by id
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


//SImpel get title by id
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
	

//Getting the name of parrent page
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
	
	
?>