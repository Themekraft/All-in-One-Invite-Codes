<?php

function all_in_one_invite_codes_validate_code( $code, $user_email = '' ) {

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


	if ( $query->have_posts() ) {

		while ( $query->have_posts() ) : $query->the_post();
			$status = get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code_status', true );
			if ( $status ) {
				$result['error'] = __( 'Invite code invalid', 'all-in-one-invite-code' );

				return $result;
			} else {

				$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );

				if ( isset( $all_in_one_invite_codes_options['email'] ) ) {

					if ( ! empty( $all_in_one_invite_codes_options['email'] ) && $all_in_one_invite_codes_options['email'] != $user_email ) {
						$result['error'] = __( 'eMail address does not below to this invite code.', 'all-in-one-invite-code' );

						return $result;
					}

				}
				if ( empty( $all_in_one_invite_codes_options['email'] ) ) {
					$all_in_one_invite_codes_options['email'] = $user_email;
					update_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
				}
				update_post_meta( get_the_ID(), 'tk_all_in_one_invite_code_status', 'used' );


			}
		endwhile;


	} else {
		$result['error'] = __( 'Invite code not exist', 'all-in-one-invite-code' );

		return $result;

	}

}