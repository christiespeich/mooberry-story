jQuery( document ).ready(function() {

	// bind the change event on all the drop down
	jQuery('#_mbds_story').on('change', mbds_post_story_change);


	// set visibility of everything as needed
	mbds_post_story_change();



});

function mbds_post_story_change() {
	var post_series = jQuery('#_mbds_story').val();
	switch (post_series) {
		case '0':
			// if no series selected hide everything else
			jQuery('.cmb2-id--mbds-story').nextAll('div').hide();
			break;
		default:
			// show everything but New Series Name
			jQuery('.cmb2-id--mbds-story').nextAll('div').show();
	}

}



