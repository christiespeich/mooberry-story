<?php

class mbds_Posts_Widget extends WP_Widget {
	function __construct() {
		 parent::__construct(
			 
			// base ID of the widget
			'mbds_posts_widget',
			 
			// name of the widget
			__('Mooberry Story - Chapters List', 'mooberry-story' ),
			 
			// widget options
			array (
				'description' => __( 'Displays a list of chapters of a story.', 'mooberry-story' ),
				'classname' => 'mbds_Posts_Widget',
			)
			 
		);
	}
	 
	function form( $instance ) {
	
	 	if ($instance) {
			$mbds_pw_story = $instance['mbds_pw_story'];
			$mbds_pw_title = $instance['mbds_pw_title'];
			$mbds_pw_count = $instance['mbds_pw_count'];
		} else {
			$mbds_pw_story = 'all';
			$mbds_pw_title = '';
			$mbds_pw_count = '';
		}
		
		include dirname( __FILE__ ) . '/views/widget-posts.php';
	}
	 
	function update( $new_instance, $old_instance ) {       
		$instance = $old_instance;
		$instance['mbds_pw_story'] = strip_tags( $new_instance['mbds_pw_story'] );
		$instance['mbds_pw_title'] = strip_tags( $new_instance['mbds_pw_title'] );
		$instance['mbds_pw_count'] = strip_tags( $new_instance['mbds_pw_count'] );
 
		return $instance;
	}
	 
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;        
		echo $before_title . esc_html($instance['mbds_pw_title']) . $after_title; 

		switch ($instance['mbds_pw_count']) {
			case 'all':
				$posts = mbds_get_posts_list($instance['mbds_pw_story']);
				break;
			case 'latest':
				$posts = mbds_get_most_recent_post($instance['mbds_pw_story']);
				break;
		}
		if ($posts != null) {
			echo '<ol class="mbs_posts_widget_list">';
			
			foreach ($posts as $post) {
				echo '<li><a href="' . $post['link'] . '">' . $post['title'] . '</a></li>';
			}
			echo '</ol>';
		} else {
			$posts_name = mbds_get_story_post_name($instance['mbds_pw_story'], 'plural');
			echo sprintf(__('No %s found', 'mooberry-story'), $posts_name);
		}
		echo $after_widget;
		 
	} 
}