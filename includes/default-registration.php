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
	$tk_invite_code = ( ! empty( $_GET['invite_code'] ) ) ? sanitize_text_field( $_GET['invite_code'] ) : '';

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

		// Validate teh code
		$result = all_in_one_invite_codes_validate_code( trim( $_POST['tk_invite_code'] ), $user_email );
		if ( isset( $result['error'] ) ) {
			$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), $result['error'] ) );
		}

	}

	return $errors;
}

add_filter( 'registration_errors', 'all_in_one_invite_code_registration_errors', 10, 3 );

