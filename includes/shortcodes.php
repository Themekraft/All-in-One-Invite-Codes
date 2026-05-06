<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'all_in_one_invite_codes_list_codes_by_user', 'all_in_one_invite_codes_list_codes' );
/**
 * Create the list of codes for the user with a option to sent invites to new users.
 *
 * @param $attr
 *
 * @return string
 */
function all_in_one_invite_codes_list_codes( $attr ) {

	AllinOneInviteCodes::setNeedAssets( true, 'all-in-one-invite-codes' );
	ob_start();

	// If the user is not logged in display a login form
	if ( ! is_user_logged_in() ) {
		echo '<p>' . esc_html__( 'Please login to manage your invite codes.', 'all-in-one-invite-codes' ) . '</p>';
		wp_login_form();

		return '';
	}

	// Add the js in the shortcode so we can use this more easy as Block in a later process. ?>
	<script>
		<?php echo 'var ajaxurl = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";'; ?>

	</script>

	<?php

	// Get the user invite codes
	$args = array(
		'author'         => get_current_user_id(),
		'posts_per_page' => - 1,
		'post_type'      => 'tk_invite_codes', // you can use also 'any'
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) {
		echo '<ul>';
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );
			$email                           = empty( $all_in_one_invite_codes_options['email'] ) ? '' : $all_in_one_invite_codes_options['email'];
			$code_amount                     = isset( $all_in_one_invite_codes_options['generate_codes'] ) ? $all_in_one_invite_codes_options['generate_codes'] : 1;
			$is_multiple_use                 = isset( $all_in_one_invite_codes_options['multiple_use'] ) ? '(' . $code_amount . ')' : '';

			// If invite code is Multiple Use then run the multiple invite codes  logic and valdiations
			if ( isset( $all_in_one_invite_codes_options['multiple_use'] ) ) {
				echo '<li>';
				echo '<div class="aioic-top">';
				echo '<div class="aioic-info">';
				echo '<div><p>Code: ';
				echo esc_html( get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true ) ) . ' ' . esc_html( $is_multiple_use );
				echo '</p></div>';
				echo '<div><p>Status: ';
				$status = all_in_one_invite_codes_get_status( get_the_ID() );
				echo esc_html( $status );
				echo '</p></div>';
				echo '</div>';
				echo '<div class="aioic-right">';
				if ( $code_amount > 0 ) {
					echo esc_html__( 'Give this Invite Code to friends, they can use it to register on the site', 'all-in-one-invite-codes' );
				} else {
					echo esc_html__( 'Invite Code limit reached', 'all-in-one-invite-codes' );
				}
				echo '</div>';
				echo '</div>';

				if ( $code_amount > 0 ) {
					echo '<div class="aioic-form" id="tk_all_in_one_invite_code_open_invite_form_id_' . esc_attr( get_the_ID() ) . '"></div>';
				}

				echo '</li>';

			} else {
				echo '<li>';
				echo '<div class="aioic-top">';
				echo '<div class="aioic-info">';
					echo '<div><p>Code: ';
					echo esc_html( get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true ) );
					echo '</p></div>';
					echo '<div><p>Status: ';
					$status = all_in_one_invite_codes_get_status( get_the_ID() );
					echo esc_html( $status );
					echo '</p></div>';
				echo '</div>';

				echo '<div class="aioic-right">';
				if ( empty( $email ) && $status == 'Active' ) {
					echo '<p><a class="button" data-code_id="' . esc_attr( get_the_ID() ) . '" id="tk_all_in_one_invite_code_open_invite_form" href="#">' . esc_html__( 'Invite a Friend Now', 'all-in-one-invite-codes' ) . '</a></p>';
				} else {
					echo esc_html__( 'Invite was sent to: ', 'all-in-one-invite-codes' ) . esc_html( $email );
				}
				echo '</div>';
				echo '</div>';

				if ( empty( $email ) && $status == 'Active' ) {
					echo '<div class="aioic-form" id="tk_all_in_one_invite_code_open_invite_form_id_' . esc_attr( get_the_ID() ) . '"></div>';
				}

				echo '</li>';
			}

		endwhile;
		echo '</ul>';

		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' )

		?>

		<div style="display: none" id="tk_all_in_one_invite_code_send_invite_form">
			<p><span>To: </span><input type="email" id="tk_all_in_one_invite_code_send_invite_to" value=""><span id="tk_all_in_one_invite_code_send_invite_to_error"></span></p>
			<p><span>Subject: </span><input type="text" id="tk_all_in_one_invite_code_send_invite_subject" value="<?php echo empty( $all_in_one_invite_codes_mail_templates['subject'] ) ? '' : esc_html( $all_in_one_invite_codes_mail_templates['subject'] ); ?>"></p>
			<p><span>Message Text:</span><textarea cols="70" rows="5" id="tk_all_in_one_invite_code_send_invite_message_text"><?php echo empty( $all_in_one_invite_codes_mail_templates['message_text'] ) ? '' : esc_html( $all_in_one_invite_codes_mail_templates['message_text'] ); ?></textarea></p>
			<a href="#" data-send_code_id="0" id="tk_all_in_one_invite_code_send_invite_submit" class="button">Send invitation</a>
		</div>

		<?php
	}

	wp_reset_postdata();

	$tmp = ob_get_clean();

	return $tmp;
}

