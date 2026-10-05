<?php
/** Context-scoped presentation assets. */

add_action(
	'wp_enqueue_scripts',
	static function () {
		$base_path = get_template_directory() . '/assets/css/';
		$base_url  = get_template_directory_uri() . '/assets/css/';

		if ( is_page( array( 'patient-reviews', 'leave-a-review' ) ) ) {
			wp_enqueue_style( 'global360-patient-reviews', $base_url . 'patient-reviews.css', array( 'global-360-theme-style' ), filemtime( $base_path . 'patient-reviews.css' ) );
		}

		if ( is_page_template( 'page-find-a-doctor.php' ) || get_query_var( 'find_a_doctor_state' ) ) {
			wp_enqueue_style( 'global360-directory', $base_url . 'directory.css', array( 'global-360-theme-style' ), filemtime( $base_path . 'directory.css' ) );
		}

		if ( is_singular( 'doctor' ) ) {
			wp_enqueue_style( 'global360-doctor', $base_url . 'doctor.css', array( 'global-360-theme-style' ), filemtime( $base_path . 'doctor.css' ) );
		}

		if ( ! is_page_template( 'page-linktree.php' ) ) {
			wp_enqueue_style( 'global360-floating-assessment', $base_url . 'floating-assessment.css', array( 'global-360-theme-style' ), filemtime( $base_path . 'floating-assessment.css' ) );
		}

		$uses_latest_article_cards = ( is_home() && ! get_query_var( 'find_a_doctor_state' ) )
			|| is_page( 'blog' )
			|| is_category()
			|| is_tag()
			|| is_author()
			|| is_date()
			|| is_post_type_archive( 'post' );

		if ( $uses_latest_article_cards && wp_style_is( 'global360blocks-latest-articles-style', 'registered' ) ) {
			wp_enqueue_style( 'global360blocks-latest-articles-style' );
		}
	},
	20
);
