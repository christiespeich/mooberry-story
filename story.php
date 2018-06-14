<?php



/****************************************************
	meta boxes
****************************************************/

add_action( 'cmb2_init', 'mbds_init_story_meta_box' );
function mbds_init_story_meta_box() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_mbds_';

	$yes_no =  array(
								'yes'   =>  __('Yes', 'mooberry-story'),
								'no' =>  __('No', 'mooberry-story'),
		);

	$story_meta_box = new_cmb2_box( apply_filters('mbds_story_meta_box', array(
		'id'            => $prefix . 'story_meta_box',
		'title'         => __( 'About the Story', 'mooberry-story' ),
		'object_types'  => array( 'mbds_story', ), // Post type
		 'context'    => 'normal',
		 'priority'   => 'high',
		 'show_names' => true, // Show field names on the left		
	) ) );

	$story_meta_box->add_field( apply_filters('mbds_story_type_field', array(
		'name'       => __( 'This Is A', 'mooberry-story' ),
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
		'name'       => __( 'Custom Type', 'mooberry-story' ),
		'id'         => $prefix . 'custom_type',
		'type'       => 'text',
	) ) );


	$story_meta_box->add_field( apply_filters( 'mbds_story_open_story_field', array(
		'name'      =>  __('Can other users add to this story?', 'mooberry-story'),
		'id'        =>  $prefix . 'open_story',
		'type'      => 'select',
		'default'   =>  'no',
		'options'   =>  $yes_no,
	)	)	);

	$story_meta_box->add_field( apply_filters('mbds_story_posts_name_field', array(
		'name'       => __( 'Posts Should Be Called', 'mooberry-story' ),
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
		'name'       => __( 'Custom Posts Name - Singular', 'mooberry-story' ),
		'id'         => $prefix . 'custom_posts_name_single',
		'type'       => 'text',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_custom_posts_name_plural_field', array(
		'name'       => __( 'Custom Posts Name - Plural', 'mooberry-story' ),
		'id'         => $prefix . 'custom_posts_name_plural',
		'type'       => 'text',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_include_posts_name_field', array(
		'name'		=> __('Include Posts Name and Count in Titles?', 'mooberry-story'),
		'id'		=> $prefix . 'include_posts_name',
		'type'		=> 'checkbox',
		'desc'		=> 'Will prepend, for example, Chapter X: to the title of the post. If you just name your posts "Chapter 1", etc. don\'t check this.',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_complete_field', array(
		'name'       => __( 'Story Is Complete?', 'mooberry-story' ),
		'id'         => $prefix . 'complete',
		'type'       => 'checkbox',
	) ) );
	
	$story_meta_box->add_field( apply_filters('mbds_story_summary_field', array(
		'name'       => __( 'Story Summary', 'mooberry-story' ),
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
		'title'         => __('Story Cover',  'mooberry-story' ),
		'object_types'  => array( 'mbds_story', ), // Post type
		'context'       => 'side',
		'priority'      => 'default',
		'show_names'    => false, // Show field names on the left
	)));
	
	$cover_image_meta_box->add_field( apply_filters('mbds_story_cover_image_field', array(
			'name' => __('Story Cover',  'mooberry-story'),
			'id' => '_mbds_cover',
			'type' => 'file',
			'allow' => array(  'attachment' ) // limit to just attachments with array( 'attachment' )
	)));
	
	$story_display_meta_box = new_cmb2_box( apply_filters('mbds_story_display_meta_box', array(
		'id'            => 'mbds_display',
		'title'         => __('Story Display Settings',  'mooberry-story' ),
		'object_types'  => array( 'mbds_story', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names'    => true, // Show field names on the left
	)));

	$story_display_meta_box->add_field( apply_filters('mbds_story_prev_top', array(
		'name'  =>  __('Show Previous Chapter Link on top of page?', 'mooberry-story' ),
		'id'    =>  '_mbds_prev_top',
		'type'  =>  'select',
		'options'    =>  $yes_no,
		)));
	
	$story_display_meta_box->add_field( apply_filters('mbds_story_next_top', array(
		'name'  =>  __('Show Next Chapter Link on top of page?', 'mooberry-story' ),
		'id'    =>  '_mbds_next_top',
		'type'  =>  'select',
		'options'    =>  $yes_no,
		)));
	
	$story_display_meta_box->add_field( apply_filters('mbds_story_toc_top', array(
		'name'  =>  __('Show Table of Contents Link on top of page?', 'mooberry-story' ),
		'id'    =>  '_mbds_toc_top',
		'type'  =>  'select',
		'options'    =>  $yes_no,
		)));
	
		$story_display_meta_box->add_field( apply_filters('mbds_story_prev_bottom', array(
		'name'  =>  __('Show Previous Chapter Link on bottom of page?', 'mooberry-story' ),
		'id'    =>  '_mbds_prev_bottom',
		'type'  =>  'select',
		'options'    =>  $yes_no,
		)));
	
	$story_display_meta_box->add_field( apply_filters('mbds_story_next_bottom', array(
		'name'  =>  __('Show Next Chapter Link on bottom of page?', 'mooberry-story' ),
		'id'    =>  '_mbds_next_bottom',
		'type'  =>  'select',
		'options'    =>  $yes_no,
		)));
	
	$story_display_meta_box->add_field( apply_filters('mbds_story_toc_bottom', array(
		'name'  =>  __('Show Table of Contents Link on bottom of page?', 'mooberry-story' ),
		'id'    =>  '_mbds_toc_bottom',
		'type'  =>  'select',
		'options'    =>  $yes_no,
		)));
}

add_action('add_meta_boxes_mbds_story', 'mbds_add_posts_meta_box');
function mbds_add_posts_meta_box() {
	
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_mbds_';
	add_meta_box( $prefix . 'posts_meta_box', __( 'Story Posts', 'mooberry-story' ), 'mbds_posts_meta_box', 'mbds_story', 'normal',
         'default');
}

function mbds_posts_meta_box() {
	global $post;
	echo '<p>' . __('Drag and drop the items to reorder them.', 'mooberry-story') . '</p>';
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

	// v1.2.1 -- add check for posts to be blank
	if (isset($_POST['posts']) && $_POST['posts'] != '') {
	
		// $_POST['posts']  = "mbds_post[]=2131&mbds_post[]=2135&mbds_post[]=2133&mbds_post[]=2243&mbds_post[]=2245&mbds_post[]=2247&mbds_post[]=2249&mbds_post[]=2251&mbds_post[]=2253&mbds_post[]=2255&mbds_post[]=2257&mbds_post[]=2259"
		
		// parse_str($_POST['posts']) creates variable $mbds_post which is an array of post ids
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
    $columns['mbds_type'] = __('Type', 'mooberry-story');
	$columns['mbds_length'] = __('Number of Posts', 'mooberry-story');
	$columns['mbds_complete'] = __('Complete', 'mooberry-story');
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
				echo __('Yes', 'mooberry-story');
			} else {
				echo __('No', 'mooberry-story');
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
