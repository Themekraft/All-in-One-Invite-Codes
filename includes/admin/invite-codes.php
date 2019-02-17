<?php

function all_in_one_invite_codes_register_post_type() {

	/**
	 * Post Type: Invite Codes.
	 */

	$labels = array(
		"name"          => __( "Invite Codes", "all_in_one_invite_codes" ),
		"singular_name" => __( "Invite Code", "all_in_one_invite_codes" ),
	);

	$args = array(
		"label"                 => __( "Invite Codes", "all_in_one_invite_codes" ),
		"labels"                => $labels,
		"description"           => "",
		"public"                => false,
		"publicly_queryable"    => false,
		"show_ui"               => true,
		"delete_with_user"      => false,
		"show_in_rest"          => true,
		"rest_base"             => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive"           => false,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => false,
		"exclude_from_search"   => true,
		"capability_type"       => "post",
		"map_meta_cap"          => true,
		"hierarchical"          => false,
		"rewrite"               => array( "slug" => "tk_invite_codes", "with_front" => false ),
		"query_var"             => true,
		"supports"              => false,
	);

	register_post_type( "tk_invite_codes", $args );
}

add_action( 'init', 'all_in_one_invite_codes_register_post_type' );



/**
 *
 * Add the actions to list table
 *
 * @param $actions
 * @param $post
 *
 * @return mixed
 */
function all_in_one_invite_codes_add_action_buttons( $actions, $post ) {

	if ( get_post_type() === 'tk_invite_codes' ) {

		$url = add_query_arg(
			array(
				'post_id'   => $post->ID,
				'my_action' => 'export_form',
			)
		);

		unset( $actions['inline hide-if-no-js'] );

		$base = home_url();

		$preview_page_id = get_option( 'buddyforms_preview_page', true );

		$actions['resent']       = '<a href="#" id="all_in_one_disable_invite_code">Disable</a>';
		$actions['disable']  = '<a href="#" id="all_in_one_resent_invite_code">Resent Invitation</a>';


	}

	return $actions;
}

add_filter( 'post_row_actions', 'all_in_one_invite_codes_add_action_buttons', 10, 2 );





function tk_invite_codes_columns( $columns, $post_id = false ) {
	unset( $columns['date'] );
	unset( $columns['title'] );

	$columns['code']  = __( 'Code', 'all-in-one-invite-codes' );
	$columns['status']  = __( 'Status', 'all-in-one-invite-codes' );
	$columns['email'] = __( 'eMail', 'all-in-one-invite-codes' );

	return $columns;
}

add_action( 'manage_tk_invite_codes_posts_columns', 'tk_invite_codes_columns', 10, 2 );
function custom_tk_invite_codes_columns( $columns, $post_id = false ) {

	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );


	switch ( $columns ) {
		case 'code' :
			echo get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );
			break;
		case 'status' :
			echo empty( get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true ) ) ? __( 'Active', 'all-in-one-invite-codes' ) : __( 'Used', 'all-in-one-invite-codes' );;
			break;
		case 'email' :
			echo isset( $all_in_one_invite_codes_options['email'] ) ? $all_in_one_invite_codes_options['email'] : '--';
			break;
	}

}

add_action( 'manage_tk_invite_codes_posts_custom_column', 'custom_tk_invite_codes_columns', 10, 2 );


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function all_in_one_invite_codes_hide_publishing_actions() {
	global $post;

	if ( get_post_type( $post ) == 'tk_invite_codes' ) { ?>
        <style type="text/css">
            .misc-pub-visibility,
            .misc-pub-curtime,
            .misc-pub-post-status {
                display: none;
            }

            h1 {
                display: none;
            }

            .metabox-prefs label {
                /* float: right; */
                /* margin-top: 57px; */
                /*width: 100%;*/
            }

            /* Sven Quick Fix ToDo: Konrad please check it;) */
            .wrap .wp-heading-inline, .page-title-action {
                display: none;
            }

            #postbox-container-1, #postbox-container-2 {
                margin-top: 50px;
            }

            #minor-publishing-actions {
                display:none;
            }

        </style>
        <script>
            jQuery(document).ready(function (jQuery) {

                jQuery('body').find('h1:first').remove();
                jQuery('body').find('#post-body-content').remove();
                jQuery('body').find('.wp-heading-inline').remove();
            });
        </script>
		<?php
	}

}

//add_action( 'admin_head-edit.php', 'all_in_one_invite_codes_hide_publishing_actions' );
add_action( 'admin_head-post.php', 'all_in_one_invite_codes_hide_publishing_actions' );
add_action( 'admin_head-post-new.php', 'all_in_one_invite_codes_hide_publishing_actions' );


//
// Add new Actions Buttons to the publish metabox
//
function all_in_one_invite_codes_add_button_to_submit_box() {
	global $post;

	if ( get_post_type( $post ) != 'tk_invite_codes' ) {
		return;
	}

	?>

    <div id="all-in-one-invite-codes-actions" class="misc-pub-section">
        <p><a href="#" id="all_in_one_disable_invite_code" class="button button-large bf_button_action">Disable This Invite Code</a></p>
        <p><a href="#" id="all_in_one_resent_invite_code" class="button button-large bf_button_action">Resent Invitation Mail</a></p>
        <div class="clear"></div>
    </div>

	<?php

}
add_action( 'post_submitbox_misc_actions', 'all_in_one_invite_codes_add_button_to_submit_box' );


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
	return array(
		'email' => '',
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
				<?php _e( 'email', 'all_in_one_invite_codes' ); ?>
            </label>
            <input
                    type="email"
                    name="all_in_one_invite_codes_options[email]"
                    id="all_in_one_invite_codes_options_email"
                    value="<?php echo esc_attr( $all_in_one_invite_codes_options['email'] ); ?>"
            >
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

function all_in_one_invite_codes_md5( $post_id = false ) {
	global $post;

	if ( ! $post_id ) {
		$post_id = $post->ID;
	}

	$md5 = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );

	if ( ! $md5 ) {
		$md5 = substr( md5( time() * rand() ), 0, 24 );
	}

	return $md5;
}


