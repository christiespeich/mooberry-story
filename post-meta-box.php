<?php
/************************************
	META BOX
************************************/
add_action( 'cmb2_init', 'mbds_init_post_meta_box' );
function mbds_init_post_meta_box() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_mbds_';

	$post_meta_box = new_cmb2_box( apply_filters('mbds_post_meta_box', array(
		'id'            => $prefix . 'post_meta_box',
		'title'         => __( 'Mooberry Story', MBDS_TEXT_DOMAIN ),
		'object_types'  => array( 'post', ), // Post type
		 'context'    => 'normal',
		 'priority'   => 'high',
		 'show_names' => true, // Show field names on the left
		
		
	) ) );

	$post_meta_box->add_field( apply_filters('mbds_posts_story_field', array(
		'name'       => __( 'Story', MBDS_TEXT_DOMAIN ),
		'id'         => $prefix . 'story',
		'type'       => 'select',
		'options'	=> mbds_get_story_list(),
		
	) ) );	
	/*
	$post_meta_box->add_field( apply_filters('mbds_posts_summary_field', array(
		'name'       => __( 'Summary', MBDS_TEXT_DOMAIN ),
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
	*/
}
	

/**********************************************************
	METABOX SAVING
**********************************************************/
add_action( 'cmb2_override__mbds_story_meta_save', 'mbds_story_save', 3, 4);
function mbds_story_save($override, $a, $args, $field_obj ) {
	
	
	global $post;
	$new_story = $a['value'];
	$mbds_story = get_post_meta($new_story, '_mbds_posts', true);
	
	// remove from any story already in 
	// get all stories
	// if this post id is in the posts array of the story, remove it
	// then renumber the array
	$stories = mbds_get_stories('all', null, null, null);
	foreach($stories as $each_story) {
		$story = mbds_get_story($each_story->ID);
	
		if (array_key_exists('_mbds_posts', $story)) {
			// if the ID is in the array, get the key
			$keys = array_keys($story['_mbds_posts'], $post->ID);
			
			foreach ($keys as $key) {
				// remove the post
				unset($story['_mbds_posts'][$key]);
			}
			// renumber the indices
			$story['_mbds_posts'] = array_values($story['_mbds_posts']);
			
			// update the story
			update_post_meta($each_story->ID, '_mbds_posts', $story['_mbds_posts']);
		}
	}
	
	
	// if assigned to a story, save the post at the end of the order
	if ($new_story != '0') {
		$mbds_story[] = $post->ID;
		$mbds_story = apply_filters('mbds_posts_save_story_field', $mbds_story);
		update_post_meta($new_story, '_mbds_posts', $mbds_story);
		// don't override, still need to save story setting
		return null;
	}
	
	// if story = 0 then there should be no story data saved
	if ($new_story == '0') {
		delete_post_meta($post->ID, '_mbds_story');
		// override. Don't save a 0 in the database
		return true;
	}
	

	
}


/*************************************************************************
	columns
**************************************************************************/

// Add to our admin_init function
add_filter('manage_post_posts_columns', 'mbds_add_post_columns');
function mbds_add_post_columns($columns) {
    $columns['mbds_story'] = __('Story', MBDS_TEXT_DOMAIN);
    return apply_filters('mbds_posts_columns', $columns);
}

// Add to our admin_init function
add_action('manage_posts_custom_column', 'mbds_render_post_columns', 10, 2);
function mbds_render_post_columns($column_name, $id) {
    switch ($column_name) {
		case 'mbds_story':
			$storyID = get_post_meta($id, '_mbds_story', true);
			$mbds_story = mbds_get_story($storyID);
			if (count($mbds_story)>0) {
				echo $mbds_story['title'];
				$orderID = array_search($id, $mbds_story['_mbds_posts']);
				if ($orderID !== false) {
					echo '<br>';
					// grab installment name
					$post_name = mbds_get_story_post_name( $storyID, 'single' );
					echo sprintf(__('%s %d of %d', MBDS_TEXT_DOMAIN), $post_name, $orderID+1, count($mbds_story['_mbds_posts']));
				}
			}
		break;
	}
}
/*
// Add to our admin_init function
add_action('quick_edit_custom_box',  'mbds_add_quick_edit', 10, 2);
function mbds_add_quick_edit($column_name, $post_type) {
    if ($column_name != 'mbds_story') return;
    ?>
    <fieldset class="inline-edit-col-left">
    <div class="inline-edit-col">
        <span class="title">Series</span>
        <input type="hidden" name="mbds_story_noncename" id="mbds_story_noncename" value="" />
		<select name='_mbds_story' id='_mbds_story'>
        <?php // Get all widget sets
			$story_list = mbds_get_story_list();
			foreach ($story_list as $storyID => $story) {
				echo "<option class='widget-option' value='{$storyID}'>{$story}</option>\n";
			}
		
            
        ?>
        </select>
    </div>
    </fieldset>
    <?php
}
*/
