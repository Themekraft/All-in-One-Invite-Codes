<?php

/**
 * Add the invite only form element to the WordPress default registration
 *
 * @since  0.1
 *
 * return html
 */
function all_in_one_invite_code_register_form() {

	// Check if default registration integration is enabled
	if ( ! all_in_one_invite_codes_is_default_registration() ) {
		return;
	}

	// Check if the invite code is coming from a link
	$tk_invite_code = ( ! empty( $_GET['invite_code'] ) ) ? sanitize_key( trim( $_GET['invite_code'] ) ) : '';

	?>
    <p>
        <label for="tk_invite_code"><?php _e( 'Invitation Code', 'all-in-one-invite-code' ) ?><br/>
            <input type="text" name="tk_invite_code" id="tk_invite_code" class="input"
                   value="<?php echo esc_attr( $tk_invite_code ); ?>" size="25"/></label>
    </p>
	<?php
}

add_action( 'register_form', 'all_in_one_invite_code_register_form' );

/**
 * Validate the registration form element
 *
 * @since  0.1
 *
 * return object
 */
function all_in_one_invite_code_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	// Check if default registration integration is enabled
	if ( ! all_in_one_invite_codes_is_default_registration() ) {
		return $errors;
	}

	// Check if the field has a code
	if ( empty( $_POST['tk_invite_code'] ) || ! empty( $_POST['tk_invite_code'] ) && trim( $_POST['tk_invite_code'] ) == '' ) {
		$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), __( 'You must include a Invite Code.', 'all-in-one-invite-code' ) ) );
	} else {

		$tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );

		// Validate teh code
		$result = all_in_one_invite_codes_validate_code( $tk_invite_code, $user_email );
		if ( isset( $result['error'] ) ) {
			$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), $result['error'] ) );
		}

	}

	return $errors;
}

add_filter( 'registration_errors', 'all_in_one_invite_code_registration_errors', 10, 3 );

add_action( 'user_register', 'all_in_one_invite_code_registration_save', 10, 1 );
function all_in_one_invite_code_registration_save( $user_id ) {

	$tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );

	$post_id = all_in_one_invite_codes_get_code_id_by_code( $tk_invite_code );

	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options' );

	$user_data = get_userdata( $user_id );

	$all_in_one_invite_codes_options['email'] = $user_data->user_email;

	update_post_meta( $post_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
	update_post_meta( $post_id, 'tk_all_in_one_invite_code_status', 'Used' );

}
