<?php

/**
 * Add the Settings Page to the All in One Invite Codes Menu
 */
function all_in_one_invite_codes_tree_menu() {
	add_submenu_page( 'edit.php?post_type=tk_invite_codes', __( 'All in One Invite Codes Settings', 'all_in_one_invite_codes_tree' ), __( 'Tree View', 'all_in_one_invite_codes_tree' ), 'manage_options', 'all_in_one_invite_codes_tree', 'all_in_one_invite_codes_tree_page' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_tree_menu' );

/**
 * Settings Page Content
 */
function all_in_one_invite_codes_tree_page() { ?>

	<style>

		ul.children {
			padding-left: 30px;
		}


	</style>
	<div id="post" class="wrap">

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="postbox-container-1" class="postbox-container">

				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php all_in_one_invite_codes_tree_tabs_content(); ?>
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
function all_in_one_invite_codes_tree_admin_tabs( $current = 'general' ) {
	$tabs = array( 'general' => 'General Statistics' );

	$tabs                              = apply_filters( 'all_in_one_invite_codes_tree_admin_tabs', $tabs );
	$tabs['invite_codes_tree']         = 'Invite Codes Tree';
	$tabs['invite_codes_user_tree']    = 'User Tree';
	$tabs['invite_codes_user_tracker'] = 'User Tracker';

	echo '<h2 class="nav-tab-wrapper" style="padding-bottom: 0;">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab" . esc_attr( $class ) . "' href='edit.php?post_type=tk_invite_codes&page=all_in_one_invite_codes_tree&tab=" . esc_attr( $tab ) . "'>" . esc_html( $name ) . '</a>';
	}
	echo '</h2>';
}


function all_in_one_invite_codes_tree_tabs_content() {
	global $pagenow, $all_in_one_invite_codes;
	?>
	<div id="poststuff">

		<?php

		// Display the Update Message
		if ( isset( $_GET['updated'] ) && 'true' == sanitize_text_field( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>All in One Invite Codes...</p></div>';
		}

		if ( isset( $_GET['tab'] ) ) {
			all_in_one_invite_codes_tree_admin_tabs( sanitize_key( $_GET['tab'] ) );
		} else {
			all_in_one_invite_codes_tree_admin_tabs( 'general' );
		}

		if ( $pagenow == 'edit.php' && $_GET['page'] == 'all_in_one_invite_codes_tree' ) {

			if ( isset( $_GET['tab'] ) ) {
				$tab = sanitize_key( $_GET['tab'] );
			} else {
				$tab = 'general';
			}

			switch ( $tab ) {
				case 'general':
					$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );
					?>
					<div class="metabox-holder">
						<div class="postbox all_in_one_invite_codes-metabox">
							<div class="inside">
								Add some general statistics

								<?php
								$invite_codes_stats = wp_count_posts( 'tk_invite_codes' );

								echo '<ul>';
								foreach ( $invite_codes_stats as $type => $count ) {
									echo '<li>' . esc_html( $type ) . ': ' . esc_html( $count ) . '</li>';
								}
								echo '</ul>';
								?>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;
				case 'invite_codes_tree':
					?>
					<div class="metabox-holder">
						<div class="postbox all_in_one_invite_codes-metabox">
							<div class="inside">
								<?php
								add_filter( 'wp_list_pages', 'all_in_one_invite_codes_wp_list_pages_filter', 10, 3 );
								add_filter( 'post_type_link', 'all_in_one_invite_codes_list_pages_permalink_filter', 10, 2 );

								wp_list_pages(
									array(
										'post_type'   => 'tk_invite_codes',
										'title_li'    => 'Invite Codes Flow',
										'post_status' => 'publish',
									)
								);

								remove_filter( 'wp_list_pages', 'all_in_one_invite_codes_wp_list_pages_filter', 10, 3 );
								remove_filter( 'post_type_link', 'all_in_one_invite_codes_list_pages_permalink_filter', 10, 2 );
								?>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;

				case 'invite_codes_user_tree':
					?>
					<div class="metabox-holder">
						<div class="postbox all_in_one_invite_codes-metabox">
							<div class="inside">
								<?php
								add_filter( 'wp_list_pages', 'all_in_one_invite_codes_user_tree_wp_list_pages_filter', 10, 3 );
								add_filter( 'post_type_link', 'all_in_one_invite_codes_list_pages_permalink_filter', 10, 2 );

								wp_list_pages(
									array(
										'post_type'   => 'tk_invite_codes',
										'title_li'    => 'User Tree',
										'post_status' => 'publish',

									)
								);

								remove_filter( 'wp_list_pages', 'all_in_one_invite_codes_wp_list_pages_filter', 10, 3 );
								remove_filter( 'post_type_link', 'all_in_one_invite_codes_list_pages_permalink_filter', 10, 2 );
								?>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;
				case 'invite_codes_user_tracker':
					?>
					<div class="metabox-holder">
						<div class="postbox all_in_one_invite_codes-metabox">
							<div class="inside">
								<?php
								add_filter( 'wp_list_pages', 'all_in_one_invite_codes_user_tracker_wp_list_pages_filter', 10, 3 );
								add_filter( 'post_type_link', 'all_in_one_invite_codes_list_pages_permalink_filter', 10, 2 );

								wp_list_pages(
									array(
										'post_type'   => 'tk_invite_codes',
										'title_li'    => 'User Tracker',
										'post_status' => 'publish',

									)
								);

								remove_filter( 'wp_list_pages', 'all_in_one_invite_codes_wp_list_pages_filter', 10, 3 );
								remove_filter( 'post_type_link', 'all_in_one_invite_codes_list_pages_permalink_filter', 10, 2 );
								?>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;

				default:
					do_action( 'all_in_one_invite_codes_tree_page_tab', $tab );

					break;
			}
		}
		?>
	</div> <!-- #poststuff -->
	<?php
}


function all_in_one_invite_codes_list_pages_permalink_filter( $permalink, $page ) {
	return get_edit_post_link( $page->ID );
}
function all_in_one_invite_codes_user_tracker_wp_list_pages_filter( $html, $key, $values ) {

	echo '<script src="' . esc_attr( TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL ) . 'assets/js/datatables.min.js' . '"></script>';
	 echo '<link rel="stylesheet" href="' . esc_attr( TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL ) . 'assets/css/dataTables.min.css' . '"></link>';
	   $user_tree_data = '[';
	foreach ( $values as $key => $value ) {
		$old_title = $value->post_title;

		$values[ $key ] = $value;

		$invite_key     = get_post_meta( $value->ID, 'tk_all_in_one_invite_code', true );
		$invite_status  = get_post_meta( $value->ID, 'tk_all_in_one_invite_code_status', true );
		$invite_options = get_post_meta( $value->ID, 'all_in_one_invite_codes_options', true );

		if( isset( $invite_options['email'] ) ){
			$email = $invite_options['email'];
			if ( ! empty( $email ) ) {

				$invited         = get_user_by( 'email', $email );
				$inviter         = get_user_by( 'ID', $value->post_author );
				$invited_by      = $inviter->display_name;
				$invited_user    = $invited->display_name ? $invited->display_name : 'not registered yet.';
				$user_tree_data .= '["' . $invited_by . '","' . $email . ' (<b>' . $invited_user . '</b>)"],';
	
			}
		}
	}
	$user_tree_data  = rtrim( $user_tree_data, ',' );
	$user_tree_data .= ']';
	echo '<table id="tree_user_table" class="display">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Invited</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>';

	echo '</ul>';
	echo "<script> jQuery('#tree_user_table');";
	echo "    jQuery('#tree_user_table').dataTable( {data: " . esc_js( $user_tree_data ) . '}) ';

	echo '</script>';

	// return $html;
}

function all_in_one_invite_codes_user_tree_wp_list_pages_filter( $html, $key, $values ) {

	foreach ( $values as $key => $value ) {

		if( ! is_object( $value ) ){
			continue;
		}
		$old_title = $value->post_title;

		$values[ $key ] = $value;

		$invite_key     = get_post_meta( $value->ID, 'tk_all_in_one_invite_code', true );
		$invite_status  = get_post_meta( $value->ID, 'tk_all_in_one_invite_code_status', true );
		$invite_options = get_post_meta( $value->ID, 'all_in_one_invite_codes_options', true );

		$new_title = '';
		if ( isset( $invite_options['email'] ) && $invite_options['email'] != '--' ) {

			$user      = get_user_by( 'email', $invite_options['email'] );
			if( ! is_object( $user ) ){
				continue;
			}
			$avatar    = get_avatar_url( $user->ID );
			$new_title = $invite_key . ' </a><br> <img src="' . $avatar . '" /> <br>Status: ' . $invite_status . ' <br> User: <a href="' . get_edit_user_link( $user->ID ) . '">  ' . $user->display_name;

		} else {
			$new_title = '';
		}

		$html = str_replace( $old_title, $new_title, $html );
	}

	return $html;
}

function all_in_one_invite_codes_wp_list_pages_filter( $html, $key, $values ) {

	return $html;

	foreach ( $values as $key => $value ) {
		$old_title = $value->post_title;

		$values[ $key ] = $value;

		$invite_key     = get_post_meta( $value->ID, 'tk_all_in_one_invite_code', true );
		$invite_status  = get_post_meta( $value->ID, 'tk_all_in_one_invite_code_status', true );
		$invite_options = get_post_meta( $value->ID, 'all_in_one_invite_codes_options', true );

		$new_title = '';
		if ( $invite_options['email'] ) {
			$user      = get_user_by( 'email', $invite_options['email'] );
			$new_title = $invite_key . ' </a><br> Status: ' . $invite_status . ' <br> User: <a href="' . get_edit_user_link( $user->ID ) . '">  ' . $user->display_name;
		} else {
			$new_title = $invite_key . ' </a><br> Status: ' . $invite_status . ' <br>';
		}

		$html = str_replace( $old_title, $new_title, $html );
	}

	return $html;
}


function all_in_one_invite_codes_exclude_drafts_branches() {
	global $wpdb;
	$exclude = array();
	$results = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} where post_status = 'draft' AND post_type = 'tk_invite_codes' " );
	$exclude = array_merge( $exclude, $results );
	while ( $results ) :
		$results = $wpdb->get_col( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'tk_invite_codes' AND post_status = 'publish' AND post_parent > 0 AND post_parent IN (" . join( ',', $results ) . ') ' );
		$exclude = array_merge( $exclude, $results );
	endwhile;

	return join( ',', $exclude );
}
