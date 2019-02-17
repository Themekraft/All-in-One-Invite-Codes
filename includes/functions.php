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