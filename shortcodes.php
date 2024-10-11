<?php

add_shortcode( 'mbs_next', 'mbds_shortcode_next' );
add_shortcode( 'mbs_prev', 'mbds_shortcode_prev' );
add_shortcode( 'mbs_summary', 'mbds_shortcode_summary' );
add_shortcode( 'mbs_cover', 'mbds_shortcode_cover' );
add_shortcode( 'mbs_toc', 'mbds_shortcode_toc' );
add_shortcode( 'mbs_toc_link', 'mbds_shortcode_toc_link' );


function mbds_get_storyID( $story ) {
	global $post;
	if ( $story == '' ) {
		return get_post_meta( $post->ID, '_mbds_story', true );
	} else {
		return mbds_get_storyID_by_slug( $story );
	}

}

function mbds_output_summary( $display, $postID ) {
	if ( $display == 'yes' ) {
		$summary = get_post_meta( $postID, '_mbds_summary', true );
		if ( $summary != '' ) {
			return ': ' . preg_replace( '/\\n/', '<br>', $summary );
		} else {
			return '';
		}
	}
}

function mbds_next_prev( $nextprev, $text, $story, $summary ) {
	global $post;
	$html_output = '';
	$storyID     = mbds_get_storyID( $story );
	$mbds_story  = mbds_get_story( $storyID );
	if ( $storyID == '' ) {
		return $html_output;
	}
	$posts = mbds_get_posts_list( $storyID );
	$found = null;
	foreach ( $posts as $one_post ) {
		if ( $one_post['ID'] == $post->ID ) {
			$found = $one_post['order'];
		}
	}

	if ( $found !== null ) {

		if ( $nextprev == 'next' ) {
			// make sure not last item if next
			$found ++;
			if ( $found >= count( $posts ) ) {
				$found = null;
			}
		}
		if ( $nextprev == 'prev' ) {
			// make sure no the first tiem
			if ( $found == 0 ) {
				$found = null;
			} else {
				$found = $found - 1;
			}
		}
		if ( $found !== null ) {
			$html_output .= '<div class="mbs_' . $nextprev . '">' . $text . ': <a href="' . $posts[ $found ]['link'] . '">';
			if ( isset( $mbds_story['_mbds_include_posts_name'] ) ) {
				$html_output .= '<span class="mbs_' . $nextprev . '_posts_name">' . mbds_display_posts_name( $mbds_story, $posts[ $found ]['ID'], true ) . ' </span>';
			}
			$html_output .= $posts[ $found ]['title'] . '</a>';
			$html_output .= mbds_output_summary( $summary, $posts[ $found ]['ID'] );
			$html_output .= '</div>';
		}
	}

	return $html_output;

}


function mbds_shortcode_next( $attr, $content ) {
	$attr = shortcode_atts( array(
		'summary' => 'no',
		'story'   => '',
	), $attr );

	return apply_filters( 'mbds_next_shortcode', mbds_next_prev( 'next', __( 'Next', 'mooberry-story' ), $attr['story'], $attr['summary'] ) );
}

function mbds_shortcode_prev( $attr, $content ) {
	$attr = shortcode_atts( array(
		'summary' => 'no',
		'story'   => '',
	), $attr );

	return apply_filters( 'mbds_prev_shortcode', mbds_next_prev( 'prev', __( 'Previous', 'mooberry-story' ), $attr['story'], $attr['summary'] ) );
}

function mbds_shortcode_summary( $attr, $content ) {
	$attr       = shortcode_atts( array( 'story' => '' ), $attr );
	$storyID    = mbds_get_storyID( $attr['story'] );
	$mbds_story = mbds_get_story( $storyID );

	$html_output = '<div class="mbs_story_summary">';
	if ( isset( $mbds_story['_mbds_summary'] ) ) {
		$html_output .= '<p>' . preg_replace( '/\\n/', '</p><p>', $mbds_story['_mbds_summary'] ) . '</p>';
	}
	$html_output .= get_post_field('post_content', $storyID );
	$html_output .= '</div>';

	return apply_filters( 'mbds_summary_shortcode', $html_output );
}


