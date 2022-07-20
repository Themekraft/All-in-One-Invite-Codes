<?php

/**
 * Create the metabox for the code options
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
 * Render the metabox and display the options
 *
 * @param $post
 */
function all_in_one_invite_codes_render_metabox( $post ) {
	$post_id = ( ! empty( $post ) && isset( $post->ID ) ) ? $post->ID : false;

	// Get or generate the invite code
	$all_in_one_invite_code = all_in_one_invite_codes_md5( $post_id );

	// Get the invite code options
	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );

	// Get the default values
	$all_in_one_invite_codes_options_defaults = all_in_one_invite_codes_options_defaults();

	// Merge the options so we have the default take care of the missing values.
	$all_in_one_invite_codes_options = wp_parse_args( $all_in_one_invite_codes_options, $all_in_one_invite_codes_options_defaults );

	$email          = isset( $all_in_one_invite_codes_options['email'] ) ? $all_in_one_invite_codes_options['email'] : '';
	$generate_codes = isset( $all_in_one_invite_codes_options['generate_codes'] ) ? $all_in_one_invite_codes_options['generate_codes'] : '';
	$type           = isset( $all_in_one_invite_codes_options['type'] ) ? $all_in_one_invite_codes_options['type'] : 'registration';
	$multiple_use   = isset( $all_in_one_invite_codes_options['multiple_use'] ) ? 'checked="true"' : '';

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
				<b><?php esc_html_e( 'Assign to specific email', 'all_in_one_invite_codes' ); ?></b>
				<p><?php esc_html_e( 'Restrict usage of this invite code for a specific email address. Leave blank if you want to make this invite code public accessible for any registration.', 'all_in_one_invite_codes' ); ?></p>
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
			<label for="all_in_one_invite_codes_options_multiple_use">
				<b><?php esc_html_e( 'Set this invite code as multiple use', 'all_in_one_invite_codes' ); ?></b>

			</label>
			<p>
				Multiple Use: <input
						type="checkbox"
						name="all_in_one_invite_codes_options[multiple_use]"
						id="all_in_one_invite_codes_options_multiple_use"
						<?php echo esc_attr( $multiple_use ); ?>
				>
			</p>
		</div>

		<div>
			<label for="all_in_one_invite_codes_options_email" id="label_single_use">
				<b><?php esc_html_e( 'Generate new Invite Codes after account activation', 'all_in_one_invite_codes' ); ?></b>
				<p><?php esc_html_e( 'Enter a number to generate new invite codes if this invite code got used.', 'all_in_one_invite_codes' ); ?></p>
			</label>
			<label for="all_in_one_invite_codes_options_email" id="label_multiple_use">

				<p><?php esc_html_e( 'Enter invite code number of uses', 'all_in_one_invite_codes' ); ?></p>
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
		<div>
			<label for="all_in_one_invite_codes_options_type">
				<b><?php esc_html_e( 'Purpose?', 'all-in-one-invite-codes' ); ?></b>
				<p><?php esc_html_e( 'Select an Action to limit the usage of the invite code to one particular action on your site and set the coupon code to used after thais action is done.', 'all_in_one_invite_codes' ); ?></p>
			</label>

			<?php

			$type_options             = array();
			$type_options['any']      = __( 'Any', 'all-in-one-invite-codes' );
			$type_options['register'] = __( 'Register', 'all-in-one-invite-codes' );

			$type_options = apply_filters( 'all_in_one_invite_codes_options_type_options', $type_options )

			?>
			<p>
				Purpose: <select
						name="all_in_one_invite_codes_options[type]"
						id="all_in_one_invite_codes_options_type"
						value="<?php echo esc_attr( $type ); ?>">

					<?php
					foreach ( $type_options as $slug => $option ) {
						if ( $slug == $type ) {
							echo '<option value="' . esc_attr( $slug ) . '"  selected>' . esc_html( $option ) . '</option >';
						} else {
							echo '<option value="' . esc_attr( $slug ) . '" >' . esc_html( $option ) . '</option >';
						}
					}
					?>

				</select>
			</p>
		</div>
	</fieldset>

	<?php
	// List related codes as child codes
	$args = array(
		'post_parent'    => $post_id,
		'posts_per_page' => - 1,
		'post_type'      => 'tk_invite_codes', // you can use also 'any'
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) :
			$the_query->the_post();

			echo '<a href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '">' . esc_html( get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true ) ) . '</a>';
			echo '<br>';
		endwhile;
	endif;

	wp_reset_postdata();

	// add the nonce check
	wp_nonce_field( 'all_in_one_invite_codes_options_nonce', 'all_in_one_invite_codes_options_process' );

}

/**
 * Save the options
 *
 * @param $post_id
 * @param $post
 *
 * @return int
 */
