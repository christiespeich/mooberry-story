<?php
	
add_shortcode( 'mbs_next', 'mbds_shortcode_next' );
add_shortcode( 'mbs_prev', 'mbds_shortcode_prev' );
add_shortcode( 'mbs_summary', 'mbds_shortcode_summary');


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
				$html_output .= '<span class="mbs_' . $nextprev . '_posts_name">' . mbds_display_posts_name($mbds_story, $posts[$found]['ID']) . '</span>';
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
	
	return apply_filters('mbds_next_shortcode', mbds_next_prev('next', __('Next', MBDS_TEXT_DOMAIN), $attr['story'], $attr['summary']));
}

function mbds_shortcode_prev($attr, $content) {
	$attr = shortcode_atts(array('summary' => 'no',
								'story' => ''), $attr);
								
	return apply_filters('mbds_prev_shortcode', mbds_next_prev('prev', __('Previous', MBDS_TEXT_DOMAIN), $attr['story'], $attr['summary']));
}

function mbds_shortcode_summary($attr, $content) {
	$attr = shortcode_atts(array('story' => ''), $attr);
	$storyID = mbds_get_storyID($story);
	$mbds_story = mbds_get_story($storyID);
	$html_output = '<p class="mbs_story_summary">' . $mbds_story['summary'] . '</p>';
	return $html_output;
}
