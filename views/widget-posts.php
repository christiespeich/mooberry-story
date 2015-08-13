<p>
	<label for="<?php echo $this->get_field_id('mbds_pw_title'); ?>"><?php _e('Widget Title:', 'mbd-blog-post-series'); ?></label>
	<input type="text" name="<?php echo $this->get_field_name('mbds_pw_title'); ?>" id="<?php echo $this->get_field_id('mbds_pw_title'); ?>" value="<?php echo esc_attr($mbds_pw_title); ?>">
</p>
<p>
	<label id="<?php echo $this->get_field_id( 'mbds_pw_story' ); ?>_label" for="<?php echo $this->get_field_id( 'mbds_pw_story' ); ?>"><?php _e('Which story:', 'mbd-blog-post-series'); ?></label>
	<select id="<?php echo $this->get_field_id('mbds_pw_story'); ?>" name="<?php echo $this->get_field_name('mbds_pw_story'); ?>"  >
		<?php echo mbds_get_stories_dropdown( $mbds_pw_story ); ?>
	</select>
</p>
<p id="<?php echo $this->get_field_id( 'mbds_pw_count' ); ?>_p">
	<label  for="<?php echo $this->get_field_id('mbds_pw_count'); ?>"><?php _e('How many:', 'mbd-blog-post-series'); ?></label>
	<select id="<?php echo $this->get_field_id('mbds_pw_count'); ?>" name="<?php echo $this->get_field_name('mbds_pw_count'); ?>">
		<?php echo mbds_get_post_widget_dropdown( $mbds_pw_count );  ?>
	</select>
</p>
