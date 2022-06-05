<?php



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
				echo '<div>Code: ';
				echo esc_html( get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true ) ) . ' ' . esc_html( $is_multiple_use );
				echo '</div>';
				echo '<div>Status: ';
				$status = all_in_one_invite_codes_get_status( get_the_ID() );
				echo esc_html( $status );
				echo '</div>';
				echo '</div>';
				echo '<div class="aioic-right">';
				if ( $code_amount > 0 ) {
					echo esc_html__( 'Give this Invite Code to friends, they can use it to register on the site', 'all_in_one_invite_codes' );
				} else {
					echo esc_html__( 'Invite Code limit reached', 'all_in_one_invite_codes' );
				}
				echo '</div>';
				echo '</div>';

				if ( $code_amount > 0 ) {
					echo '<div class="aioic-form" id="tk_all_in_one_invite_code_open_invite_form_id_' . get_the_ID() . '"></div>';
				}

				echo '</li>';

			} else {
				echo '<li>';
				echo '<div class="aioic-top">';
				echo '<div class="aioic-info">';
					echo '<div>Code: ';
					echo esc_html( get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true ) );
					echo '</div>';
					echo '<div>Status: ';
					$status = all_in_one_invite_codes_get_status( get_the_ID() );
					echo esc_html( $status );
					echo '</div>';
				echo '</div>';

				echo '<div class="aioic-right">';
				if ( empty( $email ) && $status == 'Active' ) {
					echo '<a class="button" data-code_id="' . get_the_ID() . '" id="tk_all_in_one_invite_code_open_invite_form" href="#">Invite a Friend Now</a>';
				} else {
					echo esc_html__( 'Invite was sent to: ', 'all_in_one_invite_codes' ) . esc_html( $email );
				}
				echo '</div>';
				echo '</div>';

				if ( empty( $email ) && $status == 'Active' ) {
					echo '<div class="aioic-form" id="tk_all_in_one_invite_code_open_invite_form_id_' . get_the_ID() . '"></div>';
				}

				echo '</li>';
			}

		endwhile;
		echo '</ul>';

		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' )

		?>

		<div style="display: none" id="tk_all_in_one_invite_code_send_invite_form">
			<p><span>To:</span><input type="email" id="tk_all_in_one_invite_code_send_invite_to" value=""><span id="tk_all_in_one_invite_code_send_invite_to_error"></span></p>
			<p><span>Subject:</span><input type="text" id="tk_all_in_one_invite_code_send_invite_subject" value="<?php echo empty( $all_in_one_invite_codes_mail_templates['subject'] ) ? '' : esc_html( $all_in_one_invite_codes_mail_templates['subject'] ); ?>"></p>
			<p><span>Message Text:</span><textarea cols="70" rows="5" id="tk_all_in_one_invite_code_send_invite_message_text"><?php echo empty( $all_in_one_invite_codes_mail_templates['message_text'] ) ? '' : esc_html( $all_in_one_invite_codes_mail_templates['message_text'] ); ?></textarea></p>
			<a href="#" data-send_code_id="0" id="tk_all_in_one_invite_code_send_invite_submit" class="button">Send</a>
		</div>

		<?php
	}

	wp_reset_postdata();

	$tmp = ob_get_clean();

	return $tmp;
}
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

					$formattedDate = date( DATE_COOKIE, strtotime( $post_date ) );
					echo sprintf( esc_html__( 'The user : %1$s was invited by %2$s on  ', 'all_in_one_invite_codes' ), esc_html( $user->display_name ), esc_html( $invited_by ) ) . esc_html( $formattedDate );
					wp_reset_postdata();

					$tmp = ob_get_clean();

					return $tmp;

				}

			endwhile;
		}
		echo sprintf( esc_html__( 'The user : %s was not invited by anyone', 'all_in_one_invite_codes' ), esc_html( $user->display_name ) );
		wp_reset_postdata();

		$tmp = ob_get_clean();

		return $tmp;
	}
	echo esc_html__( 'No user was found with the ID : ', 'all_in_one_invite_codes' ) . esc_html( $filter_id );
	wp_reset_postdata();

	$tmp = ob_get_clean();

	return $tmp;

}

add_shortcode( 'all_in_one_invite_codes_list_codes_by_user', 'all_in_one_invite_codes_list_codes' );
add_shortcode( 'all_in_one_invite_codes_invited_by_user_filter', 'all_in_one_invite_codes_invited_by_user' );


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
			<label for="all_in_one_invite_codes_options_email">
				<b><?php esc_html_e( 'Generate new Invite Codes after account activation', 'all_in_one_invite_codes' ); ?></b>
				<p><?php esc_html_e( 'Enter a number to generate new invite codes if this invite code got used.', 'all_in_one_invite_codes' ); ?></p>
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
	<?php

	// add the nonce check
	wp_nonce_field( 'all_in_one_invite_codes_options_nonce', 'all_in_one_invite_codes_options_process' );
}

add_shortcode( 'all_in_one_invite_codes_create', 'all_in_one_invite_codes_create' );

/**
 * Create a list of codes not assigned to any user
 *
 * @param $attr
 *
 * @return string
 */

function all_in_one_invite_codes_list_codes_not_assigend( $attr ) {
	global $wpdb;
	AllinOneInviteCodes::setNeedAssets( true, 'all-in-one-invite-codes' );
	ob_start();
	// Add the js in the shortcode so we can use this more easy as Block in a later process.
	?>
	<script>
		<?php echo 'var ajaxurl = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";'; ?>
	</script>
	<?php
	$generated_codes = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = %s", 'tk_invite_codes', 'publish' ) );
	if ( ! empty( $generated_codes ) ) {
		$no_codes_unassigned_found = true;
		foreach ( $generated_codes as $unassigned_codes ) {
			$single_invite_code = get_post_meta( (int) $unassigned_codes, 'all_in_one_invite_codes_options', true );
			if ( empty( $single_invite_code['email'] ) ) {
				$no_codes_unassigned_found = false;
				echo '<ul>
						<li>
							<div class="aioic-top">
									<div class="aioic-info">
										<div><strong>Code:</strong> ' . esc_html( get_post_meta( (int) $unassigned_codes, 'tk_all_in_one_invite_code', true ) );
				echo '					</div>
									</div>
							</div>
							<div class="aioic-right"></div>
						</li>
					</ul>';
			}
		}
		if ( $no_codes_unassigned_found ) {
			echo 'Sorry, no unassigned invite codes were found.';
		}
	}
	$wpdb->flush();
	$tmp = ob_get_clean();
	return $tmp;
}

add_shortcode( 'all_in_one_invite_codes_list_codes_not_assigend', 'all_in_one_invite_codes_list_codes_not_assigend' );
