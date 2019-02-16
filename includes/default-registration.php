<?php

add_action( 'register_form', 'myplugin_register_form' );
function myplugin_register_form() {

	$first_name = ( ! empty( $_POST['first_name'] ) ) ? sanitize_text_field( $_POST['first_name'] ) : '';

	?>
	<p>
		<label for="first_name"><?php _e( 'Invitation Code', 'mydomain' ) ?><br />
			<input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(  $first_name  ); ?>" size="25" /></label>
	</p>
	<?php
}

//2. Add validation. In this case, we make sure first_name is required.
add_filter( 'registration_errors', 'myplugin_registration_errors', 10, 3 );
function myplugin_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
		$errors->add( 'first_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a first name.', 'mydomain' ) ) );

	}

	return $errors;
}

//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'myplugin_user_register' );
function myplugin_user_register( $user_id ) {
	if ( ! empty( $_POST['first_name'] ) ) {
		update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
	}
}

