<?php

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite', 'all_in_one_invite_codes_send_invite' );

function all_in_one_invite_codes_send_invite(){

	$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' );

	$md5 = get_post_meta( $_POST['post_id'], 'tk_all_in_one_invite_code', true );

	$to = $_POST['to'];
	$subject = $_POST['subject'];
	$body = $_POST['message_text'];
	$headers = array('Content-Type: text/html; charset=UTF-8');
	$invite_code = $md5;

	// Replace Buddytext Shortcodes with form element values


	// Site Name
	$site_name = get_bloginfo( 'name' );
	$subject = all_in_one_invite_codes_inviten_email_replace_shortcode( $subject, '[site_name]', $site_name );
	$body = all_in_one_invite_codes_inviten_email_replace_shortcode( $body, '[site_name]', $site_name );


	// Invite Link
	$invite_link = '<a href="' . wp_registration_url() . '&invite_code=' . $invite_code . '">Link</a>' ;
	$subject = all_in_one_invite_codes_inviten_email_replace_shortcode( $subject, '[invite_link]', $invite_link );
	$body = all_in_one_invite_codes_inviten_email_replace_shortcode( $body, '[invite_link]', $invite_link );


	$send = wp_mail( $to, $subject, $body, $headers );

	$all_in_one_invite_codes_options = get_post_meta( $_POST['post_id'], 'all_in_one_invite_codes_options', true );

	if( empty($all_in_one_invite_codes_options['email'])){
		$all_in_one_invite_codes_options['email'] = $to;
		update_post_meta( $_POST['post_id'], 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
	}




	if ( ! $send ) {
		$json['error'] = __( 'Invite could not get send. please contact the Support.', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();
	}
	$json['message'] = __('Invite send successfully', 'buddyforms');;

	echo json_encode( $json );
	die();
}