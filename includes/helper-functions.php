<?php
function mbds_log($var) {
		error_log(print_r($var, true)); 
}

function mbds_get_storyID_by_slug($story) {
	$posts = get_posts(array('posts_per_page' => -1,
				'post_status'	=> 'publish',
				'post_type' => 'mbds_story',
				'name' => $story,
				));
	wp_reset_postdata();
	if (count($posts)>0) {
		return $posts[0]->ID;
	} else {
		return '';
	}
	
}

function mbds_get_story_list() {
		$posts = get_posts(array('posts_per_page' => -1,
				'post_status'	=> 'publish',
				'post_type' => 'mbds_story',
				));
		$stories = array();
		$stories['0'] = '';
		foreach ($posts as $post) {
			$stories[$post->ID] = $post->post_title;
		}
		wp_reset_postdata();
		return apply_filters('mbds_story_list', $stories);
}

function mbds_get_story( $storyID ) {
	$posts = get_posts(array('posts_per_page' => -1,
				'post_status'	=> 'publish',
				'post_type' => 'mbds_story',
				'post__in' => array($storyID)
				));
	$story = array();
	if (count($posts)>0) {
		$story['ID'] = $storyID;
		$story['title'] = $posts[0]->post_title;
		$story['link'] = get_permalink($storyID);
		$meta_data = get_post_meta($storyID);
		foreach ($meta_data as $key => $data) {
			$story[$key] = $meta_data[$key][0];
		}
		if (isset($story['_mbds_posts'])) {
			$story['_mbds_posts'] = unserialize($story['_mbds_posts']);
		}
		// v1.2.1 make sure _mbds_posts is an array
		if ( !array_key_exists( '_mbds_posts', $story ) || !is_array( $story['_mbds_posts'] ) ) {
			$story['_mbds_posts'] = array();
		}
		$story['genres'] = wp_get_post_terms( $storyID, 'mbds_genre');
		$story['series'] = wp_get_post_terms( $storyID, 'mbds_series');
	}
	wp_reset_postdata();
	return apply_filters('mbds_story', $story);
}

function mbds_get_stories($filter, $complete, $series, $genre) {
	//$meta_query = array();
	//$tax_query = array();
	
	$args = array(
			'post_type'	=>	'mbds_story',
			'posts_per_page' => -1,
			'post_status'	=> 'publish',
			'order'			=> 'ASC',
			'orderby'		=> 'title');
	

	
	if ($filter == 'recent') {
		// get all posts that are part of a story
		// order by post_modified desc
		// the first one returned is the most recently updated
		// grab the storyid of that one and then query for stories
		$post_args = array(
				'post_type'	=>	'post',
				'posts_per_page' => -1,
				'post_status'	=> 'publish',
				'order'			=> 'DESC',
				'orderby'		=> 'post_modified',
				'meta_query'	=> array(array(
								'key'	=>	'_mbds_story',
								'compare'	=>	'EXISTS'))
				);
		$posts = get_posts($post_args);
		if (count($posts)>0) {
			$storyID = get_post_meta($posts[0]->ID, '_mbds_story', true);
			$args['post__in'] = array($storyID);
		} else {
			return null;  // nothing to return 
		}
	}
	
	
	if ($complete == 'complete') {
		$meta_query[] = array( 	'key'		=>	'_mbds_complete',
								'value'		=>	'on',
								'compare'	=>	'=',
						);
	}
	if ($complete == 'incomplete') {
		$meta_query[] = array( 	'key'		=>	'_mbds_complete',
								'value'		=>	'bug #23268',
								'compare'	=>	'NOT EXISTS',
						);
	}
	if ($series != null) {
		$tax_query[] = array(	'taxonomy'	=>	'mbds_series',
								'terms'		=>	$series,
								'operator'	=>	'IN',
						);
	}
	if ($genre != null) {
		$tax_query[] = array(	'taxonomy'	=>	'mbds_genre',
								'terms'		=>	$genre,
								'operator'	=>	'IN',
						);
	}
	if (isset($meta_query)) {
		if (count($meta_query) > 1) {
			$meta_query = array(	'relation' 	=>	'AND',
									$meta_query);
		} else {
			$meta_query = array( $meta_query);
		}
	}
	if (isset($tax_query)) {
		if (count($tax_query) > 1) {
			$tax_query = array(	'relation' 	=>	'AND',
									$tax_query);
		} else {
			$tax_query = array( $tax_query);
		}
	}
	
	if (isset($meta_query)) {
		$args['meta_query']	= $meta_query;
	}
	if (isset($tax_query)) {
			$args['tax_query']	= $tax_query;
	
	}
	$stories = get_posts($args);
	wp_reset_postdata();
	return apply_filters('mbds_stories', $stories);
						
						
}