add_shortcode( 'all_in_one_invite_codes_invited_by_user_filter', 'all_in_one_invite_codes_invited_by_user' );
/**
 * Create a list of invites by user
 *
 * @param $attr
 *
 * @return string
 */
function all_in_one_invite_codes_invited_by_user( $attr ) {

	ob_start();
	$filter_id = isset( $attr['userid'] ) ? $attr['userid'] : get_current_user_id();

	$user = get_user_by( 'ID', $filter_id );
	if ( $user->ID ) {
		$email     = $user->user_email;
		$args      = array(

			'posts_per_page' => - 1,
			'post_type'      => 'tk_invite_codes', // you can use also 'any'
			'orderby'        => 'post_author',
			'order'          => 'ASC',
		);
		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );
				$email_needle                    = empty( $all_in_one_invite_codes_options['email'] ) ? '' : $all_in_one_invite_codes_options['email'];

				if ( $email == $email_needle ) {
					$author_id  = (int) $the_query->post->post_author;
					$inviter    = get_user_by( 'ID', $author_id );
					$invited_by = $inviter->display_name;
					$post_date  = $the_query->post->post_date;

					$formatted_date = wp_date( DATE_COOKIE, strtotime( $post_date ) );
					/* translators: 1: invited user's display name, 2: inviter's display name */
					echo sprintf( esc_html__( 'The user : %1$s was invited by %2$s on  ', 'all-in-one-invite-codes' ), esc_html( $user->display_name ), esc_html( $invited_by ) ) . esc_html( $formatted_date );
					wp_reset_postdata();

					$tmp = ob_get_clean();

					return $tmp;

				}

			endwhile;
		}
		/* translators: %s: user's display name */
		echo sprintf( esc_html__( 'The user : %s was not invited by anyone', 'all-in-one-invite-codes' ), esc_html( $user->display_name ) );
		wp_reset_postdata();

		$tmp = ob_get_clean();

		return $tmp;
	}
	echo esc_html__( 'No user was found with the ID : ', 'all-in-one-invite-codes' ) . esc_html( $filter_id );
	wp_reset_postdata();

	$tmp = ob_get_clean();

	return $tmp;

}

add_shortcode( 'all_in_one_invite_codes_list_codes_not_assigend', 'all_in_one_invite_codes_list_codes_not_assigend' );
/**
 * Create a list of codes not assigned to any user
 *
 * @param $attr
 *
 * @return string
 */
