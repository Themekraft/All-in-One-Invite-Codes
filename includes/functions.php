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

	$md5 = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );

	if ( ! $md5 ) {
		$md5 = substr( md5( time() * rand() ), 0, 24 );
	}

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
		case 'disabled' :
			$status = __( 'Disabled', 'all-in-one-invite-codes' );
			break;
		case 'used' :
			$status = __( 'Used', 'all-in-one-invite-codes' );
			break;
		case 'validated' :
			$status = __( 'Validated', 'all-in-one-invite-codes' );
			break;
	}

	return $status;
}

function all_in_one_invite_codes_set_status( $code_id, $status ) {
	return update_post_meta( $code_id, 'tk_all_in_one_invite_code_status', $status );
}

function all_in_one_invite_codes_is_valide( $code_id ) {

	$status = get_post_meta( $code_id, 'tk_all_in_one_invite_code_status', true );

	if ( ! $status || $status == 'validated' ) {
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
function all_in_one_invite_codes_inviten_email_replace_shortcode( $string, $shortcode, $value ) {
	if ( strpos( $string, $shortcode ) >= 0 ) {
		$string = str_replace( $shortcode, $value, $string );
	}

	return $string;
}