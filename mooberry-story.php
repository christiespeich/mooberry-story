<?php
/*
Plugin Name: Mooberry Story
Plugin URI:  http://www.mooberrydreams.com/products/mooberry-story
Description: Organizes multiple blog posts into a series. Make it easy for readers to find your stories, including older ones.
Version:     1.2.2
Author:      Mooberry Dreams
Author URI:  https://profiles.wordpress.org/mooberrydreams/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: mbd-blog-post-series

Mooberry Story is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Mooberry Story is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Mooberry Story. If not, see https://www.gnu.org/licenses/gpl-2.0.html.

*/

define('MBDS_PLUGIN_DIR', plugin_dir_path( __FILE__ )); 

define('MBDS_PLUGIN_VERSION_KEY', 'mbds_version');
define('MBDS_PLUGIN_VERSION', '1.2.2'); 


//update checker
require 'includes/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = new PluginUpdateChecker_2_2(
    'http://www.mooberrydreams.com/plugins/2c49afcc-e4a0-403b-a980-c755043a201e/updater.json',
    __FILE__
);

// load in CMB2
if ( file_exists( dirname( __FILE__ ) . '/includes/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/includes/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/includes/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/includes/CMB2/init.php';
}

require_once dirname( __FILE__ ) . '/story.php';
require_once dirname( __FILE__ ) . '/post-meta-box.php';
require_once dirname( __FILE__ ) . '/includes/helper-functions.php';
require_once dirname(__FILE__) . '/shortcodes.php';
require_once dirname(__FILE__) . '/widget-stories.php';
require_once dirname(__FILE__) . '/widget-posts.php';


register_activation_hook( __FILE__, 'mbds_activate' );
function mbds_activate() {
	mbds_init();
	
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}



add_action( 'admin_head', 'mbds_register_admin_styles' );	 
function mbds_register_admin_styles() {
	wp_register_style( 'mbds-admin-styles', plugins_url( 'css/admin-style.css', __FILE__)  );
	wp_enqueue_style( 'mbds-admin-styles' );
	wp_enqueue_style('mbds-jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');

}

add_action( 'admin_footer', 'mbds_register_script');
function mbds_register_script() {
	
	//wp_enqueue_script( 'mbds-admin-post-series', plugins_url(  'js/admin-post-series.js', __FILE__), array('jquery'));
	
	wp_enqueue_script( 'mbds-admin-story-widget', plugins_url( 'js/admin-story-widget.js', __FILE__));
	
	// story cpt js
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script( 'mbds-admin-story', plugins_url(  'js/admin-story.js', __FILE__), array('jquery')); 
	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	wp_localize_script( 'mbds-admin-story', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'security' => wp_create_nonce( 'mbds_story_cpt_ajax_nonce' ) ) );
}

add_action( 'wp_enqueue_scripts', 'mbds_register_styles', 15 );
function mbds_register_styles() {
	wp_register_style( 'mbds-styles', plugins_url( 'css/style.css', __FILE__) ) ;
	wp_enqueue_style( 'mbds-styles' );
}

