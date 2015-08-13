<?php
	
add_shortcode( 'mbs_next', 'mbds_shortcode_next' );
add_shortcode( 'mbs_prev', 'mbds_shortcode_prev' );
add_shortcode( 'mbs_summary', 'mbds_shortcode_summary');
add_shortcode( 'mbs_cover', 'mbds_shortcode_cover');
add_shortcode( 'mbs_toc', 'mbds_shortcode_toc');
add_shortcode( 'mbs_toc_link', 'mbds_shortcode_toc_link');


function mbds_get_storyID($story) {
	global $post;
	if ($story == '') {
		return get_post_meta($post->ID, '_mbds_story', true);
	} else {
		return mbds_get_storyID_by_slug($story);
	}
	
}

function mbds_output_summary($display, $postID) {
	if ($display == 'yes') {
		$summary = get_post_meta($postID, '_mbds_summary', true);
		if ($summary != '') {
			return ': ' .  preg_replace('/\\n/', '<br>', $summary);
		} else {
			return '';
		}
	}
}

function mbds_next_prev($nextprev, $text, $story, $summary) {
	global $post;
	$html_output = '';
	$storyID = mbds_get_storyID($story);
	$mbds_story = mbds_get_story($storyID);
	if ($storyID == '') {
		return $html_output;
	}	
	$posts = mbds_get_posts_list($storyID);
	$found = null;
	foreach($posts as $one_post) {
			if ($one_post['ID'] == $post->ID) {
				$found = $one_post['order'];
			}
	}
	
	if ($found !== null) {
		
		if ($nextprev == 'next') {
			// make sure not last item if next
			$found++;
			if ($found >= count($posts)) {
				$found = null;
			}
		}
		if ($nextprev == 'prev') {
			// make sure no the first tiem
			if ($found == 0) {
				$found = null;
			} else {
				$found = $found - 1;
			}
		}	
		if ($found !== null) {
			$html_output .= '<div class="mbs_' . $nextprev . '">' . $text . ': <a href="' . $posts[$found]['link'] . '">';
			if (isset($mbds_story['_mbds_include_posts_name'])) {
				$html_output .= '<span class="mbs_' . $nextprev . '_posts_name">' . mbds_display_posts_name($mbds_story, $posts[$found]['ID']) . ': </span>';
			}
			$html_output .= $posts[$found]['title'] . '</a>';
			$html_output .= mbds_output_summary($summary, $posts[$found]['ID']);
			$html_output .= '</div>';
		}
	}
	return $html_output;
		
}


function mbds_shortcode_next($attr, $content) {
	$attr = shortcode_atts(array('summary' => 'no',
								'story' => ''), $attr);
	
	return apply_filters('mbds_next_shortcode', mbds_next_prev('next', __('Next', 'mbd-blog-post-series'), $attr['story'], $attr['summary']));
}

function mbds_shortcode_prev($attr, $content) {
	$attr = shortcode_atts(array('summary' => 'no',
								'story' => ''), $attr);
								
	return apply_filters('mbds_prev_shortcode', mbds_next_prev('prev', __('Previous', 'mbd-blog-post-series'), $attr['story'], $attr['summary']));
}

function mbds_shortcode_summary($attr, $content) {
	$attr = shortcode_atts(array('story' => ''), $attr);
	$storyID = mbds_get_storyID($attr['story']);
	$mbds_story = mbds_get_story($storyID);
	mbd_log($attr['story']);
	mbd_log($storyID);
	mbd_log($mbds_story);
	$html_output = '<div class="mbs_story_summary">';
	if (isset($mbds_story['_mbds_summary'])) {
		$html_output .= '<p>' .  preg_replace('/\\n/', '</p><p>',$mbds_story['_mbds_summary']) . '</p>';
	}
	$html_output .= '</div>';
		
	return apply_filters('mbds_summary_shortcode', $html_output);
}


function mbds_shortcode_cover( $attr, $content) {
	$attr = shortcode_atts(array('story' => ''), $attr);
	$storyID = mbds_get_storyID($attr['story']);
	$mbds_story = mbds_get_story($storyID);
	$html_output = '';
	if (isset($mbds_story['_mbds_cover'])) {
		$html_output = '<img class="mbs_cover_image" src="' . $mbds_story['_mbds_cover'] . '">';
	}
	return apply_filters('mbds_cover_shortcode', $html_output);
}

function mbds_shortcode_toc( $attr, $content ) {
	$attr = shortcode_atts(array('story' => ''), $attr);
	$storyID = mbds_get_storyID($attr['story']);
	$mbds_story = mbds_get_story($storyID);
	$html_output = '<div class="mbs_toc"><h2 class="mbs_toc_title">' .  __('Table of Contents', 'mbd-blog-post-series') . '</h2>';
	$html_output .= '<ul class="mbs_toc_list">';
	$posts = mbds_get_posts_list( $storyID );
	foreach ($posts as $each_post) {
		$html_output .= '<li><a href="' . $each_post['link'] . '">';
		if (isset($mbds_story['_mbds_include_posts_name'])) {
			$html_output .= '<span class="mbs_toc_item_posts_name">' . mbds_display_posts_name($mbds_story, $each_post['ID']) . ': </span>';
		}
		$html_output .= '<span class="mbs_toc_item_title">' . $each_post['title'] . '</span></a></li>';
	}
	$html_output .= '</ul>';
	$html_output .= '</div>';
	return apply_filters('mbds_toc_shortcode', $html_output);
}

function mbds_shortcode_toc_link( $attr, $content) {
	$attr = shortcode_atts(array('story' => ''), $attr);
	$storyID = mbds_get_storyID($attr['story']);
	$html_output = '<a class="mbs_toc_link" href="' . get_permalink($storyID) . '">' . __('Table of Contents', 'mbd-blog-post-series') . '</a>';
	return apply_filters('mbds_toc_link_shortcode', $html_output);
}