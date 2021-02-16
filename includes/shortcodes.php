<?php


function all_in_one_invite_codes_list_codes_user_tree( $attr ) {

	AllinOneInviteCodes::setNeedAssets(true, 'all-in-one-invite-codes');
	ob_start();



	// Add the js in the shortcode so we can use this more easy as Block in a later process. ?>
	<script>
		<?php echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";' ?>

	</script>

	<?php

	// Get the user invite codes
	$args = array(

			'posts_per_page' => - 1,
			'post_type'      => 'tk_invite_codes', //you can use also 'any'
			'orderby' => 'post_author',
			'order' => 'ASC'
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) {
		echo '<h2>Invite Code Tree</h2>';
		//echo '<br/>';
		echo '<ul>';
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );
			$email                           = empty( $all_in_one_invite_codes_options['email'] ) ? '' : $all_in_one_invite_codes_options['email'];
			$status = all_in_one_invite_codes_get_status( get_the_ID() );
			$author_id =  (int)$the_query->post->post_author;
			$author_login = get_user_meta($author_id,'nickname',true);





			if ( !empty( $email ) && $status == 'Active' ) {
				echo '<li>';
				echo '<div class="aioic-right">';
				echo '<b>'.$author_login.': </b>'; echo __( 'Sent an invite to: ', 'all_in_one_invite_codes' ) . $email;
				echo '</div>';
				echo '</li>';
			}




		endwhile;
		echo '</ul>';

		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' )


		?>



		<?php
	}

	wp_reset_postdata();

	$tmp = ob_get_clean();

	return $tmp;
}
/**
 * Create the list of codes for the user with a option to sent invites to new users.
 *
 * @param $attr
 *
 * @return string
 */
function all_in_one_invite_codes_list_codes( $attr ) {

    AllinOneInviteCodes::setNeedAssets(true, 'all-in-one-invite-codes');
	ob_start();

	// If the user is not logged in display a login form
	if ( ! is_user_logged_in() ) {
		echo '<p>' . __( 'Please login to manage your invite codes.', 'all-in-one-invite-codes' ) . '</p>';
		wp_login_form();

		return '';
	}

	// Add the js in the shortcode so we can use this more easy as Block in a later process. ?>
    <script>
		<?php echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";' ?>

    </script>

	<?php

	// Get the user invite codes
	$args = array(
		'author'         => get_current_user_id(),
		'posts_per_page' => - 1,
		'post_type'      => 'tk_invite_codes', //you can use also 'any'
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) {
		echo '<ul>';
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );
			$email                           = empty( $all_in_one_invite_codes_options['email'] ) ? '' : $all_in_one_invite_codes_options['email'];

			echo '<li>';
			echo '<div class="aioic-top">';
			echo '<div class="aioic-info">';
				echo '<div>Code: ';
				echo get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true );
				echo '</div>';
				echo '<div>Status: ';
				echo $status = all_in_one_invite_codes_get_status( get_the_ID() );
				echo '</div>';
			echo '</div>';

			echo '<div class="aioic-right">';
			if ( empty( $email ) && $status == 'Active' ) {
				echo '<a class="button" data-code_id="' . get_the_ID() . '" id="tk_all_in_one_invite_code_open_invite_form" href="#">Invite a Friend Now</a>';
			} else {
				echo __( 'Invite was sent to: ', 'all_in_one_invite_codes' ) . $email;
			}
			echo '</div>';
			echo '</div>';

			if ( empty( $email ) && $status == 'Active' ) {
				echo '<div class="aioic-form" id="tk_all_in_one_invite_code_open_invite_form_id_' . get_the_ID() . '"></div>';
			}

			echo '</li>';
		endwhile;
		echo '</ul>';

		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' )


		?>

        <div style="display: none" id="tk_all_in_one_invite_code_send_invite_form">
            <p><span>To:</span><input type="email" id="tk_all_in_one_invite_code_send_invite_to" value=""><span id="tk_all_in_one_invite_code_send_invite_to_error"></span></p>
            <p><span>Subject:</span><input type="text" id="tk_all_in_one_invite_code_send_invite_subject" value="<?php echo empty( $all_in_one_invite_codes_mail_templates['subject'] ) ? '' : $all_in_one_invite_codes_mail_templates['subject']; ?>"></p>
            <p><span>Message Text:</span><textarea cols="70" rows="5" id="tk_all_in_one_invite_code_send_invite_message_text"><?php echo empty( $all_in_one_invite_codes_mail_templates['message_text'] ) ? '' : $all_in_one_invite_codes_mail_templates['message_text']; ?></textarea></p>
            <a href="#" data-send_code_id="0" id="tk_all_in_one_invite_code_send_invite_submit" class="button">Send</a>
        </div>

		<?php
	}

	wp_reset_postdata();

	$tmp = ob_get_clean();

	return $tmp;
}

add_shortcode( 'all_in_one_invite_codes_list_codes_by_user', 'all_in_one_invite_codes_list_codes' );
add_shortcode( 'all_in_one_invite_codes_list_codes_user_tree', 'all_in_one_invite_codes_list_codes_user_tree' );

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
        <div>
            <label for="all_in_one_invite_codes_options_type">
                <b><?php _e( 'Purpose?', 'all-in-one-invite-codes' ); ?></b>
                <p><?php _e( 'Select an Action to limit the usage of the invite code to one particular action on your site and set the coupon code to used after thais action is done.', 'all_in_one_invite_codes' ); ?></p>
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

					<?php foreach ( $type_options as $slug => $option ) {
						if ( $slug == $type ) {
							echo '<option value="' . $slug . '"  selected>' . $option . '</option >';
						} else {
							echo '<option value="' . $slug . '" >' . $option . '</option >';
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
