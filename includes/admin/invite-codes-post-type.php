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
		"hierarchical"          => true,
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

		unset( $actions['inline hide-if-no-js'] );

		$base = home_url();

		$preview_page_id = get_option( 'buddyforms_preview_page', true );

		$actions['resent']  = '<a href="#" data-post_id="' . $post->ID . '" id="all_in_one_disable_invite_code">Disable</a>';
		$actions['disable'] = '<a href="#" data-post_id="' . $post->ID . '" id="all_in_one_resent_invite_code">Resent Invitation</a>';


	}

	return $actions;
}

add_filter( 'post_row_actions', 'all_in_one_invite_codes_add_action_buttons', 10, 2 );


function tk_invite_codes_columns( $columns, $post_id = false ) {
	unset( $columns['date'] );
	unset( $columns['title'] );

	$columns['code']           = __( 'Code', 'all-in-one-invite-codes' );
	$columns['status']         = __( 'Status', 'all-in-one-invite-codes' );
	$columns['email']          = __( 'eMail', 'all-in-one-invite-codes' );
	$columns['parent']          = __( 'Parent', 'all-in-one-invite-codes' );
	$columns['generate_codes'] = __( 'Generate new codes after account activation', 'all-in-one-invite-codes' );

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
			$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

			switch ( $status ) {
				case 'disabled' :
					$status = __( 'Disabled', 'all-in-one-invite-codes' );
					break;
				case 'used' :
					$status = __( 'Used', 'all-in-one-invite-codes' );
					break;

			}

			echo empty( $status ) ? __( 'Active', 'all-in-one-invite-codes' ) : $status;
			break;
		case 'email' :
			echo isset( $all_in_one_invite_codes_options['email'] ) ? $all_in_one_invite_codes_options['email'] : '--';
			break;
		case 'generate_codes' :
			echo isset( $all_in_one_invite_codes_options['generate_codes'] ) ? $all_in_one_invite_codes_options['generate_codes'] : '--';
			break;
		case 'parent' :
			echo wp_get_post_parent_id( $post_id );
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
                display: none;
            }

        </style>
        <script>
            jQuery(document).ready(function (jQuery) {

                jQuery('body').find('h1:first').remove();
                jQuery('body').find('#post-body-content').remove();
                jQuery('body').find('.wp-heading-inline').remove();


				<?php
				$status = get_post_meta( $post->ID, 'tk_all_in_one_invite_code_status', true );


				if ( $status == 'disabled' ) {?>
                jQuery('body').find('.postbox-container h2').text('Disabled');
                jQuery('body').find('#publish').remove();
                jQuery("#post :input").prop("disabled", true);
				<?php } ?>

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

	if ( get_post_status() != 'publish' ) {
		return;
	}
	?>

    <div id="all-in-one-invite-codes-actions" class="misc-pub-section">
        <p><a href="#" data-post_id="<?php echo $post->ID ?>" id="all_in_one_disable_invite_code"
              class="button button-large bf_button_action">Disable This Invite Code</a></p>
        <p><a href="#" data-post_id="<?php echo $post->ID ?>" id="all_in_one_resent_invite_code"
              class="button button-large bf_button_action">Resent Invitation Mail</a></p>
        <div class="clear"></div>
    </div>

	<?php

}

add_action( 'post_submitbox_misc_actions', 'all_in_one_invite_codes_add_button_to_submit_box' );

