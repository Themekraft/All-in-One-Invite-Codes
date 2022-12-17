<?php

/**
 * Get existing or create new Invite Code
 *
 * @since  0.1
 *
 * return md5 code
 */
function all_in_one_invite_codes_md5( $post_id = false ) {
	global $post;

	if ( ! $post_id ) {
		$post_id = $post->ID;
	}

	$md5                             = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );
	$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );
	$code_length                     = $all_in_one_invite_codes_general['character_length'] ?? 5;

	if ( ! $md5 ) {
		$md5 = substr( md5( time() * rand() ), 0, $code_length );
	}

	if ( ! $md5 ) {
		$md5 = substr( md5( time() * rand() ), 0, 24 );
	}
	$md5 = sanitize_key( $md5 );
	return $md5;
}

/**
 * Check if the WordPress default registration should get protected with an invite only form element
 *
 * @since  0.1
 *
 * return boolean
 */
function all_in_one_invite_codes_is_default_registration() {
	$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );
	if ( isset( $all_in_one_invite_codes_general['default_registration'] ) && $all_in_one_invite_codes_general['default_registration'] == 'disable' ) {
		return false;
	}

	return true;
}

/**
 * Get the invite code status in a readable form
 *
 * @since  0.1
 *
 * return text
 */
function all_in_one_invite_codes_get_status( $post_id ) {

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	switch ( $status ) {
		case 'disabled':
			$status = __( 'Disabled', 'all-in-one-invite-codes' );
			break;
		case 'used':
			$status = __( 'Used', 'all-in-one-invite-codes' );
			break;
		case 'valide':
			$status = __( 'Valide', 'all-in-one-invite-codes' );
			break;
	}

	return $status;
}

function all_in_one_invite_codes_get_code_id_by_code( $code ) {
	// Get the invite code
	$args  = array(
		'post_type'  => 'tk_invite_codes',
		'meta_query' => array(
			array(
				'key'     => 'tk_all_in_one_invite_code',
				'value'   => sanitize_key( trim( $_POST['tk_invite_code'] ) ),
				'compare' => '=',
			),
		),
	);
	$query = new WP_Query( $args );

	$podt_id = 0;
	// Get the invite code id
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) :
			$query->the_post();
			$podt_id = get_the_ID();
		endwhile;
	}
	return $podt_id;
}

function all_in_one_invite_codes_is_valide( $code_id ) {

	$status = get_post_meta( $code_id, 'tk_all_in_one_invite_code_status', true );

	if ( ! $status || $status == 'valide' || $status == 'Active' ) {
		return true;
	}

	return false;
}


/**
 * Process the email notification shortcodes
 *
 * @since  0.1
 *
 * return text
 */
function all_in_one_invite_codes_replace_shortcode( $string, $shortcode, $value ) {
	if ( strpos( $string, $shortcode ) >= 0 ) {
		$string = str_replace( $shortcode, $value, $string );
	}

	return $string;
}

/**
 * Create the code default values
 */
function all_in_one_invite_codes_options_defaults() {
	$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );

	return array(
		'email'          => '',
		'generate_codes' => isset( $all_in_one_invite_codes_general['generate_codes_amount'] ) ? $all_in_one_invite_codes_general['generate_codes_amount'] : '',
	);
}