function all_in_one_invite_codes_list_codes_not_assigend( $attr ) {
	AllinOneInviteCodes::setNeedAssets( true, 'all-in-one-invite-codes' );
	ob_start();
	// Add the js in the shortcode so we can use this more easy as Block in a later process.
	?>
	<script>
		<?php echo 'var ajaxurl = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";'; ?>
	</script>
	<?php

	$cache_key = 'all_in_one_invite_codes_published_ids';
	$generated_codes = wp_cache_get( $cache_key, 'all_in_one_invite_codes' );
	if ( false === $generated_codes ) {
		global $wpdb;
		$generated_codes = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
			'tk_invite_codes',
			'publish'
		) );
		wp_cache_set( $cache_key, $generated_codes, 'all_in_one_invite_codes', MINUTE_IN_SECONDS );
	}

	if ( ! empty( $generated_codes ) ) {
		$no_codes_unassigned_found = true;
		foreach ( $generated_codes as $unassigned_codes ) {
			$single_invite_code = get_post_meta( (int) $unassigned_codes, 'all_in_one_invite_codes_options', true );
			if ( empty( $single_invite_code['email'] ) ) {
				$no_codes_unassigned_found = false;
				printf(
					'<ul><li><div class="aioic-top"><div class="aioic-info"><div><strong>%1$s</strong> %2$s</div></div></div><div class="aioic-right"></div></li></ul>',
					esc_html__( 'Code:', 'all-in-one-invite-codes' ),
					esc_html( get_post_meta( (int) $unassigned_codes, 'tk_all_in_one_invite_code', true ) )
				);
			}
		}
		if ( $no_codes_unassigned_found ) {
			echo esc_html__( 'Sorry, no unassigned invite codes were found.', 'all-in-one-invite-codes' );
		}
	}
	$tmp = ob_get_clean();
	return $tmp;
}

add_shortcode( 'all_in_one_invite_codes_create', 'all_in_one_invite_codes_create' );
function all_in_one_invite_codes_create( $attr ) {

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

	?>
        <div>
            <input
                    type="hidden"
                    name="tk_all_in_one_invite_code"
                    id="tk_all_in_one_invite_code_modal"
                    value="<?php echo esc_attr( $all_in_one_invite_code ); ?>"
            >

            <label for="all_in_one_invite_codes_options_email">
                <b><?php esc_html_e( 'Assign to specific email', 'all-in-one-invite-codes' ); ?></b>
                <p><?php esc_html_e( 'Restrict usage of this invite code for a specific email address. Leave blank if you want to make this invite code public accessible for any registration.', 'all-in-one-invite-codes' ); ?></p>
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
                <b><?php esc_html_e( 'Generate new Invite Codes after account activation', 'all-in-one-invite-codes' ); ?></b>
                <p><?php esc_html_e( 'Enter a number to generate new invite codes if this invite code got used.', 'all-in-one-invite-codes' ); ?></p>
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
                <p><?php esc_html_e( 'Select an Action to limit the usage of the invite code to one particular action on your site and set the coupon code to used after thais action is done.', 'all-in-one-invite-codes' ); ?></p>
            </label>

			<?php
			$type_options = array(
				'any'      => __( 'Any', 'all-in-one-invite-codes' ),
				'register' => __( 'Register', 'all-in-one-invite-codes' ),
			);
			$type_options = apply_filters( 'all_in_one_invite_codes_options_type_options', $type_options );
			?>
            <p>
                Purpose: <select name="all_in_one_invite_codes_options[type]" id="all_in_one_invite_codes_options_type">
					<?php foreach ( $type_options as $slug => $option ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug, $type ); ?>><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
                </select>
            </p>
        </div>
	<?php


	// add the nonce check
	wp_nonce_field( 'all_in_one_invite_codes_options_nonce', 'all_in_one_invite_codes_options_process' );
}