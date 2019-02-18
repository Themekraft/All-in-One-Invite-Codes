<?php

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

function all_in_one_invite_codes_is_default_registration(){
	$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );
	if(isset($all_in_one_invite_codes_general['default_registration']) && $all_in_one_invite_codes_general['default_registration'] == 'disable'){
		return false;
	}
	return true;
}

function all_in_one_invite_codes_get_status( $post_id ){

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	switch ( $status ) {
		case 'disabled' :
			$status = __( 'Disabled', 'all-in-one-invite-codes' );
			break;
		case 'used' :
			$status = __( 'Used', 'all-in-one-invite-codes' );
			break;
		default:
			$status = __( 'Active', 'all-in-one-invite-codes' );
			break;
	}

	return $status;
}