add_action( 'init', 'mbds_init' );	
function mbds_init() {
	register_post_type('mbds_story',
			apply_filters('mbds_story_cpt', array(	
			'label' => __('Stories', 'mbd-blog-post-series' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-media-text',
			'menu_position' => 20,
			'show_in_nav_menus' => true,
			'has_archive' => false,
			'hierarchical' => false,
			'rewrite' => array( 'slug' => 'story' ),
			'query_var' => true,
			'supports' => array( 'title', 'comments' ),
			'taxonomies' => array( 'mbds_genre', 'mbds_series' ),
			'labels' => array (
				'name' => __('Stories', 'mbd-blog-post-series'),
				'singular_name' => __('Story', 'mbd-blog-post-series'),
				'menu_name' => __('Stories', 'noun', 'mbd-blog-post-series'),
				'all_items' => __('All Stories', 'mbd-blog-post-series'),
				'add_new' => __('Add New', 'mbd-blog-post-series'),
				'add_new_item' => __('Add New Story', 'mbd-blog-post-series'),
				'edit' => __('Edit', 'mbd-blog-post-series'),
				'edit_item' => __('Edit Story', 'mbd-blog-post-series'),
				'new_item' => __('New Story', 'mbd-blog-post-series'),
				'view' => __('View Story', 'mbd-blog-post-series'),
				'view_item' => __('View Story', 'mbd-blog-post-series'),
				'search_items' => __('Search Stories', 'mbd-blog-post-series'),
				'not_found' => __('No Stories Found', 'mbd-blog-post-series'),
				'not_found_in_trash' => __('No Stories Found in Trash', 'mbd-blog-post-series'),
				'parent' => __('Parent Story', 'mbd-blog-post-series'),
				'filter_items_list'     => __( 'Filter Story List', 'mbd-blog-post-series' ),
				'items_list_navigation' => __( 'Story List Navigation', 'mbd-blog-post-series' ),
				'items_list'            => __( 'Story List', 'mbd-blog-post-series' ),
				),
			) )
		);
		
		register_taxonomy('mbds_genre', 'mbds_story', 
			apply_filters('mdbs_genre_taxonomy', array(
				//'rewrite' => false, 
				'rewrite' => array(	'slug' => 'mbds_genres' ),
				'public' => true, //false,
				'show_admin_column' => true,
				'update_count_callback' => '_update_post_term_count',
				'capabilities'	=> array(
					'manage_terms' => 'manage_categories',
					'edit_terms'   => 'manage_categories',
					'delete_terms' => 'manage_categories',
					'assign_terms' => 'edit_posts',				
				),
				'labels' => array(
					'name' => __('Genres', 'mbd-blog-post-series'),
					'singular_name' => __('Genre', 'mbd-blog-post-series'),
					'search_items' => __('Search Genres' , 'mbd-blog-post-series'),
					'all_items' =>  __('All Genres' , 'mbd-blog-post-series'),
					'parent_item' =>  __('Parent Genre' , 'mbd-blog-post-series'),
					'parent_item_colon' =>  __('Parent Genre:' , 'mbd-blog-post-series'),
					'edit_item' =>  __('Edit Genre' , 'mbd-blog-post-series'),
					'update_item' =>  __('Update Genre' , 'mbd-blog-post-series'),
					'add_new_item' =>  __('Add New Genre' , 'mbd-blog-post-series'),
					'new_item_name' =>  __('New Genre Name' , 'mbd-blog-post-series'),
					'menu_name' =>  __('Genres' , 'mbd-blog-post-series'),
					'popular_items' => __('Popular Genres', 'mbd-blog-post-series'),
					'separate_items_with_commas' => __('Separate genres with commas', 'mbd-blog-post-series'),
					'add_or_remove_items' => __('Add or remove genres', 'mbd-blog-post-series'),
					'choose_from_most_used' => __('Choose from the most used genres', 'mbd-blog-post-series'),
					'not_found' => __('No genres found', 'mbd-blog-post-series'),
					'items_list_navigation' => __( 'Genre navigation', 'mbd-blog-post-series' ),
					'items_list'            => __( 'Genre list', 'mbd-blog-post-series' ),
				)
			) )
		);
		
		register_taxonomy('mbds_series', 'mbds_story', 
			apply_filters('mbds_series_taxonomy', array( 
			'rewrite' =>  array( 'slug' => 'mbds_series' ),
			'public' => true, // false,
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
			'capabilities'	=> array(
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_posts',				
			),
			'labels' => array(
				'name' => __('Series', 'mbd-blog-post-series'),
				'singular_name' => __('Series', 'mbd-blog-post-series'),
				'search_items' => __('Search Series' , 'mbd-blog-post-series'),
				'all_items' =>  __('All Series' , 'mbd-blog-post-series'),
				'parent_item' =>  __('Parent Series' , 'mbd-blog-post-series'),
				'parent_item_colon' =>  __('Parent Series:' , 'mbd-blog-post-series'),
				'edit_item' =>  __('Edit Series' , 'mbd-blog-post-series'),
				'update_item' =>  __('Update Series' , 'mbd-blog-post-series'),
				'add_new_item' =>  __('Add New Series' , 'mbd-blog-post-series'),
				'new_item_name' =>  __('New Series Name' , 'mbd-blog-post-series'),
				'menu_name' =>  __('Series' , 'mbd-blog-post-series'),
				'popular_items' => __('Popular Series', 'mbd-blog-post-series'),
				'separate_items_with_commas' => __('Separate series with commas', 'mbd-blog-post-series'),
				'add_or_remove_items' => __('Add or remove series', 'mbd-blog-post-series'),
				'choose_from_most_used' => __('Choose from the most used series', 'mbd-blog-post-series'),
				'not_found' => __('No Series found', 'mbd-blog-post-series'),
				'items_list_navigation' => __( 'Series navigation', 'mbd-blog-post-series' ),
				'items_list'            => __( 'Series list', 'mbd-blog-post-series' ),
			)
		) )
	);		
}

add_action( 'widgets_init', 'mbds_register_story_widget' ); // function to load my widget 
function mbds_register_story_widget() {
	register_widget('mbds_Story_Widget');
	register_widget('mbds_Posts_Widget');
}


add_filter( 'the_content', 'mbds_content');
function mbds_content($content) {
	global $post;
	
	// this weeds out content in the sidebar and other odd places
		// thanks joeytwiddle for this update
		// added in version 2.3
		if (!in_the_loop() || !is_main_query() ) {
			return $content;
		}
		
		if (!is_single()) {
			return $content;
		}
	
	//if it's a post that is part of a story, add next and prev links to top and bottom
	if ( get_post_type() == 'post' && is_main_query() && !is_admin() ) {
		
		$storyID = get_post_meta($post->ID, '_mbds_story', true);
		if ($storyID != '') {
			$story_text = $content;
			$mbds_story = mbds_get_story($storyID);
			$content = '[mbs_toc_link]';
			$content .= '[mbs_prev][mbs_next]<br style="clear:both;">';
			$content .= '<h2 class="mbs_posts_title">';
			
			if (isset($mbds_story['_mbds_include_posts_name'])) {
				 $content .= mbds_display_posts_name($mbds_story, $post->ID) . '<br>';
			 }
			
			$content .= $post->post_title . '</h2>';
			$content .= '<div class="mbs_posts_text">' . $story_text . '</div>';
			$content .= '[mbs_toc_link]';
			$content .= '[mbs_prev][mbs_next]<br style="clear:both;">';
		}
	}
	
	// if it's a story page, show the TOC
	if (get_post_type() == 'mbds_story' && is_main_query() && !is_admin() ) {
		$mbds_story = mbds_get_story( $post->ID );
		
		$slug = $post->post_name;
		
		$content = '[mbs_cover story="' . $slug . '"]';
		
		$content .= '[mbs_summary story="' . $slug . '"]';

		$content .= '[mbs_toc story="' . $slug . '"]';
	}
	return apply_filters('mbds_content', $content);

}

// see https://joshlevinson.me/2013/08/14/filter-a-page-posts-title-only-on-that-page-post/
add_action('loop_start','mbds_condition_filter_title');
function mbds_condition_filter_title($query){
	global $wp_query;
	// tc_title_text for theme Customizr
	if($query === $wp_query){
		add_filter( 'the_title', 'mbds_posts_title', 10, 2);
		add_filter('tc_title_text', 'mbds_posts_title');
	}else{
		remove_filter('the_title','mbds_posts_title', 10, 2);
		remove_filter('tc_title_text', 'mbds_posts_title');
	}
}

//add_filter('tc_title_text', 'mbdb_tax_grid_title');
//add_filter( 'the_title', 'mbds_posts_title', 10, 2);
function mbds_posts_title( $title, $id ) {
	global $post;
	// this weeds out content in the sidebar and other odd places
		// thanks joeytwiddle for this update
		// added in version 2.3
		if (!in_the_loop() || !is_main_query() ) {
			return $title;
		}
			
		
		
	if (get_post_type() == 'post' && is_main_query() && !is_admin() && $post->ID == $id) {
		
		$storyID = get_post_meta($post->ID, '_mbds_story', true);
		$story = mbds_get_story($storyID);
		if (count($story) != 0) {
			if (!is_single()) {
				return apply_filters('mbdb_archive_title', $story['title'] . ': ' .$title);
			} else {
				$post_title = $title;
				$title = $story['title'];
				// . '<h2>';
				// if (isset($story['_mbds_include_posts_name'])) {
					// $title = $title . mbds_display_posts_name($story, $post->ID);
				// }
				 // $title .= $post_title . '</h2>';
			}
		}
	}
	return apply_filters('mbds_posts_title', $title);
}


// edit the breadcrumb for the Customizr theme if this is a tax_grid (series, tag, genre)
// tc_breadcrumb_trail_items should be unique enough to the Customizr theme
// that it doesn't affect anything else?
add_filter('tc_breadcrumb_trail_items', 'mbds_posts_breadcrumb', 10, 2);
function mbds_posts_breadcrumb( $trail, $args) {
	global $post;
	// this weeds out content in the sidebar and other odd places
		// thanks joeytwiddle for this update
		// added in version 2.3
		if (!in_the_loop() || !is_main_query() ) {
			return $trail;
		}
		
	if (get_post_type() == 'post' && is_main_query() && !is_admin()) {
		
		$storyID = get_post_meta($post->ID, '_mbds_story', true);
		$story = mbds_get_story($storyID);
		if (count($story) != 0) {
			$trail[1] = '<a href="' . $story['link'] . '">' . $story['title'] . '</a>';
			$trail[2] = $post->post_title;
			array_splice($trail, 3);
		}
	}
	return $trail;
}
	

add_filter( 'wp_title', 'mbds_wp_title', 10, 2 );
function mbds_wp_title($title, $sep ) {
	
	if ( is_feed() ) {
		return $title;
	}
	// this weeds out content in the sidebar and other odd places
		// thanks joeytwiddle for this update
		// added in version 2.3
		if (!in_the_loop() || !is_main_query() ) {
			return $title;
		}
		
	if (get_post_type() == 'mbds_story' && is_main_query() && !is_admin() ) {
		global $post;
		$mbds_story = mbds_get_story( $post->ID );
		$title = $mbds_story['title'] . ' - ' . __('A', 'mbd-blog-post-series');
		
		if (isset($mbds_story['genres'])) {
			if (count($mbds_story['genres']>0)) {
				foreach ($mbds_story['genres'] as $genre) {
					$title .= ' ' . $genre->name . ',';
				}
				// remove the last ','
				$title = substr($title, 0, -1);
			}
		}
		$title .= ' ' . $mbds_story['_mbds_type'] . ' ' . $sep . ' ' . get_bloginfo( 'name', 'display' );
	
	}
	
	return apply_filters('mbds_wp_title', $title);
}



