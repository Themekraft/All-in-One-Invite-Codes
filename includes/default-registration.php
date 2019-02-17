<?php

add_action( 'register_form', 'all_in_one_invite_codesregister_form' );
function all_in_one_invite_codesregister_form() {

	$tk_invite_code = ( ! empty( $_POST['tk_invite_code'] ) ) ? sanitize_text_field( $_POST['tk_invite_code'] ) : '';

	?>
    <p>
        <label for="tk_invite_code"><?php _e( 'Invitation Code', 'all-in-one-invite-code' ) ?><br/>
            <input type="text" name="tk_invite_code" id="tk_invite_code" class="input"
                   value="<?php echo esc_attr( $tk_invite_code ); ?>" size="25"/></label>
    </p>
	<?php
}

add_filter( 'registration_errors', 'all_in_one_invite_codesregistration_errors', 10, 3 );
function all_in_one_invite_codesregistration_errors( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST['tk_invite_code'] ) || ! empty( $_POST['tk_invite_code'] ) && trim( $_POST['tk_invite_code'] ) == '' ) {
		$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), __( 'You must include a Invite Code.', 'all-in-one-invite-code' ) ) );
	} else {

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


		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) : $query->the_post();
				$status = get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code_status', true );
				if ( $status ) {
					$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), __( 'Invite code invalid', 'all-in-one-invite-code' ) ) );
					return $errors;
				} else {

					$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );

					if( isset( $all_in_one_invite_codes_options['email'] ) ) {

					    if($all_in_one_invite_codes_options['email'] != $_POST['user_email'] ){
						    $errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), __( 'eMail address does not below to this invite code.', 'all-in-one-invite-code' ) ) );
						    return $errors;
                        }
					}



					update_post_meta( get_the_ID(), 'tk_all_in_one_invite_code_status', 'used' );

				}
			endwhile;


		} else {
			$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), __( 'Invite code not exist', 'all-in-one-invite-code' ) ) );
		}


	}

	return $errors;
}

//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'all_in_one_invite_codesuser_register' );
function all_in_one_invite_codesuser_register( $user_id ) {
	if ( ! empty( $_POST['tk_invite_code'] ) ) {
		update_user_meta( $user_id, 'tk_invite_code', sanitize_text_field( $_POST['tk_invite_code'] ) );
	}
}

