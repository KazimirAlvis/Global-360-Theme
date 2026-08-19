<?php
/** Context-scoped presentation assets. */

add_action(
	'wp_enqueue_scripts',
	static function () {
		$base_path = get_template_directory() . '/assets/css/';
		$base_url  = get_template_directory_uri() . '/assets/css/';

		if ( is_page_template( 'page-find-a-doctor.php' ) || get_query_var( 'find_a_doctor_state' ) ) {
			wp_enqueue_style( 'global360-directory', $base_url . 'directory.css', array( 'global-360-theme-style' ), filemtime( $base_path . 'directory.css' ) );
		}

		if ( is_singular( 'doctor' ) ) {
			wp_enqueue_style( 'global360-doctor', $base_url . 'doctor.css', array( 'global-360-theme-style' ), filemtime( $base_path . 'doctor.css' ) );
		}
	},
	20
);