function all_in_one_invite_codes_save_options( $post_id, $post ) {
	global $wpdb;

	if ( ! isset( $_POST['all_in_one_invite_codes_options_process'] ) ) {
		return $post_id;
	}

	if ( ! wp_verify_nonce( $_POST['all_in_one_invite_codes_options_process'], 'all_in_one_invite_codes_options_nonce' ) ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['all_in_one_invite_codes_options'] ) ) {
		return $post_id;
	}

	// Set up an empty array
	$sanitized = array();

	// Sanitize with wp_filter_post_kses
	foreach ( wp_unslash( $_POST['all_in_one_invite_codes_options'] ) as $key => $detail ) {
		$sanitized[ $key ] = wp_kses_post( $detail );
	}

	// Do the update
	update_post_meta( $post_id, 'all_in_one_invite_codes_options', $sanitized );

	// Check if code is multiuse
	if ( isset( $sanitized['multiple_use'] ) && isset( $sanitized['email'] ) ) {
		// If the code is multiple then save the total
		$code_amount             = isset( $sanitized['generate_codes'] ) ? $sanitized['generate_codes'] : 1;
		$sanitized['code_total'] = $code_amount;
		update_post_meta( $post_id, 'all_in_one_invite_codes_options', $sanitized );

		$asign_user = get_user_by( 'email', $sanitized['email'] );
		if ( $asign_user ) {
			$arg    = array(
				'ID'          => $post_id,
				'post_author' => $asign_user->ID,
			);
			$result = $wpdb->update( $wpdb->posts, array( 'post_author' => $asign_user->ID ), array( 'ID' => $post_id ), array( '%d' ), array( '%d' ) );
			if ( ! $result ) {
				return false;
			}
		}
	}

	if ( isset( $_POST['all_in_one_invite_codes_options']['email'] ) && isset( $_POST['tk_all_in_one_invite_code'] ) ) {

		$email = sanitize_email( $_POST['all_in_one_invite_codes_options']['email'] );
		$type  = sanitize_text_field( $_POST['all_in_one_invite_codes_options']['type'] );
		switch ( $type ) {

			case 'any':
			case 'register':
				$message_text = 'message_text';
				break;
			default:
				$message_text = $type;
				break;

		}
		$tk_invite_code                         = sanitize_key( $_POST['tk_all_in_one_invite_code'] );
		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' );

		$subject   = isset( $all_in_one_invite_codes_mail_templates['subject'] ) ? $all_in_one_invite_codes_mail_templates['subject'] : __( "You've Been Invited!", 'all-in-one-invite-code' );
		$body      = isset( $all_in_one_invite_codes_mail_templates[ $message_text ] ) ? $all_in_one_invite_codes_mail_templates[ $message_text ] : __( 'You got an invite from the site [site_name]. Please use this link to register with your invite code [invite_link]', 'all-in-one-invite-code' );
		$site_name = get_bloginfo( 'name' );
		$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[site_name]', $site_name );
		$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_code]', $tk_invite_code );

		$body = all_in_one_invite_codes_replace_shortcode( $body, '[site_name]', $site_name );
		$body = all_in_one_invite_codes_replace_shortcode( $body, '[invite_code]', $tk_invite_code );

		// Invite Link
		$buddypress_active = false;
		if ( function_exists( 'bp_is_active' ) ) {
			$buddypress_active = true;
		}
		if ( $buddypress_active || ! all_in_one_invite_codes_is_default_registration() ) {
			$invite_link = '<a href="' . wp_registration_url() . '?invite_code=' . $tk_invite_code . '">Link</a>';
		} else {
			$invite_link = '<a href="' . wp_registration_url() . '&invite_code=' . $tk_invite_code . '">Link</a>';
		}

		$subject = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_link]', $invite_link );
		$body    = all_in_one_invite_codes_replace_shortcode( $body, '[invite_link]', $invite_link );

		// sent the mail
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'tk_invite_codes' ) {
			$email_param = array(
				'to'      => $email,
				'subject' => $subject,
				'body'    => $body,
				'headers' => $headers,
			);
			$email_param = apply_filters( 'all_in_one_invite_code_custom_email', $email_param );
			if ( ! empty( $email ) ) {
				$send = wp_mail( $email_param['to'], $email_param['subject'], $email_param['body'], $email_param['headers'] );
			}
		}
	}

	if ( ! get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true ) ) {
		update_post_meta( $post_id, 'tk_all_in_one_invite_code_status', 'Active' );
	}

	return $post_id;
}

add_action( 'save_post_tk_invite_codes', 'all_in_one_invite_codes_save_options', 1, 2 );

/**
 * Save the invite code
 *
 * @param $post_id
 * @param $post
 *
 * @return mixed
 */
function all_in_one_invite_codes_save_code( $post_id, $post ) {

	if ( ! isset( $_POST['all_in_one_invite_codes_options_process'] ) ) {
		return $post_id;
	}

	if ( ! wp_verify_nonce( $_POST['all_in_one_invite_codes_options_process'], 'all_in_one_invite_codes_options_nonce' ) ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['tk_all_in_one_invite_code'] ) ) {
		return $post_id;
	}

	$tk_all_in_one_invite_code = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );

	if ( $tk_all_in_one_invite_code ) {
		return $post_id;
	}

	$tk_invite_code = sanitize_key( trim( $_POST['tk_all_in_one_invite_code'] ) );

	update_post_meta( $post_id, 'tk_all_in_one_invite_code', $tk_invite_code );

	return $post_id;
}

add_action( 'save_post', 'all_in_one_invite_codes_save_code', 1, 2 );

/**
 * Remove slugdiv
 */
function all_in_one_invite_codes_remove_slugdiv() {
	remove_meta_box( 'slugdiv', 'tk_invite_codes', 'normal' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_remove_slugdiv' );


add_filter( 'wp_insert_post_data', 'all_in_one_invite_codes_change_title' );
function all_in_one_invite_codes_change_title( $data ) {
	if ( ! isset( $data['ID'] ) ) {
		return $data;
	}

	$post_id = $data['ID'];
	if ( ! isset( $_POST['all_in_one_invite_codes_options_process'] ) ) {
		return $data;
	}

	if ( ! wp_verify_nonce( $_POST['all_in_one_invite_codes_options_process'], 'all_in_one_invite_codes_options_nonce' ) ) {
		return $data;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $data;
	}

	if ( ! isset( $_POST['tk_all_in_one_invite_code'] ) ) {
		return $data;
	}

	$data['post_title'] = sanitize_key( trim( $_POST['tk_all_in_one_invite_code'] ) );

	return $data;
}
