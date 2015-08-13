<?php



/****************************************************
	meta boxes
****************************************************/

add_action( 'cmb2_init', 'mbds_init_story_meta_box' );
function mbds_init_story_meta_box() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_mbds_';

	$story_meta_box = new_cmb2_box( apply_filters('mbds_story_meta_box', array(
		'id'            => $prefix . 'story_meta_box',
		'title'         => __( 'About the Story', 'mbd-blog-post-series' ),
		'object_types'  => array( 'mbds_story', ), // Post type
		 'context'    => 'normal',
		 'priority'   => 'high',
		 'show_names' => true, // Show field names on the left		
	) ) );

	$story_meta_box->add_field( apply_filters('mbds_story_type_field', array(
		'name'       => __( 'This Is A', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'type',
		'type'       => 'select',
		'options'	=> array(
						'story' => 'Story',
						'short' => 'Short Story',
						'novella' => 'Novella',
						'novel' => 'Novel',
						'serial' => 'Serial',
						'custom' => 'Custom'),
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_custom_type_field', array(
		'name'       => __( 'Custom Type', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'custom_type',
		'type'       => 'text',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_posts_name_field', array(
		'name'       => __( 'Posts Should Be Called', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'posts_name',
		'type'       => 'select',
		'options'	=> mbds_get_post_names_options(), /*
		'options'	=> array(
						'chapters' => 'Chapters',
						'episodes' => 'Episodes',
						'parts' => 'Parts',
						'custom' => 'Custom'), */
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_custom_posts_name_single_field', array(
		'name'       => __( 'Custom Posts Name - Singular', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'custom_posts_name_single',
		'type'       => 'text',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_custom_posts_name_plural_field', array(
		'name'       => __( 'Custom Posts Name - Plural', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'custom_posts_name_plural',
		'type'       => 'text',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_include_posts_name_field', array(
		'name'		=> __('Include Posts Name and Count in Titles?', 'mbd-blog-post-series'),
		'id'		=> $prefix . 'include_posts_name',
		'type'		=> 'checkbox',
		'desc'		=> 'Will prepend, for example, Chapter X: to the title of the post. If you just name your posts "Chapter 1", etc. don\'t check this.',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_complete_field', array(
		'name'       => __( 'Story Is Complete?', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'complete',
		'type'       => 'checkbox',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_summary_field', array(
		'name'       => __( 'Story Summary', 'mbd-blog-post-series' ),
		'id'         => $prefix . 'summary',
		'type'       => 'wysiwyg',
		'options'	=>	array(
				'wpautop' => true, // use wpautop?
				'media_buttons' => false, // show insert/upload button(s)
				'textarea_rows' => 5,
				'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
				'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
				'teeny' => true,
				'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
			),
	) ) );
	
	$cover_image_meta_box = new_cmb2_box( apply_filters('mbds_story_cover_image_meta_box', array(
		'id'            => 'mbds_cover_image',
		'title'         => __('Story Cover',  'mbd-blog-post-series' ),
		'object_types'  => array( 'mbds_story', ), // Post type
		'context'       => 'side',
		'priority'      => 'default',
		'show_names'    => false, // Show field names on the left
	)));
	
	$cover_image_meta_box->add_field( apply_filters('mbds_story_cover_image_field', array(
			'name' => __('Story Cover',  'mbd-blog-post-series'),
			'id' => '_mbds_cover',
			'type' => 'file',
			'allow' => array(  'attachment' ) // limit to just attachments with array( 'attachment' )
	)));
	
	
	
}

add_action('add_meta_boxes_mbds_story', 'mbds_add_posts_meta_box');
function mbds_add_posts_meta_box() {
	
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_mbds_';
	add_meta_box( $prefix . 'posts_meta_box', __( 'Story Posts', 'mbd-blog-post-series' ), 'mbds_posts_meta_box', 'mbds_story', 'normal',
         'default');
}

function mbds_posts_meta_box() {
	global $post;
	echo '<p>' . __('Drag and drop the items to reorder them.', 'mbd-blog-post-series') . '</p>';
	echo '	<ul id="mbds_post_grid">';
	
	$posts = mbds_get_posts_list($post->ID);
	foreach ($posts as $one_post) {
		echo '<li id="mbds_post_' . $one_post['ID'] . '" class="ui-state-default"><span class="ui-icon"></span>' . $one_post['title'] . '</li>';
	}
	
	echo '</ul>';
}


add_action( 'wp_ajax_get_story_posts', 'mbdbps_get_story_posts' );	
function mbdbps_get_story_posts() {
	$nonce = $_POST['security'];
	
	// check to see if the submitted nonce matches with the
	// generated nonce we created earlier
	if ( ! wp_verify_nonce( $nonce, 'mbds_story_cpt_ajax_nonce' ) ) {
		die ( );
	}
	global $post;
	
	$posts = mbds_get_posts_list($post->ID);
			
	echo json_encode($posts);
	
	wp_die();
	
}


add_action( 'wp_ajax_save_posts_grid', 'mbds_save_posts_grid' );	
function mbds_save_posts_grid() {

	$nonce = $_POST['security'];
	// check to see if the submitted nonce matches with the
	// generated nonce we created earlier
	if ( ! wp_verify_nonce( $nonce, 'mbds_story_cpt_ajax_nonce' ) ) {
		die ( );
	}

	if (isset($_POST['posts'])) {
		
		parse_str($_POST['posts']);
	
		update_post_meta($_POST['storyID'], '_mbds_posts', $mbds_post);
		}
	
}




/****************************************************
	columns
****************************************************/


// Add to our admin_init function
add_filter('manage_mbds_story_posts_columns', 'mbds_add_story_columns');
function mbds_add_story_columns($columns) {
    $columns['mbds_type'] = __('Type', 'mbd-blog-post-series');
	$columns['mbds_length'] = __('Number of Posts', 'mbd-blog-post-series');
	$columns['mbds_complete'] = __('Complete', 'mbd-blog-post-series');
    return apply_filters('mbds_story_columns', $columns);
}

// Add to our admin_init function
add_action('manage_mbds_story_posts_custom_column', 'mbds_render_story_columns', 10, 2);
function mbds_render_story_columns($column_name, $id) {
    switch ($column_name) {
		case 'mbds_type':
			$mbds_type = get_post_meta($id, '_mbds_type', true);
			if ($mbds_type == 'custom') {
				echo get_post_meta($id, '_mbds_custom_type_single', true);
			} else {
				echo $mbds_type;
			}
			break;
		case 'mbds_complete':
			$completed = get_post_meta($id, '_mbds_complete', true);
			if ($completed == 'on') {
				echo __('Yes', 'mbd-blog-post-series');
			} else {
				echo __('No', 'mbd-blog-post-series');
			}
			break;
		case 'mbds_length':
			$posts = get_post_meta($id, '_mbds_posts', true);
			if ($posts == '') {	
				echo '0';
			} else {
				echo count($posts);
			}
			break;
	}
}
