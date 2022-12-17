<?php

/**
 * Add the Settings Page to the All in One Invite Codes Menu
 */
function all_in_one_invite_codes_settings_menu() {
	add_submenu_page( 'edit.php?post_type=tk_invite_codes', __( 'All in One Invite Codes Settings', 'all_in_one_invite_codes' ), __( 'Settings', 'all_in_one_invite_codes' ), 'manage_options', 'all_in_one_invite_codes_settings', 'all_in_one_invite_codes_settings_page' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_settings_menu' );

/**
 * Settings Page Content
 */
function all_in_one_invite_codes_settings_page() { ?>

	<div id="post" class="wrap">

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="postbox-container-1" class="postbox-container">
					<?php all_in_one_invite_codes_settings_page_sidebar(); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php all_in_one_invite_codes_settings_page_tabs_content(); ?>
				</div>
			</div>
		</div>

	</div> <!-- .wrap -->
	<?php
}

/**
 * Settings Tabs Navigation
 *
 * @param string $current
 */
function all_in_one_invite_codes_admin_tabs( $current = 'general' ) {
	$tabs = array( 'general' => 'General Settings' );

	$tabs         = apply_filters( 'all_in_one_invite_codes_admin_tabs', $tabs );
	$tabs['mail'] = 'Mail Templates';

	echo '<h2 class="nav-tab-wrapper" style="padding-bottom: 0;">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo wp_kses_post( "<a class='nav-tab$class' href='edit.php?post_type=tk_invite_codes&page=all_in_one_invite_codes_settings&tab=$tab'>$name</a>" );
	}
	echo '</h2>';
}

/**
 * Register Settings Options
 */
function all_in_one_invite_codes_register_option() {

	// General Settings
	register_setting( 'all_in_one_invite_codes_general', 'all_in_one_invite_codes_general', 'all_in_one_invite_codes_default_sanitize' );

	// Mail Templates
	register_setting( 'all_in_one_invite_codes_mail_templates', 'all_in_one_invite_codes_mail_templates', 'all_in_one_invite_codes_default_sanitize' );

}

add_action( 'admin_init', 'all_in_one_invite_codes_register_option' );

/**
 * @param $new
 *
 * @return mixed
 */
function all_in_one_invite_codes_default_sanitize( $new ) {
	return $new;
}

/**
 *
 * Tabs content with the options
 */
function all_in_one_invite_codes_settings_page_tabs_content() {
	global $pagenow, $all_in_one_invite_codes;
	?>
	<div id="poststuff">

		<?php

		// Display the Update Message
		if ( isset( $_GET['updated'] ) && 'true' == sanitize_text_field( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>All in One Invite Codes...</p></div>';
		}

		if ( isset( $_GET['tab'] ) ) {
			all_in_one_invite_codes_admin_tabs( sanitize_key( $_GET['tab'] ) );
		} else {
			all_in_one_invite_codes_admin_tabs( 'general' );
		}

		if ( $pagenow == 'edit.php' && sanitize_key( $_GET['page'] ) == 'all_in_one_invite_codes_settings' ) {

			if ( isset( $_GET['tab'] ) ) {
				$tab = sanitize_key( $_GET['tab'] );
			} else {
				$tab = 'general';
			}

			switch ( $tab ) {
				case 'general':
					$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );

					if( empty( $all_in_one_invite_codes_general ) ){
						$all_in_one_invite_codes_general = array();
						$all_in_one_invite_codes_general['default_registration']  		  = 'Enable';
						$all_in_one_invite_codes_general['generate_codes_amount'] 		  = '5';
						$all_in_one_invite_codes_general['character_length']              = '5';
						add_option( 'all_in_one_invite_codes_general', $all_in_one_invite_codes_general, '', 'yes' );
					}
					
					?>
					<div class="metabox-holder">
						<div class="postbox all_in_one_invite_codes-metabox">
							<div class="inside">
								<form method="post" action="options.php">

									<?php settings_fields( 'all_in_one_invite_codes_general' ); ?>


									<table class="form-table">
										<tbody>

										<!-- Registration Settings -->
										<tr>
											<th colspan="2">
												<h3>
													<span><?php esc_html_e( 'General Settings', 'all_in_one_invite_codes' ); ?></span>
												</h3>
											</th>
										</tr>

										<tr valign="top">
											<th scope="row" valign="top">
												<?php esc_html_e( 'Add Invite only Validation to the WordPress default registration form', 'all-in-one-invite-codes' ); ?>
											</th>
											<td>
												<?php
												$pages['enabled'] = 'Enable';
												$pages['disable'] = 'Disable';

												if ( isset( $pages ) && is_array( $pages ) ) {
													echo '<select name="all_in_one_invite_codes_general[default_registration]" id="all_in_one_invite_codes_general">';

													foreach ( $pages as $page_id => $page_name ) {
														echo '<option ' . esc_attr( selected( $all_in_one_invite_codes_general['default_registration'], $page_id ) ) . 'value="' . esc_attr( $page_id ) . '">' . esc_html( $page_name ) . '</option>';
													}
													echo '</select>';
												}
												?>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php esc_html_e( 'How manny now Invite Codes should get generated after the new user is activated?', 'all-in-one-invite-codes' ); ?>
											</th>
											<td>
												<input type="number"
													   name="all_in_one_invite_codes_general[generate_codes_amount]"
													   id="all_in_one_invite_codes_general_generate_codes_amount"
													   value="<?php echo isset( $all_in_one_invite_codes_general['generate_codes_amount'] ) ? esc_attr( $all_in_one_invite_codes_general['generate_codes_amount'] ) : '5'; ?>">
											</td>
										</tr>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php esc_html_e( 'Invites codes characters length', 'all-in-one-invite-codes' ); ?>
											</th>
											<td>
												<input type="number"
													   name="all_in_one_invite_codes_general[character_length]"
													   id="all_in_one_invite_codes_general_character_length"
													   min="5"
													   value="<?php echo isset( $all_in_one_invite_codes_general['character_length'] ) ? esc_attr( $all_in_one_invite_codes_general['character_length'] ) : '5'; ?>">
											</td>
										</tr>
										</tbody>
									</table>

									<?php submit_button(); ?>

								</form>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;
				case 'mail':
					$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' );

					if( empty( $all_in_one_invite_codes_mail_templates ) ){
						$all_in_one_invite_codes_mail_templates                 = array();
						$all_in_one_invite_codes_mail_templates['subject']      = 'Invite code';
						$all_in_one_invite_codes_mail_templates['message_text'] = 'You got an invite from the site [site_name]. Please use this link to register with your invite code [invite_link]';
						add_option( 'all_in_one_invite_codes_mail_templates', $all_in_one_invite_codes_mail_templates, '', 'yes' );
					}

					$message_text_default = __( 'You got an invite from the site [site_name]. Please use this link to register with your invite code [invite_link]' );
					?>
					<div class="metabox-holder">
						<div class="postbox all_in_one_invite_codes-metabox">

							<div class="inside">

								<form method="post" action="options.php">

									<?php settings_fields( 'all_in_one_invite_codes_mail_templates' ); ?>
									<style>
										li.aioic-shortcode-list {
											font-size: 14px;
											margin-left: 10px;
											list-style-type: square;
										}
									</style>


									<table class="form-table">
										<tbody>
										<tr>
											<th colspan="2">
												<h3>
													<span><?php esc_html_e( 'Invite eMail Settings', 'all_in_one_invite_codes' ); ?></span>
												</h3>
											</th>
										</tr>
										<tr>
											<td colspan="2">
												<b><?php echo esc_html__( 'You can use Shortcodes to dynamically add data to the text.', 'all-in-one-invite-codes' ); ?></b>
												<ul>
													<li class="ioic-shortcode-list">[site_name]</li>
													<li class="ioic-shortcode-list">[invite_code]</li>
													<li class="ioic-shortcode-list">[invite_link]</li>
												</ul>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row" valign="top">
												<?php esc_html_e( 'Subject', 'all-in-one-invite-codes' ); ?>
											</th>
											<td>
												<input type="text"
													   name="all_in_one_invite_codes_mail_templates[subject]"
													   id="all_in_one_invite_codes_mail_templates"
													   value="<?php echo isset( $all_in_one_invite_codes_mail_templates['subject'] ) ? esc_attr( $all_in_one_invite_codes_mail_templates['subject'] ) : 'Invite Code'; ?>">
											</td>
										</tr>

											<?php
											$email_templates_types                 = array();
											$email_templates_types['message_text'] = __( 'Message Text', 'all-in-one-invite-codes' );
											$email_templates_types                 = apply_filters( 'all_in_one_invite_codes_options_email_templates', $email_templates_types );

											foreach ( $email_templates_types as $key => $description ) {
												$template_message = empty( $all_in_one_invite_codes_mail_templates[ $key ] ) ? $message_text_default : $all_in_one_invite_codes_mail_templates[ $key ];
												echo '<tr valign="top">';
												echo '<th scope="row" valign="top">' . esc_html( $description ) . '</th>';
												echo '<td>';

												echo '<textarea cols="70" rows="5" id="all_in_one_invite_codes_mail_templates" name="all_in_one_invite_codes_mail_templates[' . esc_attr( $key ) . ']" >' . wp_kses_post( $template_message ) . '</textarea>';
												echo '</td>';
												echo '</tr>';

											}
											?>



										</tbody>
									</table>

									<?php submit_button(); ?>

								</form>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;

				default:
					do_action( 'all_in_one_invite_codes_settings_page_tab', $tab );

					break;
			}
		}
		?>
	</div> <!-- #poststuff -->
	<?php
}

function all_in_one_invite_codes_settings_page_sidebar() {
	echo '<p>Placeholder Text</p>';
}
