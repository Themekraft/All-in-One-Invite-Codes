<?php

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite', 'all_in_one_invite_codes_send_invite' );

function all_in_one_invite_codes_send_invite(){

	$to = 'sendto@example.com';
	$subject = 'The subject';
	$body = 'The email body content';
	$headers = array('Content-Type: text/html; charset=UTF-8');

	$send = wp_mail( $to, $subject, $body, $headers );

	if ( ! $send ) {
		$json['error'] = __( 'Used or Disabled Invite Codes can not get changed.', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();
	}
	$json['message'] = __('Invite send successfully', 'buddyforms');;

	echo json_encode( $json );
	die();
}