<?php

//
// Add the Settings Page to the All in One Invite Codes Menu
//
function all_in_one_invite_codes_settings_menu() {
	add_submenu_page( 'edit.php?post_type=tk_invite_codes', __( 'All in One Invite Codes Settings', 'all_in_one_invite_codes' ), __( 'Settings', 'all_in_one_invite_codes' ), 'manage_options', 'all_in_one_invite_codes_settings', 'all_in_one_invite_codes_settings_page' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_settings_menu' );

//
// Settings Page Content
//
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

//
// Settings Tabs Navigation
//
/**
 * @param string $current
 */
function all_in_one_invite_codes_admin_tabs( $current = 'general' ) {
	$tabs = array( 'general' => 'General Settings' );

	$tabs          = apply_filters( 'all_in_one_invite_codes_admin_tabs', $tabs );
	$tabs['mail'] = 'Mail Templates';


	echo '<h2 class="nav-tab-wrapper" style="padding-bottom: 0;">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab$class' href='edit.php?post_type=tk_invite_codes&page=all_in_one_invite_codes_settings&tab=$tab'>$name</a>";
	}
	echo '</h2>';
}

//
// Register Settings Options
//
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

function all_in_one_invite_codes_settings_page_tabs_content() {
	global $pagenow, $all_in_one_invite_codes; ?>
    <div id="poststuff">

		<?php

		// Display the Update Message
		if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>All in One Invite Codes...</p></div>';
		}

		if ( isset ( $_GET['tab'] ) ) {
			all_in_one_invite_codes_admin_tabs( $_GET['tab'] );
		} else {
			all_in_one_invite_codes_admin_tabs( 'general' );
		}

		if ( $pagenow == 'edit.php' && $_GET['page'] == 'all_in_one_invite_codes_settings' ) {

			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = 'general';
			}

			switch ( $tab ) {
				case 'general' :
					$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );
					print_r($all_in_one_invite_codes_general);
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
                                                    <span><?php _e( 'Tab 1', 'all_in_one_invite_codes' ); ?></span>
                                                </h3>
                                            </th>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row" valign="top">
												<?php _e( 'Tab 1', 'all_in_one_invite_codes' ); ?>
                                            </th>
                                            <td>
                                                <label for="tk_aio_ic_default_registration"><p>Registration Form</p>
                                                </label>
                                                <textarea cols="70" rows="5" id="tk_aio_ic_default_registration"
                                                          name="all_in_one_invite_codes_general[default_registration]"><?php echo empty( $all_in_one_invite_codes_general['default_registration'] ) ? '' : $all_in_one_invite_codes_general['default_registration']; ?></textarea>
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
				case 'mail' :

                    $all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' );
                    print_r($all_in_one_invite_codes_mail_templates);
                    ?>
                    <div class="metabox-holder">
                        <div class="postbox all_in_one_invite_codes-metabox">

                            <div class="inside">

                                <form method="post" action="options.php">

									<?php settings_fields( 'all_in_one_invite_codes_mail_templates' ); ?>


                                    <table class="form-table">
                                        <tbody>

                                        <!-- Registration Settings -->
                                        <tr>
                                            <th colspan="2">
                                                <h3>
                                                    <span><?php _e( 'Tab 2', 'all_in_one_invite_codes' ); ?></span>
                                                </h3>
                                            </th>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row" valign="top">
												<?php _e( 'Tab 1', 'all_in_one_invite_codes' ); ?>
                                            </th>
                                            <td>
                                                <label for="all_in_one_invite_codes_mail_templates"><p>Registration Form</p>
                                                </label>
                                                <textarea cols="70" rows="5" id="all_in_one_invite_codes_mail_templates"
                                                          name="all_in_one_invite_codes_mail_templates[first_invite]"><?php echo empty( $all_in_one_invite_codes_mail_templates['first_invite'] ) ? '' : $all_in_one_invite_codes_mail_templates['first_invite']; ?></textarea>
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
	echo 'hatte';
}

/**
 * Process a settings import from a json file
 */
function all_in_one_invite_codes_process_settings_import() {
	if ( empty( $_POST['all_in_one_invite_codes_action'] ) || 'import_settings' != $_POST['all_in_one_invite_codes_action'] ) {
		return false;
	}
	if ( ! wp_verify_nonce( $_POST['all_in_one_invite_codes_import_nonce'], 'all_in_one_invite_codes_import_nonce' ) ) {
		return false;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$name      = explode( '.', $_FILES['import_file']['name'] );
	$extension = end( $name );

	if ( $extension != 'json' ) {
		wp_die( __( 'Please upload a valid .json file' ) );
	}

	$import_file = $_FILES['import_file']['tmp_name'];
	if ( empty( $import_file ) ) {
		wp_die( __( 'Please upload a file to import' ) );
	}
	// Retrieve the settings from the file and convert the json object to an array.
	$settings = json_decode( file_get_contents( $import_file ), true );

	$form_id = all_in_one_invite_codes_create_form_from_json( $settings );

	wp_safe_redirect( admin_url( 'post.php?post=' . $form_id . '&action=edit' ) );
	exit;
}

add_action( 'admin_init', 'all_in_one_invite_codes_process_settings_import' );


function all_in_one_invite_codes_create_form_from_json( $json_array ) {

	$bf_forms_args = array(
		'post_title'  => $json_array['name'],
		'post_type'   => 'all_in_one_invite_codes',
		'post_status' => 'publish',
	);

	// Insert the new form
	$post_id  = wp_insert_post( $bf_forms_args, true );
	$the_post = get_post( $post_id );

	$json_array['slug'] = $the_post->post_name;

	update_post_meta( $post_id, '_all_in_one_invite_codes_options', $json_array );

	if ( $post_id ) {
		all_in_one_invite_codes_attached_page_rewrite_rules( true );
	}

	return $post_id;

}