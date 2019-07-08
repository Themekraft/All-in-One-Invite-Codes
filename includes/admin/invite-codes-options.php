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
 * Create the code default values
 */
function all_in_one_invite_codes_options_defaults() {
	$all_in_one_invite_codes_general = get_option( 'all_in_one_invite_codes_general' );

	return array(
		'email'          => '',
		'generate_codes' => isset( $all_in_one_invite_codes_general['generate_codes_amount'] ) ? $all_in_one_invite_codes_general['generate_codes_amount'] : '',
	);
}

/**
 * Render the metabox and display the options
 */
function all_in_one_invite_codes_render_metabox() {
	global $post;

	// Get or generate the invite code
	$all_in_one_invite_code = all_in_one_invite_codes_md5( $post->ID );

	// Get the invite code options
	$all_in_one_invite_codes_options = get_post_meta( $post->ID, 'all_in_one_invite_codes_options', true );

	// Get the default values
	$all_in_one_invite_codes_options_defaults = all_in_one_invite_codes_options_defaults();

	// Merge the options so we have the default take care of the missing values.
	$all_in_one_invite_codes_options = wp_parse_args( $all_in_one_invite_codes_options, $all_in_one_invite_codes_options_defaults );

	$email          = isset( $all_in_one_invite_codes_options['email'] ) ? $all_in_one_invite_codes_options['email'] : '';
	$generate_codes = isset( $all_in_one_invite_codes_options['generate_codes'] ) ? $all_in_one_invite_codes_options['generate_codes'] : '';
	$type = isset( $all_in_one_invite_codes_options['type'] ) ? $all_in_one_invite_codes_options['type'] : 'registration';

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

            $type_options =  array();
            $type_options['any'] = __( 'Any', 'all-in-one-invite-codes' );
            $type_options['register'] = __( 'Register', 'all-in-one-invite-codes' );


            $type_options = apply_filters( 'all_in_one_invite_codes_options_type_options', $type_options )

            ?>
            <p>
                Purpose: <select
                        name="all_in_one_invite_codes_options[type]"
                        id="all_in_one_invite_codes_options_type"
                        value="<?php echo esc_attr( $type ); ?>">

                    <?php foreach ($type_options as $slug => $option ) {
	                    echo '<option value="' . $slug . '" >' . $option . '</option >';
                    }
                   ?>

                </select>
            </p>
        </div>
    </fieldset>

	<?php
	// List related codes as child codes
	$args = array(
		'post_parent'    => $post->ID,
		'posts_per_page' => - 1,
		'post_type'      => 'tk_invite_codes', //you can use also 'any'
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();

			echo '<a href="' . get_edit_post_link( get_the_ID() ) . '">' . get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true ) . '</a>';
			echo '<br>';
		endwhile;
	endif;

	wp_reset_postdata();

	// add the nonce check
	wp_nonce_field( 'all_in_one_invite_codes_options_nonce', 'all_in_one_invite_codes_options_process' );

}

/**
 * Save the options
 */
function all_in_one_invite_codes_save_options( $post_id, $post ) {

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
	foreach ( $_POST['all_in_one_invite_codes_options'] as $key => $detail ) {
		$sanitized[ $key ] = wp_filter_post_kses( $detail );
	}

	// Do the update
	update_post_meta( $post_id, 'all_in_one_invite_codes_options', $sanitized );


	if( !get_post_meta($post_id,'tk_all_in_one_invite_code_status',  true)){
		update_post_meta($post_id, 'tk_all_in_one_invite_code_status', 'Active');
    }

}

add_action( 'save_post_tk_invite_codes', 'all_in_one_invite_codes_save_options', 1, 2 );

/**
 * Save the invite code
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

}

add_action( 'save_post', 'all_in_one_invite_codes_save_code', 1, 2 );

/**
 * Remove slugdiv
 */
function all_in_one_invite_codes_remove_slugdiv() {
	remove_meta_box( 'slugdiv', 'tk_invite_codes', 'normal' );
}

add_action( 'admin_menu', 'all_in_one_invite_codes_remove_slugdiv' );


add_filter('wp_insert_post_data', 'all_in_one_invite_codes_change_title');
function all_in_one_invite_codes_change_title($data)
{
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