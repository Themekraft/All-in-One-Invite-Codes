<?php

/**
 * Validate and process the code.
 *
 * @since  0.1
 * @return text
 */
function all_in_one_invite_codes_validate_code( $code, $user_email = '' ) {

	// Get all invite codes with this code. Should only be one post.
	$args  = array(
		'post_type'  => 'tk_invite_codes',
		'meta_query' => array(
			array(
				'key'     => 'tk_all_in_one_invite_code',
				'value'   => $code,
				'compare' => '=',
			)
		)
	);
	$query = new WP_Query( $args );

	// If have posts means we have a valid code.
	if ( $query->have_posts() ) {

		while ( $query->have_posts() ) : $query->the_post();

			// Get the status of the code
			$status = get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code_status', true );

			// IF the status is set this code is not free to use and was already used before or got deactivated.
			if ( $status ) {
				$result['error'] = __( 'Invite code invalid', 'all-in-one-invite-code' );

				return $result;
			} else {

				// Alright, this is a valid code let us check if the code options are also valid for this user
				$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );


				// Check if the code is assigned to an email address and if the email address of the code is the same trying to register.
				if ( isset( $all_in_one_invite_codes_options['email'] ) ) {
					if ( ! empty( $all_in_one_invite_codes_options['email'] ) && $all_in_one_invite_codes_options['email'] != $user_email ) {
						$result['error'] = __( 'eMail address does not below to this invite code.', 'all-in-one-invite-code' );

						return $result;
					}
				}
				// If the email is empty we can use this code with any email address and assign the new mail to the code.
				if ( empty( $all_in_one_invite_codes_options['email'] ) ) {
					$all_in_one_invite_codes_options['email'] = $user_email;
					update_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
				}
				update_post_meta( get_the_ID(), 'tk_all_in_one_invite_code_status', 'used' );


			}
		endwhile;


	} else {
		// Error, something went wrong there where no code found
		$result['error'] = __( 'Invite code not exist', 'all-in-one-invite-code' );

		return $result;

	}
	return true;
}