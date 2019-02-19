<?php

/**
 * Create new invite codes after the user is registered.
 *
 * @since  0.1
 *
 */
function all_in_one_invite_codesuser_register( $user_id ) {

	// Only jump in if a invite code exist
	if ( ! empty( $_POST['tk_invite_code'] ) ) {

		// Save the invite code as user meta data to know the relation for later query's/ stats
		update_user_meta( $user_id, 'tk_all_in_one_invite_code', sanitize_text_field( $_POST['tk_invite_code'] ) );

		// Get the invite code
		$args  = array(
			'post_type'  => 'tk_invite_codes',
			'meta_query' => array(
				array(
					'key'     => 'tk_all_in_one_invite_code',
					'value'   => trim( $_POST['tk_invite_code'] ),
					'compare' => '=',
				)
			)
		);
		$query = new WP_Query( $args );


		// Get the invite code id
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$podt_id = get_the_ID();
			endwhile;
		}

		// get the invite code options
		$all_in_one_invite_codes_options = get_post_meta( $podt_id, 'all_in_one_invite_codes_options', true );

		// Check if and how many new invite code should get created.
		if ( isset( $all_in_one_invite_codes_options['generate_codes'] ) && $all_in_one_invite_codes_options['generate_codes'] > 0 ) {

			// Alright, loop and create all needed codes for this user.
			for ( $i = 1; $i <= $all_in_one_invite_codes_options['generate_codes']; $i ++ ) {
				$args        = array(
					'post_type'   => 'tk_invite_codes',
					'post_author' => $user_id,
					'post_parent' => $podt_id,
					'post_status' => 'publish'
				);
				$new_code_id = wp_insert_post( $args );

				// Create and save the new invite code as post meta
				$code = all_in_one_invite_codes_md5( $new_code_id );
				update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', wp_filter_post_kses( $code ) );

				// Assign the amount of new codes to the code witch should get created if one of this codes get used.
				$all_in_one_invite_codes_new_options['generate_codes'] = $all_in_one_invite_codes_options['generate_codes'];
				update_post_meta( $new_code_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_new_options );

			}

		}

	}
}

add_action( 'user_register', 'all_in_one_invite_codesuser_register' );