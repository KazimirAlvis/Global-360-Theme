<?php
/**
 * Platform Core consumer bridge.
 *
 * Domain reads go through Core when available. Narrow legacy fallbacks preserve
 * the transition window without making Theme templates aware of meta aliases.
 */

if ( ! function_exists( 'global360_theme_site_context' ) ) {
	/** @return array<string,mixed> */
	function global360_theme_site_context() {
		if ( function_exists( 'global360_platform' ) ) {
			return global360_platform()->site_context()->all();
		}
		$value = get_option( '360_global_settings', array() );
		return is_array( $value ) ? $value : array();
	}
}

if ( ! function_exists( 'global360_theme_clinic' ) ) {
	/** @return array<string,mixed>|null */
	function global360_theme_clinic( $post_id ) {
		static $cache = array();
		$post_id = absint( $post_id );
		if ( array_key_exists( $post_id, $cache ) ) {
			return $cache[ $post_id ];
		}
		if ( function_exists( 'global360_platform' ) ) {
			$cache[ $post_id ] = global360_platform()->clinics()->get( $post_id );
			return $cache[ $post_id ];
		}
		if ( 'clinic' !== get_post_type( $post_id ) ) {
			return null;
		}
		$doctor_ids = array();
		$candidates = get_posts(
			array(
				'post_type'      => 'doctor',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		foreach ( array_map( 'absint', $candidates ) as $doctor_id ) {
			if ( in_array( $post_id, array_map( 'absint', (array) get_post_meta( $doctor_id, 'clinic_id', true ) ), true ) ) {
				$doctor_ids[] = $doctor_id;
			}
		}
		$cache[ $post_id ] = array(
			'wp_id'              => $post_id,
			'name'               => get_the_title( $post_id ),
			'bio'                => (string) get_post_meta( $post_id, '_cpt360_clinic_bio', true ),
			'phone'              => (string) get_post_meta( $post_id, '_cpt360_clinic_phone', true ),
			'website'            => (string) get_post_meta( $post_id, '_clinic_website_url', true ),
			'addresses'          => (array) get_post_meta( $post_id, 'clinic_addresses', true ),
			'state_codes'        => (array) get_post_meta( $post_id, 'clinic_states', true ),
			'clinic_info'        => (array) get_post_meta( $post_id, 'clinic_info', true ),
			'reviews'            => (array) get_post_meta( $post_id, 'clinic_reviews', true ),
			'doctor_ids'         => $doctor_ids,
			'logo_attachment_id' => absint( get_post_meta( $post_id, '_clinic_logo_id', true ) ),
		);
		return $cache[ $post_id ];
	}
}

if ( ! function_exists( 'global360_theme_doctor' ) ) {
	/** @return array<string,mixed>|null */
	function global360_theme_doctor( $post_id ) {
		static $cache = array();
		$post_id = absint( $post_id );
		if ( array_key_exists( $post_id, $cache ) ) {
			return $cache[ $post_id ];
		}
		if ( function_exists( 'global360_platform' ) ) {
			$cache[ $post_id ] = global360_platform()->doctors()->get( $post_id );
			return $cache[ $post_id ];
		}
		if ( 'doctor' !== get_post_type( $post_id ) ) {
			return null;
		}
		$cache[ $post_id ] = array(
			'wp_id'               => $post_id,
			'name'                => (string) ( get_post_meta( $post_id, 'doctor_name', true ) ?: get_the_title( $post_id ) ),
			'title'               => (string) get_post_meta( $post_id, 'doctor_title', true ),
			'bio'                 => (string) get_post_meta( $post_id, 'doctor_bio', true ),
			'photo_attachment_id' => absint( get_post_meta( $post_id, '_doctor_photo_id', true ) ),
			'clinic_ids'          => array_values( array_filter( array_map( 'absint', (array) get_post_meta( $post_id, 'clinic_id', true ) ) ) ),
			'locations'           => array(),
		);
		return $cache[ $post_id ];
	}
}
