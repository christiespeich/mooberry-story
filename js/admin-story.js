jQuery( document ).ready(function() {

	// this is only for saving stories not other posts
	if (jQuery('#_mbds_story_meta_box').length == 0) {
		return;
	}

	// bind a function to save the grid order via ajax
	jQuery('#publish').on('click', mbds_save);
	jQuery('#_mbds_type').on('click', mbds_type_change);
	jQuery('#_mbds_posts_name').on('click', mbds_posts_name_change);

	mbds_type_change();
	mbds_posts_name_change();

	// make the grid sortable
	jQuery('#mbds_post_grid').sortable({
		opacity: 0.5,
		placeholder : 'ui-state-highlight',
		cursor: 'pointer',
		create: mbds_post_grid_update,
		update: mbds_post_grid_update,
		deactivate: function () {
				window.unsaved_changes = true;
			}
	});
});

function mbds_type_change() {
	if (jQuery('#_mbds_type').val() == 'custom') {
		jQuery('.cmb2-id--mbds-custom-type').show();
	} else {
		jQuery('.cmb2-id--mbds-custom-type').hide();
	}
}

function mbds_posts_name_change() {
	if (jQuery('#_mbds_posts_name').val() == 'custom') {
		jQuery('.cmb2-id--mbds-custom-posts-name-single').show();
		jQuery('.cmb2-id--mbds-custom-posts-name-plural').show();
	} else {
		jQuery('.cmb2-id--mbds-custom-posts-name-single').hide();
		jQuery('.cmb2-id--mbds-custom-posts-name-plural').hide();
	}
}

// update the icons in the grid
function mbds_post_grid_update() {
	// remove all the classes and add ui-icon on all of the items in the grid
	jQuery('#mbds_post_grid li span').removeClass().addClass('ui-icon');
	// add a down arrow to the first item
	jQuery('#mbds_post_grid li').first().children('span').addClass('ui-icon-arrowthick-1-s');
	// add an up arrow to the last item
	jQuery('#mbds_post_grid li').last().children('span').addClass('ui-icon-arrowthick-1-n');
	// add an up and down arrow to any non-first and non-last item
	jQuery('#mbds_post_grid li').not(jQuery('#mbds_post_grid li').first().not(jQuery('#mbds_post_grid li').last())).children('span').addClass('ui-icon-arrowthick-2-n-s');
}

// save the sorted grid via ajax
function mbds_save() {
	var data = {
			'action': 'save_posts_grid',
			'storyID': jQuery('#post_ID').val(),
			'posts': jQuery('#mbds_post_grid').sortable('serialize'),
			'security': ajax_object.security
	};
	jQuery.post(ajax_object.ajax_url, data, mbds_after_save);
}

// function that's called after the save grid ajax
// not really anything to do...
function mbds_after_save() {

}
