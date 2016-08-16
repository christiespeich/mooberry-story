<?php

class mbds_Story_Widget extends WP_Widget {
	function __construct() {
		 parent::__construct(
			 
			// base ID of the widget
			'mbds_story_widget',
			 
			// name of the widget
			__('Mooberry Story - Stories List', 'mooberry-story' ),
			 
			// widget options
			array (
				'description' => __( 'Displays a list of stories.', 'mooberry-story' ),
				'classname' => 'mbds_Story_Widget',
			)
			 
		);
	}
	 
	function form( $instance ) {
	
	 	if ($instance) {
			$mbds_sw_stories = $instance['mbds_sw_stories'];
			$mbds_sw_title = $instance['mbds_sw_title'];
			$mbds_sw_genre = $instance['mbds_sw_genre'];
			$mbds_sw_series = $instance['mbds_sw_series'];
		} else {
			$mbds_sw_stories = 'all';
			$mbds_sw_title = '';
			$mbds_sw_genre = '';
			$mbds_sw_series = '';
		}
		
		include dirname( __FILE__ ) . '/views/widget-stories.php';
	}
	 
	function update( $new_instance, $old_instance ) {       
		$instance = $old_instance;
		$instance['mbds_sw_stories'] = strip_tags( $new_instance['mbds_sw_stories']);
		$instance['mbds_sw_title'] = strip_tags($new_instance['mbds_sw_title']);
		$instance['mbds_sw_genre'] = strip_tags($new_instance['mbds_sw_genre']);
		$instance['mbds_sw_series'] = strip_tags($new_instance['mbds_sw_series']);
		return $instance;
	}
	 
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;        
		echo $before_title . esc_html($instance['mbds_sw_title']) . $after_title; 
	
		// get list of stories
		switch ($instance['mbds_sw_stories']) {
			case 'all':
				$stories = mbds_get_stories('all', null, null, null);
				break;
			case 'complete':
				$stories = mbds_get_stories('all', 'complete', null, null);
				break;
			case 'incomplete':
				$stories = mbds_get_stories('all', 'incomplete', null, null);
				break;
			case 'recent':
				$stories = mbds_get_stories('recent', null, null, null);
				break;
			case 'series':
				$stories = mbds_get_stories('all', null, $instance['mbds_sw_series'], null);
				break;
			case 'genre':
				$stories = mbds_get_stories('all', null, null, $instance['mbds_sw_genre']);
				break;
		}
		if ($stories != null) {
			echo '<ul class="mbs_story_widget_list">';
			foreach ($stories as $story) {
				echo '<li><a href="' . get_the_permalink($story->ID) . '">' . $story->post_title . '</a></li>';
			}
			echo '</ul>';
		} else {
			echo '<span class="mbs_story_widget_none">';
			echo __('No stories found', 'mooberry-story');
			echo '</span>';
		}
		echo $after_widget;
		 
	} 
}