function mbds_get_posts_list( $storyID ) {
	
	//$mbdbps_series = get_option('mbdbps_series');
	$posts_list = get_post_meta($storyID, '_mbds_posts', true);
	
	if ($posts_list == '') {
		return apply_filters('mbds_posts_list', array());
	}
		
	$args = array(
				'posts_per_page' => -1,
				'post_status'	=> 'publish',
				'post__in' => $posts_list);
	
	$posts = get_posts( $args ); 
	
	
	foreach ($posts as $post) {
	
		$key = array_search($post->ID, $posts_list);
		if ($key !== false) {
			$posts_list[$key] = array('ID' => $post->ID,
									'title' => $post->post_title,
									'link'	=> get_permalink($post->ID),
									'order'	=> $key);
		}
	}
	
	wp_reset_postdata();
	
	return apply_filters('mbds_posts_list', $posts_list);

}

function mbds_get_most_recent_post( $storyID) {
		$post_args = array(
				'post_type'	=>	'post',
				'posts_per_page' => -1,
				'post_status'	=> 'publish',
				'order'			=> 'DESC',
				'orderby'		=> 'post_modified',
				'meta_query'	=> array(array(
								'key'	=>	'_mbds_story',
								'compare'	=>	'=',
								'value'	=>	$storyID))
				);
		$posts = get_posts($post_args);
		wp_reset_postdata();
		if (count($posts)>0) {
			return array(array('ID' => $posts[0]->ID,
									'title' => $posts[0]->post_title,
									'link'	=> get_permalink($posts[0]->ID),
									'order'	=> '0'));
		} else {
			return null;
		}
		
}

function mbds_get_post_names() {
	return apply_filters('mbds_post_names', array(
		'chapters' => array('single' => __('Chapter', 'mooberry-story'),
							'plural'	=> __('Chapters', 'mooberry-story')),
		'episodes' => array('single' => __('Episode', 'mooberry-story'),
							'plural'	=> __('Episodes', 'mooberry-story')),
		'parts' => array('single' => __('Part', 'mooberry-story'),
							'plural' => __('Parts', 'mooberry-story')),
		));
}

function mbds_get_post_names_options() {
	$post_names = mbds_get_post_names();
	$options = array();
	foreach ($post_names as $key => $post_name) {
		$options[$key] = $post_names[$key]['plural'];
	}
	$options['custom'] = __('Custom', 'mooberry-story');
	return apply_filters('mbds_posts_names_options', $options);
}

function mbds_get_story_post_name( $storyID, $single_plural ) {

	if ($single_plural != 'single' && $single_plural != 'plural') {
		return '';
	}
	$post_name = get_post_meta($storyID, '_mbds_posts_name', true);
	if ($post_name == 'custom') {
		return apply_filters('mbds_story_posts_name', get_post_meta($storyID, '_mbds_custom_post_name_' . $single_plural, true));
	} else {
		$post_names = mbds_get_post_names();
	
		if (isset($post_names[$post_name])) {
			return apply_filters('mbds_story_posts_name', $post_names[$post_name][$single_plural]);
		} else {
			return '';
		}
	}
	
}

function mbds_display_posts_name($story, $postID) {
	$posts_name = mbds_get_story_post_name($story['ID'], 'single');
	$count = array_search($postID, $story['_mbds_posts']);
	$count++;
	return apply_filters('mbds_display_posts_name', $posts_name . ' ' . $count);
}

function mbds_output_dropdown( $options, $selected) {
	$html_output = '';
	foreach ($options as $value => $option) {
		$html_output .= '<option value="' . $value . '" ';
		if ($selected == $value) {
			$html_output .= 'selected';
		}
		$html_output .= '>' . $option  . '</option>';
	}
	return apply_filters('mbds_dropdown', $html_output);
}
	
function mbds_get_story_widget_dropdown( $selected ) {
	$options = array(
					'all' => __('All Stories', 'mooberry-story'),
					'complete' => __('Completed Stories', 'mooberry-story'),
					'incomplete' => __('Unfinished Stories', 'mooberry-story'),
					'recent' => __('Last Updated Story', 'mooberry-story'),
					'series' => __('Stories in a Series', 'mooberry-story'),
					'genre' => __('Stories in a Genre', 'mooberry-story'),
				);
	
	return apply_filters('mbds_story_widget_dropdown', mbds_output_dropdown($options, $selected));
	
}

function mbds_get_tax_terms_dropdown ($taxonomy, $selected) {
	$args = array( 'order' => 'ASC',
					'orderby'	=> 'name');
	$terms = get_terms($taxonomy, $args);
	$options = array();
	foreach($terms as $term) {
		$options[$term->term_id] = $term->name;
	}
	return apply_filters('mbds_tax_terms_dropdown', mbds_output_dropdown( $options, $selected));
	
}



function mbds_get_stories_dropdown( $selected ) {
	$stories = mbds_get_story_list();
	return apply_filters('mbds_stories_dropdown', mbds_output_dropdown( $stories, $selected));
}

function mbds_get_post_widget_dropdown( $selected ){
	$options = array( 'all' => __('All', 'mooberry-story'),
						'latest' => __('Most Recent', 'mooberry-story'));
	return apply_filters('mbds_post_widget_dropdown', mbds_output_dropdown( $options, $selected ));
}