<?php
/*
Plugin Name: Mooberry Story
Plugin URI:  http://www.mooberrydreams.com/products/mooberry-story
Description: Organizes multiple blog posts into a series. Make it easy for readers to find your stories, including older ones.
Version:     1.7
Author:      Mooberry Dreams
Author URI:  https://profiles.wordpress.org/mooberrydreams/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: languages
Text Domain: mooberry-story

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

define( 'MBDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define( 'MBDS_PLUGIN_VERSION_KEY', 'mbds_version' );
define( 'MBDS_PLUGIN_VERSION', '1.7' );


//update checker
require 'includes/plugin-update-checker/plugin-update-checker.php';

$mbds_update_checker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/christiespeich/mooberry-story',
	__FILE__,
	'mooberry-story'
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
require_once dirname( __FILE__ ) . '/includes/updates.php';
require_once dirname( __FILE__ ) . '/shortcodes.php';
require_once dirname( __FILE__ ) . '/widget-stories.php';
require_once dirname( __FILE__ ) . '/widget-posts.php';


register_activation_hook( __FILE__, 'mbds_activate' );
function mbds_activate() {
	mbds_init();

	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

add_action( 'plugins_loaded', 'mbds_plugins_loaded' );
function mbds_plugins_loaded() {

	load_plugin_textdomain( 'mooberry-story', false, basename( MBDS_PLUGIN_DIR ) . '/languages/' );
}

add_action( 'admin_head', 'mbds_register_admin_styles' );
function mbds_register_admin_styles() {
	wp_register_style( 'mbds-admin-styles', plugins_url( 'css/admin-style.css', __FILE__ ) );
	wp_enqueue_style( 'mbds-admin-styles' );
	wp_enqueue_style( 'mbds-jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );

}

add_action( 'admin_footer', 'mbds_register_script' );
function mbds_register_script() {

	//wp_enqueue_script( 'mbds-admin-post-series', plugins_url(  'js/admin-post-series.js', __FILE__), array('jquery'));

	wp_enqueue_script( 'mbds-admin-story-widget', plugins_url( 'js/admin-story-widget.js', __FILE__ ) );

	// story cpt js
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'mbds-admin-story', plugins_url( 'js/admin-story.js', __FILE__ ), array( 'jquery' ) );
	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	wp_localize_script( 'mbds-admin-story', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'security' => wp_create_nonce( 'mbds_story_cpt_ajax_nonce' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'mbds_register_styles', 15 );
function mbds_register_styles() {
	wp_register_style( 'mbds-styles', plugins_url( 'css/style.css', __FILE__ ) );
	wp_enqueue_style( 'mbds-styles' );
}

add_action( 'init', 'mbds_init' );
function mbds_init() {
	register_post_type( 'mbds_story',
		apply_filters( 'mbds_story_cpt', array(
			'label'             => __( 'Stories', 'mooberry-story' ),
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'menu_icon'         => 'dashicons-media-text',
			'menu_position'     => 20,
			'show_in_nav_menus' => true,
			'has_archive'       => false,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'story' ),
			'query_var'         => true,
			'supports'          => array( 'title', 'comments' ),
			'taxonomies'        => array( 'mbds_genre', 'mbds_series' ),
			'labels'            => array(
				'name'                  => __( 'Stories', 'mooberry-story' ),
				'singular_name'         => __( 'Story', 'mooberry-story' ),
				'menu_name'             => __( 'Stories', 'mooberry-story' ),
				'all_items'             => __( 'All Stories', 'mooberry-story' ),
				'add_new'               => __( 'Add New', 'mooberry-story' ),
				'add_new_item'          => __( 'Add New Story', 'mooberry-story' ),
				'edit'                  => __( 'Edit', 'mooberry-story' ),
				'edit_item'             => __( 'Edit Story', 'mooberry-story' ),
				'new_item'              => __( 'New Story', 'mooberry-story' ),
				'view'                  => __( 'View Story', 'mooberry-story' ),
				'view_item'             => __( 'View Story', 'mooberry-story' ),
				'search_items'          => __( 'Search Stories', 'mooberry-story' ),
				'not_found'             => __( 'No Stories Found', 'mooberry-story' ),
				'not_found_in_trash'    => __( 'No Stories Found in Trash', 'mooberry-story' ),
				'parent'                => __( 'Parent Story', 'mooberry-story' ),
				'filter_items_list'     => __( 'Filter Story List', 'mooberry-story' ),
				'items_list_navigation' => __( 'Story List Navigation', 'mooberry-story' ),
				'items_list'            => __( 'Story List', 'mooberry-story' ),
			),
		) )
	);

	register_taxonomy( 'mbds_genre', 'mbds_story',
		apply_filters( 'mdbs_genre_taxonomy', array(
			//'rewrite' => false,
			'rewrite'               => array( 'slug' => 'mbds_genres' ),
			'public'                => true, //false,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'capabilities'          => array(
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_posts',
			),
			'labels'                => array(
				'name'                       => __( 'Genres', 'mooberry-story' ),
				'singular_name'              => __( 'Genre', 'mooberry-story' ),
				'search_items'               => __( 'Search Genres', 'mooberry-story' ),
				'all_items'                  => __( 'All Genres', 'mooberry-story' ),
				'parent_item'                => __( 'Parent Genre', 'mooberry-story' ),
				'parent_item_colon'          => __( 'Parent Genre:', 'mooberry-story' ),
				'edit_item'                  => __( 'Edit Genre', 'mooberry-story' ),
				'update_item'                => __( 'Update Genre', 'mooberry-story' ),
				'add_new_item'               => __( 'Add New Genre', 'mooberry-story' ),
				'new_item_name'              => __( 'New Genre Name', 'mooberry-story' ),
				'menu_name'                  => __( 'Genres', 'mooberry-story' ),
				'popular_items'              => __( 'Popular Genres', 'mooberry-story' ),
				'separate_items_with_commas' => __( 'Separate genres with commas', 'mooberry-story' ),
				'add_or_remove_items'        => __( 'Add or remove genres', 'mooberry-story' ),
				'choose_from_most_used'      => __( 'Choose from the most used genres', 'mooberry-story' ),
				'not_found'                  => __( 'No genres found', 'mooberry-story' ),
				'items_list_navigation'      => __( 'Genre navigation', 'mooberry-story' ),
				'items_list'                 => __( 'Genre list', 'mooberry-story' ),
			),
		) )
	);

	register_taxonomy( 'mbds_series', 'mbds_story',
		apply_filters( 'mbds_series_taxonomy', array(
			'rewrite'               => array( 'slug' => 'mbds_series' ),
			'public'                => true, // false,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'capabilities'          => array(
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_posts',
			),
			'labels'                => array(
				'name'                       => __( 'Series', 'mooberry-story' ),
				'singular_name'              => __( 'Series', 'mooberry-story' ),
				'search_items'               => __( 'Search Series', 'mooberry-story' ),
				'all_items'                  => __( 'All Series', 'mooberry-story' ),
				'parent_item'                => __( 'Parent Series', 'mooberry-story' ),
				'parent_item_colon'          => __( 'Parent Series:', 'mooberry-story' ),
				'edit_item'                  => __( 'Edit Series', 'mooberry-story' ),
				'update_item'                => __( 'Update Series', 'mooberry-story' ),
				'add_new_item'               => __( 'Add New Series', 'mooberry-story' ),
				'new_item_name'              => __( 'New Series Name', 'mooberry-story' ),
				'menu_name'                  => __( 'Series', 'mooberry-story' ),
				'popular_items'              => __( 'Popular Series', 'mooberry-story' ),
				'separate_items_with_commas' => __( 'Separate series with commas', 'mooberry-story' ),
				'add_or_remove_items'        => __( 'Add or remove series', 'mooberry-story' ),
				'choose_from_most_used'      => __( 'Choose from the most used series', 'mooberry-story' ),
				'not_found'                  => __( 'No Series found', 'mooberry-story' ),
				'items_list_navigation'      => __( 'Series navigation', 'mooberry-story' ),
				'items_list'                 => __( 'Series list', 'mooberry-story' ),
			),
		) )
	);
}

add_action( 'widgets_init', 'mbds_register_story_widget' ); // function to load my widget
function mbds_register_story_widget() {
	register_widget( 'mbds_Story_Widget' );
	register_widget( 'mbds_Posts_Widget' );
}


add_filter( 'the_content', 'mbds_content' );
function mbds_content( $content ) {
	global $post;

	// this weeds out content in the sidebar and other odd places
	// thanks joeytwiddle for this update
	// added in version 2.3
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( ! is_single() ) {
		return $content;
	}

	//if it's a post that is part of a story, add next and prev links to top and bottom
	if ( get_post_type() == 'post' && is_main_query() && ! is_admin() ) {

		$storyID = get_post_meta( $post->ID, '_mbds_story', true );
		if ( $storyID != '' ) {
			$story_post_meta = get_post_meta( $storyID );
			$toc_top         = isset( $story_post_meta['_mbds_toc_top'][0] ) ? $story_post_meta['_mbds_toc_top'][0] == 'yes' : true;
			$next_top        = isset( $story_post_meta['_mbds_next_top'][0] ) ? $story_post_meta['_mbds_next_top'][0] == 'yes' : true;
			$prev_top        = isset( $story_post_meta['_mbds_prev_top'][0] ) ? $story_post_meta['_mbds_prev_top'][0] == 'yes' : true;

			$toc_bottom  = isset( $story_post_meta['_mbds_toc_bottom'][0] ) ? $story_post_meta['_mbds_toc_bottom'][0] == 'yes' : true;
			$next_bottom = isset( $story_post_meta['_mbds_next_bottom'][0] ) ? $story_post_meta['_mbds_next_bottom'][0] == 'yes' : true;
			$prev_bottom = isset( $story_post_meta['_mbds_prev_bottom'][0] ) ? $story_post_meta['_mbds_prev_bottom'][0] == 'yes' : true;


			$story_text = $content;
			$content    = '';
			$mbds_story = mbds_get_story( $storyID );
			if ( $toc_top ) {
				$content .= '[mbs_toc_link]';
			}
			if ( $prev_top ) {
				$content .= '[mbs_prev]';
			}
			if ( $next_top ) {
				$content .= '[mbs_next]';
			}
			$content .= '<br style="clear:both;">';
			$content .= '<h2 class="mbs_posts_title">';

			if ( isset( $mbds_story['_mbds_include_posts_name'] ) ) {
				$content .= mbds_display_posts_name( $mbds_story, $post->ID ) . '<br>';
			}

			$alt_title = get_post_meta( $post->ID, '_mbds_alt_chapter_title', true );
			if ( $alt_title != '' ) {
				$title = $alt_title;
			} else {
				$title = $post->post_title;
			}

			$content .= $title . '</h2>';
			$content .= '<div class="mbs_posts_text">' . $story_text . '</div>';
			if ( $toc_bottom ) {
				$content .= '[mbs_toc_link]';
			}
			if ( $prev_bottom ) {
				$content .= '[mbs_prev]';
			}
			if ( $next_bottom ) {
				$content .= '[mbs_next]';
			}
			$content .= '<br style="clear:both;">';
		}
	}

	// if it's a story page, show the TOC
	if ( get_post_type() == 'mbds_story' && is_main_query() && ! is_admin() ) {
		$mbds_story = mbds_get_story( $post->ID );

		$slug = $post->post_name;

		$content = '[mbs_cover story="' . $slug . '"]';

		$content .= '[mbs_summary story="' . $slug . '"]';

		$content .= '[mbs_toc story="' . $slug . '"]';
	}

	return apply_filters( 'mbds_content', $content );

}

// see https://joshlevinson.me/2013/08/14/filter-a-page-posts-title-only-on-that-page-post/
add_action( 'loop_start', 'mbds_condition_filter_title' );
function mbds_condition_filter_title( $query ) {
	global $wp_query;
	// tc_title_text for theme Customizr
	if ( $query === $wp_query ) {
		add_filter( 'the_title', 'mbds_posts_title', 10, 2 );
		add_filter( 'tc_title_text', 'mbds_posts_title', 10, 2 );
	} else {
		remove_filter( 'the_title', 'mbds_posts_title', 10 );
		remove_filter( 'tc_title_text', 'mbds_posts_title', 10 );
	}
}

//add_filter('tc_title_text', 'mbdb_tax_grid_title');
//add_filter( 'the_title', 'mbds_posts_title', 10, 2);
function mbds_posts_title( $title, $id ) {
	global $post;
	// this weeds out content in the sidebar and other odd places
	// thanks joeytwiddle for this update
	// added in version 2.3
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $title;
	}


	if ( get_post_type() == 'post' && is_main_query() && ! is_admin() && $post->ID == $id ) {

		$storyID = get_post_meta( $post->ID, '_mbds_story', true );
		$story   = mbds_get_story( $storyID );
		if ( count( $story ) != 0 ) {
			if ( ! is_single() ) {
				return apply_filters( 'mbdb_archive_title', $story['title'] . ': ' . $title );
			} else {
				$post_title = $title;
				$title      = $story['title'];
				// . '<h2>';
				// if (isset($story['_mbds_include_posts_name'])) {
				// $title = $title . mbds_display_posts_name($story, $post->ID);
				// }
				// $title .= $post_title . '</h2>';
			}
		}
	}

	return apply_filters( 'mbds_posts_title', $title );
}


// edit the breadcrumb for the Customizr theme if this is a tax_grid (series, tag, genre)
// tc_breadcrumb_trail_items should be unique enough to the Customizr theme
// that it doesn't affect anything else?
add_filter( 'tc_breadcrumb_trail_items', 'mbds_posts_breadcrumb', 10, 2 );
function mbds_posts_breadcrumb( $trail, $args ) {
	global $post;
	// this weeds out content in the sidebar and other odd places
	// thanks joeytwiddle for this update
	// added in version 2.3
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $trail;
	}

	if ( get_post_type() == 'post' && is_main_query() && ! is_admin() ) {

		$storyID = get_post_meta( $post->ID, '_mbds_story', true );
		$story   = mbds_get_story( $storyID );
		if ( count( $story ) != 0 ) {
			$trail[1] = '<a href="' . $story['link'] . '">' . $story['title'] . '</a>';
			$trail[2] = $post->post_title;
			array_splice( $trail, 3 );
		}
	}

	return $trail;
}


add_filter( 'pre_get_document_title', 'mbds_wp_title', 10 );
//add_filter( 'the_title', 'mbds_wp_title', 10 );
function mbds_wp_title( $title ) {

	if ( is_feed() ) {
		return $title;
	}
	// this weeds out content in the sidebar and other odd places
	// thanks joeytwiddle for this update
	// added in version 2.3
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $title;
	}

	if ( get_post_type() == 'mbds_story' && is_main_query() && ! is_admin() ) {
		global $post;
		$mbds_story = mbds_get_story( $post->ID );
		$title      = $mbds_story['title'] . ' - ' . __( 'A', 'mooberry-story' );

		if ( isset( $mbds_story['genres'] ) ) {
			$genres = $mbds_story['genres'];
			if ( ! is_array( $genres ) ) {
				$genres = array( $genres );
			}
			if ( count( $genres ) > 0 ) {
				foreach ( $genres as $genre ) {
					$title .= ' ' . $genre->name . ',';
				}
				// remove the last ','
				$title = substr( $title, 0, - 1 );
			}
		}
		$title .= ' ' . $mbds_story['_mbds_type']; // . ' ' . $sep . ' ' . get_bloginfo( 'name', 'display' );

	}

	return apply_filters( 'mbds_wp_title', $title );
}



