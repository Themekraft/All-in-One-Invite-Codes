<?php

/**
 * Add the Bulk Process Page to the All in One Invite Codes Menu
 */
function all_in_one_invite_codes_bulk_menu() {
	add_submenu_page( 'edit.php?post_type=tk_invite_codes', __( 'All in One Invite Codes Bulk', 'all_in_one_invite_codes' ), __( 'Bulk Process', 'all_in_one_invite_codes' ), 'manage_options', 'all_in_one_invite_codes_bulk', 'all_in_one_invite_codes_bulk_page' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_bulk_menu' );

add_action( 'admin_enqueue_scripts', 'all_in_one_invite_codes_bulk_admin_js' );
function all_in_one_invite_codes_bulk_admin_js() {
	wp_register_script( 'invite_codes_bulk_validate_js', TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL . 'assets/js/jquery.validate.min.js' );
	wp_enqueue_script( 'invite_codes_bulk_validate_js' );
	wp_register_script( 'invite_codes_bulk_overlay_js', TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL . 'assets/loadingoverlay/loadingoverlay.js' );
	wp_enqueue_script( 'invite_codes_bulk_overlay_js' );
}

function all_in_one_invite_codes_bulk_page() {

	?>
		<style>
			.bf-alert.success {
				color: #3c763d;
				background-color: #dff0d8;
				border-color: #d6e9c6;}
			.bf-alert {
				display: block;
				padding: 15px;
				margin-bottom: 20px;
				border: 1px solid rgba(0,0,0,0.1);
				background-color: #fff;
			}
			.bf-alert.error {
				background-color: #f2dede;
				color: #a94442;
				border-color: #ebccd1;
			}
		</style>
		<div class="metabox-holder">
		  <div id="aioic_form_hero" class="postbox all_in_one_invite_codes-metabox ">
			  <div class="inside">
				  <div class="" id="form_message_aioic"></div>
				  <div class="form_wrapper clearfix">
					  <h2 class="screen-heading general-settings-screen">
						  <?php esc_html_e( 'Create Bulk Invites', 'all_in_one_invite_codes' ); ?>
					  </h2>

					  <p class="info invite-info">
						  <?php esc_html_e( 'Create multiple invites codes at once.', 'all_in_one_invite_codes-buddypress' ); ?>
					  </p>
					  <form action="" method="post"  class="" id="bulk-invite-aioic">
						  <input type="hidden" name="action" value="aioic_send_multiple_invites">
						  <?php wp_nonce_field( 'buddyforms_form_nonce', '_wpnonce', true, true ); ?>

						  <div>
							  <label for="all_in_one_invite_codes_options_email">
								  <b><?php esc_html_e( 'Generate new Invites Codes', 'all_in_one_invite_codes' ); ?></b>
								  <p><?php esc_html_e( 'Enter the amount of invites codes to generate.', 'all_in_one_invite_codes' ); ?></p>
							  </label>
							  <p>
								  Number: <input
										  type="number"
										  name="generate_codes"
										  id="all_in_one_invite_codes_options_generate_codes"

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
										  name="purpose"
										  id="all_in_one_invite_codes_options_type"
								  >

									  <?php
										foreach ( $type_options as $slug => $option ) {
											echo '<option value="' . esc_attr( $slug ) . '" >' . esc_html( $option ) . '</option >';

										}
										?>

								  </select>
							  </p>
						  </div>
						  <p  class="submit" align="right">
							  <input type="submit" name="aioic-invite-submit" id="submit" value="Generate Invites" class=" button button-primary aioic_submit">
						  </p>

					  </form>
				  </div>

			  </div>



	</div>

		<?php

}


