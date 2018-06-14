<?php
// run after cpts have been registered
add_action( 'init', 'mbds_update_versions', 30 );
function mbds_update_versions() {

	$current_version = get_option(MBDS_PLUGIN_VERSION_KEY);

	if ($current_version == '') {
		$current_version = '1.2.3';
	}

	if (version_compare($current_version, '1.3', '<')) {
		// upgrade to 1.3 script
		// add new retailers
		mbds_upgrade_to_1_3();
	}

		update_option(MBDS_PLUGIN_VERSION_KEY, MBDS_PLUGIN_VERSION);

}

function mbds_upgrade_to_1_3() {
	$stories = mbds_get_story_list( true );
	foreach ( $stories as $story_id => $story ) {
		if ( $story_id != 0 ) {
			update_post_meta( $story_id, '_mbds_open_story', 'yes' );
		}
	}

}
