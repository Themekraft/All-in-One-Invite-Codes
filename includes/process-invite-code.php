<?php

/**
 * Validate and process the code.
 *
 * @since  0.1
 * @return text
 */
function all_in_one_invite_codes_validate_code( $code, $user_email = '', $type = 'any' ) {

	// Get all invite codes with this code. Should only be one post.
	$args  = array(
		'post_type'  => 'tk_invite_codes',
		'meta_query' => array(
			array(
				'key'     => 'tk_all_in_one_invite_code',
				'value'   => $code,
				'compare' => '=',
			),
		),
	);
	$query = new WP_Query( $args );

	// If have posts means we have a valid code.
	if ( $query->have_posts() ) {

		while ( $query->have_posts() ) :
			$query->the_post();

			// IF the code is multi uyse  check if the use limit have being reached.
			$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );
			$is_multiple_use                 = isset( $all_in_one_invite_codes_options['multiple_use'] ) ? true : false;

			// Check the Expiration date
			if( isset( $all_in_one_invite_codes_options['expire_date'] ) ){

				$expire = strtotime($all_in_one_invite_codes_options['expire_date']);
				$today = strtotime("now");

				if($today >= $expire){
					$result['error'] = __( 'This invite code is expired.', 'all-in-one-invite-code' );
					return $result;
				}
			}

			if ( $is_multiple_use ) {
				$code_amount = isset( $all_in_one_invite_codes_options['generate_codes'] ) ? intval( $all_in_one_invite_codes_options['generate_codes'] ) : 0;
				if ( $code_amount <= 0 ) {

					$all_in_one_invite_codes_options['generate_codes'] = 0;
					update_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
					$result['error'] = __( 'Multi use invite code limit reached', 'all-in-one-invite-code' );
					return $result;

				}
			} else {
				// IF the status is set this code is not free to use and was already used before or got deactivated.
				if ( ! all_in_one_invite_codes_is_valide( get_the_ID() ) ) {
					$result['error'] = __( 'This invite code was already used before or got deactivated', 'all-in-one-invite-code' );

					return $result;
				} else {

					// Alright, this is a valid code let us check if the code options are also valid for this user
					$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );

					// Check if the code is assigned to an email address and if the email address of the code is the same trying to register.
					if ( isset( $all_in_one_invite_codes_options['email'] ) ) {

						if ( ! empty( $all_in_one_invite_codes_options['email'] ) && strtolower( $all_in_one_invite_codes_options['email'] ) != strtolower( $user_email ) ) {
							$result['error'] = __( 'eMail address does not below to this invite code.', 'all-in-one-invite-code' );

							return $result;
						}
					}
					// Check if the invite  code propose type match the one registered.
					if ( isset( $all_in_one_invite_codes_options['type'] ) ) {

						if ( ! empty( $all_in_one_invite_codes_options['type'] ) && $all_in_one_invite_codes_options['type'] != 'any' ) {
							// Check if the code propose is for an especific type
							if ( strtolower( $all_in_one_invite_codes_options['type'] ) != $type ) {
								$result['error'] = __( 'This invite code can´t be applied on this page, is for : ' . $all_in_one_invite_codes_options['type'] . ' page only.', 'all-in-one-invite-code' );

								return $result;
							}
						}
					}
				}
			}

		endwhile;

	} else {
		// Error, something went wrong there where no code found
		$result['error'] = __( 'Invite code not exist', 'all-in-one-invite-code' );

		return $result;

	}

	return true;
}