function mbds_shortcode_cover( $attr, $content ) {
	$attr        = shortcode_atts( array( 'story' => '' ), $attr );
	$storyID     = mbds_get_storyID( $attr['story'] );
	$mbds_story  = mbds_get_story( $storyID );
	$html_output = '';
	if ( isset( $mbds_story['_mbds_cover'] ) ) {
		$html_output = '<img class="mbs_cover_image" src="' . $mbds_story['_mbds_cover'] . '">';
	}

	return apply_filters( 'mbds_cover_shortcode', $html_output );
}

function mbds_shortcode_toc( $attr, $content ) {
	$attr       = shortcode_atts( array( 'story' => '' ), $attr );
	$storyID    = mbds_get_storyID( $attr['story'] );
	$mbds_story = mbds_get_story( $storyID );

	$html_output = '<div class="mbs_meta">';
	//$series = get_the_terms( $storyID, 'mbds_series');
	if ( isset( $mbds_story['series'] ) && is_array( $mbds_story['series'] ) && count( $mbds_story['series'] ) > 0 ) {
		$html_output .= '<div class="mbs_meta_series"><span class="mbs_meta_label mbs_meta_series_label">' . __( 'Series:', 'mooberry-story' ) . '</span> <span class="mbs_meta_value mbs_meta_series">' . get_the_term_list( $storyID, 'mbds_series', '', ', ' ) . '</span></div>';
	}
	//$genres = get_the_terms( $storyID, 'mbds_genre');
	if ( isset( $mbds_story['genres'] ) &&  is_array( $mbds_story['genres'] ) && count( $mbds_story['genres'] ) > 0 ) {
		$html_output .= '<div class="mbs_meta_genre"><span class="mbs_meta_label mbs_meta_genre_label">' . _n( 'Genre:', 'Genres:', count( $mbds_story['genres'] ), 'mooberry-story' ) . '</span> <span class="mbs_meta_value mbs_meta_genre">' . get_the_term_list( $storyID, 'mbds_genre', '', ', ' ) . '</div>';
	}
	$complete    = isset( $mbds_story['_mbds_complete'] ) ? __('Yes', 'mooberry-story') : __('No', 'mooberry-story');
	$html_output .= '<div class="mbs_meta_complete"><span class="mbs_meta_label mbs_meta_complete_label">' . __( 'Completed:', 'mooberry-story' ) . '</span> <span class="mbs_meta_value mbs_meta_complete">' . $complete;

	$total_word_count = mbds_get_story_word_count( $storyID );
	$html_output      .= '<div class="mbs_meta_word_count"><span class="mbs_meta_label mbs_meta_word_count_label">' . __( 'Word Count:', 'mooberry-story' ) . '</span> <span class="mbs_meta_value mbs_meta_word_count">' . $total_word_count;

	$html_output .= '<div class="mbs_toc"><h2 class="mbs_toc_title">' . __( 'Table of Contents', 'mooberry-story' ) . '</h2>';


	$html_output .= '</div><ul class="mbs_toc_list">';
	$posts       = mbds_get_posts_list( $storyID );
	foreach ( $posts as $each_post ) {
		$alt_title = get_post_meta( $each_post['ID'], '_mbds_alt_chapter_title', true );
		if ( $alt_title != '' ) {
			$each_post['title'] = $alt_title;
		}

		$html_output .= '<li><a href="' . $each_post['link'] . '">';
		if ( isset( $mbds_story['_mbds_include_posts_name'] ) ) {
			$html_output .= '<span class="mbs_toc_item_posts_name">' . mbds_display_posts_name( $mbds_story, $each_post['ID'] , true ) . ' </span>';
		}
		$html_output .= '<span class="mbs_toc_item_title">' . $each_post['title'] . '</span></a>';
		$html_output .= ' <span class="mbs_toc_item_word_count">(' . mbds_get_word_count( get_post_field( 'post_content', $each_post['ID'] ) ) . ' ' . __('words', 'mooberry-story') . ')</span></li>';
	}
	$html_output .= '</ul>';
	$html_output .= '</div>';

	return apply_filters( 'mbds_toc_shortcode', $html_output );
}

function mbds_shortcode_toc_link( $attr, $content ) {
	$attr        = shortcode_atts( array( 'story' => '' ), $attr );
	$storyID     = mbds_get_storyID( $attr['story'] );
	$html_output = '<a class="mbs_toc_link" href="' . get_permalink( $storyID ) . '">' . __( 'Table of Contents', 'mooberry-story' ) . '</a>';

	return apply_filters( 'mbds_toc_link_shortcode', $html_output );
}
