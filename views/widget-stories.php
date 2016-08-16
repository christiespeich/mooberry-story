<p>
	<label for="<?php echo $this->get_field_id('mbds_sw_title'); ?>"><?php _e('Widget Title:', 'mooberry-story'); ?></label>
	<input type="text" name="<?php echo $this->get_field_name('mbds_sw_title'); ?>" id="<?php echo $this->get_field_id('mbds_sw_title'); ?>" value="<?php echo esc_attr($mbds_sw_title); ?>">
</p>
<p>
	<label id="<?php echo $this->get_field_id( 'mbds_sw_stories' ); ?>_label" for="<?php echo $this->get_field_id( 'mbds_sw_stories' ); ?>"><?php _e('Which stories to list:', 'mooberry-story'); ?></label>
	<select id="<?php echo $this->get_field_id('mbds_sw_stories'); ?>" name="<?php echo $this->get_field_name('mbds_sw_stories'); ?>" onChange="javascript: mbds_sw_stories_change('<?php echo $this->get_field_id('mbds_sw_stories'); ?>', '<?php echo  $this->get_field_id('mbds_sw_genre') . '_p'; ?>', '<?php echo  $this->get_field_id('mbds_sw_series') . '_p'; ?>');" >
		<?php echo mbds_get_story_widget_dropdown( $mbds_sw_stories ); ?>
	</select>
</p>
<p id="<?php echo $this->get_field_id( 'mbds_sw_genre' ); ?>_p" style="display:<?php echo ($mbds_sw_stories=='genre') ? 'block' : 'none'; ?>">
	<label  for="<?php echo $this->get_field_id('mbds_sw_genre'); ?>"><?php _e('Which genre:', 'mooberry-story'); ?></label>
	<select id="<?php echo $this->get_field_id('mbds_sw_genre'); ?>" name="<?php echo $this->get_field_name('mbds_sw_genre'); ?>">
		<?php echo mbds_get_tax_terms_dropdown( 'mbds_genre', $mbds_sw_genre );  ?>
	</select>
</p>
<p id="<?php echo $this->get_field_id( 'mbds_sw_series' ); ?>_p" style="display:<?php echo ($mbds_sw_stories=='series') ? 'block' : 'none'; ?>">
	<label  for="<?php echo $this->get_field_id('mbds_sw_series'); ?>"><?php _e('Which series:', 'mooberry-story'); ?></label>
	<select id="<?php echo $this->get_field_id('mbds_sw_series'); ?>" name="<?php echo $this->get_field_name('mbds_sw_series'); ?>">
		<?php echo mbds_get_tax_terms_dropdown( 'mbds_series', $mbds_sw_series ); ?>
	</select>
</p>