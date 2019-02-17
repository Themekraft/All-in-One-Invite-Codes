<?php


function all_in_one_invite_codes_remove_slugdiv() {
	remove_meta_box( 'slugdiv', 'tk_invite_codes', 'normal' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_remove_slugdiv' );


/**
 * Create the metabox
 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
 */
function all_in_one_invite_codes_create_metabox() {
	add_meta_box(
		'all_in_one_invite_codes_options',
		'Invite Code: <small>' . all_in_one_invite_codes_md5() . '</small>',
		'all_in_one_invite_codes_render_metabox',
		'tk_invite_codes',
		'normal',
		'default'
	);
}

add_action( 'add_meta_boxes', 'all_in_one_invite_codes_create_metabox' );


/**
 * Create the metabox default values
 * This allows us to save multiple values in an array, reducing the size of our database.
 * Setting defaults helps avoid "array key doesn't exit" issues.
 * @todo
 */
function all_in_one_invite_codes_options_defaults() {
	$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );

	return array(
		'email' => '',
        'generate_codes' => isset( $all_in_one_invite_codes_general['generate_codes_amount'] ) ? $all_in_one_invite_codes_general['generate_codes_amount'] : '',
	);
}


/**
 * Render the metabox markup
 * This is the function called in `all_in_one_invite_codes_create_metabox()`
 */
function all_in_one_invite_codes_render_metabox() {
	global $post;

	$all_in_one_invite_code = all_in_one_invite_codes_md5( $post->ID );

	$all_in_one_invite_codes_options          = get_post_meta( $post->ID, 'all_in_one_invite_codes_options', true );
	$all_in_one_invite_codes_options_defaults = all_in_one_invite_codes_options_defaults(); // Get the default values

	$all_in_one_invite_codes_options = wp_parse_args( $all_in_one_invite_codes_options, $all_in_one_invite_codes_options_defaults );


	$email          = isset( $all_in_one_invite_codes_options['email'] ) ? $all_in_one_invite_codes_options['email'] : '';
	$generate_codes = isset( $all_in_one_invite_codes_options['generate_codes'] ) ? $all_in_one_invite_codes_options['generate_codes'] : '';

	?>

	<fieldset>
		<div>
			<input
				type="hidden"
				name="tk_all_in_one_invite_code"
				id="tk_all_in_one_invite_code"
				value="<?php echo esc_attr( $all_in_one_invite_code ); ?>"
			>

			<label for="all_in_one_invite_codes_options_email">
				<b><?php _e( 'Assign to specific email', 'all_in_one_invite_codes' ); ?></b>
				<p><?php _e( 'Restrict usage of this invite code for a specific email address. Leave blank if you want to make this invite code public accessible for any registration.', 'all_in_one_invite_codes' ); ?></p>
			</label>

			<p> eMail: <input
					type="email"
					name="all_in_one_invite_codes_options[email]"
					id="all_in_one_invite_codes_options_email"
					value="<?php echo esc_attr( $email ); ?>"
				>
			</p>

		</div>
		<div>
			<label for="all_in_one_invite_codes_options_email">
				<b><?php _e( 'Generate new Invite Codes after account activation', 'all_in_one_invite_codes' ); ?></b>
				<p><?php _e( 'Enter a number to generate new invite codes if this invite code got used.', 'all_in_one_invite_codes' ); ?></p>
			</label>
			<p>
				Number: <input
					type="number"
					name="all_in_one_invite_codes_options[generate_codes]"
					id="all_in_one_invite_codes_options_generate_codes"
					value="<?php echo esc_attr( $generate_codes ); ?>"
				>
			</p>
		</div>
	</fieldset>

	<?php

	wp_nonce_field( 'all_in_one_invite_codes_options_nonce', 'all_in_one_invite_codes_options_process' );

}

function all_in_one_invite_codes_save_options( $post_id, $post ) {

	if ( ! isset( $_POST['all_in_one_invite_codes_options_process'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['all_in_one_invite_codes_options_process'], 'all_in_one_invite_codes_options_nonce' ) ) {
		return $post->ID;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	if ( ! isset( $_POST['all_in_one_invite_codes_options'] ) ) {
		return $post->ID;
	}

	// Set up an empty array
	$sanitized = array();

	foreach ( $_POST['all_in_one_invite_codes_options'] as $key => $detail ) {
		$sanitized[ $key ] = wp_filter_post_kses( $detail );
	}

	update_post_meta( $post->ID, 'all_in_one_invite_codes_options', $sanitized );


}

add_action( 'save_post', 'all_in_one_invite_codes_save_options', 1, 2 );

function all_in_one_invite_codes_save_code( $post_id, $post ) {


	if ( ! isset( $_POST['all_in_one_invite_codes_options_process'] ) ) {
		return $post->ID;
	}

	if ( ! wp_verify_nonce( $_POST['all_in_one_invite_codes_options_process'], 'all_in_one_invite_codes_options_nonce' ) ) {
		return $post->ID;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	if ( ! isset( $_POST['tk_all_in_one_invite_code'] ) ) {
		return $post->ID;
	}

	$tk_all_in_one_invite_code = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );

	if ( $tk_all_in_one_invite_code ) {
		return $post->ID;
	}

	update_post_meta( $post->ID, 'tk_all_in_one_invite_code', wp_filter_post_kses( $_POST['tk_all_in_one_invite_code'] ) );

}

add_action( 'save_post', 'all_in_one_invite_codes_save_code', 1, 2 );

