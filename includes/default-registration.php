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

		$type = isset($_GET['action']) ? strtolower($_GET['action']) : '';
		if(empty($type)){
			$type = isset($_POST['wp-submit']) ? strtolower($_POST['wp-submit']) : '';
		}
		if(empty($type)){
			$type = 'any';
		}
		// Validate the code
		$result = all_in_one_invite_codes_validate_code( $tk_invite_code, $user_email,$type );
		if ( isset( $result['error'] ) ) {
			$errors->add( 'tk_invite_code_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'all-in-one-invite-code' ), $result['error'] ) );
		}

	}

	return $errors;
}

add_filter( 'registration_errors', 'all_in_one_invite_code_registration_errors', 10, 3 );


function all_in_one_invite_code_registration_save( $user_id ) {

	if ( empty( $_POST['tk_invite_code'] ) ) {
	    return;
	}

	$tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );

	// Save the invite code as user meta data to know the relation for later query's/ stats
	update_user_meta( $user_id, 'tk_all_in_one_invite_code', $tk_invite_code );

	// Get the invite code
	$args  = array(
		'post_type'  => 'tk_invite_codes',
		'meta_query' => array(
			array(
				'key'     => 'tk_all_in_one_invite_code',
				'value'   => $tk_invite_code,
				'compare' => '=',
			)
		)
	);
	$query = new WP_Query( $args );


	// Get the invite code id
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post();
			$post_parent_post_id = get_the_ID();
		endwhile;
	}

	// get the invite code options
	$all_in_one_invite_codes_options = get_post_meta( $post_parent_post_id, 'all_in_one_invite_codes_options', true );
	$is_multiple_use				 = isset( $all_in_one_invite_codes_options['multiple_use'] ) ? true : false;
	if($is_multiple_use){
		$code_amount                 = isset( $all_in_one_invite_codes_options['generate_codes'] ) ? intval($all_in_one_invite_codes_options['generate_codes']) : 0;
		if($code_amount > 0){
			$code_amount                                       = $code_amount -1;
			$all_in_one_invite_codes_options['generate_codes'] = $code_amount;
		    update_post_meta( $post_parent_post_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );			

		}
		else{
			update_post_meta( $post_parent_post_id, 'tk_all_in_one_invite_code_status', 'Used' );	
		}


	}
	else{

		// Check if and how many new invite code should get created.
	if ( isset( $all_in_one_invite_codes_options['generate_codes'] ) && $all_in_one_invite_codes_options['generate_codes'] > 0 ) {

		// Alright, loop and create all needed codes for this user.
		for ( $i = 1; $i <= $all_in_one_invite_codes_options['generate_codes']; $i ++ ) {



		    $args        = array(
				'post_type'   => 'tk_invite_codes',
				'post_author' => $user_id,
				'post_parent' => $post_parent_post_id,
				'post_status' => 'publish',
                'post_title'  => $tk_invite_code,
			);
			$new_code_id = wp_insert_post( $args );

			// Create and save the new invite code as post meta
			$code = all_in_one_invite_codes_md5( $new_code_id );
			$code = wp_filter_post_kses( $code );

			$args        = array(
                'ID'          => $new_code_id,
				'post_title'  => $code,
			);
			wp_update_post($args);

			update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', $code );

			// Assign the amount of new codes to the code witch should get created if one of this codes get used.
			$all_in_one_invite_codes_new_options['generate_codes'] = $all_in_one_invite_codes_options['generate_codes'];
			update_post_meta( $new_code_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_new_options );

			update_post_meta( $new_code_id, 'tk_all_in_one_invite_code_status', 'Active' );

		}

	}
	$user_data = get_userdata( $user_id );

	$all_in_one_invite_codes_options['email'] = $user_data->user_email;

	update_post_meta( $post_parent_post_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
	update_post_meta( $post_parent_post_id, 'tk_all_in_one_invite_code_status', 'Used' );
	}

	

}
add_action( 'user_register', 'all_in_one_invite_code_registration_save', 10, 1 );
