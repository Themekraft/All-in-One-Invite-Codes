<?php

add_action( 'wp_ajax_all_in_one_invite_codes_create_code', 'all_in_one_invite_codes_create_code' );
function all_in_one_invite_codes_create_code(){

    if ( empty( $_POST['tk_invite_code'] ) ) {
        return;
    }

    $tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );


    $user_id =  get_current_user_id();
    $args        = array(
        'post_type'   => 'tk_invite_codes',
        'post_author' => $user_id,
        'post_status' => 'publish',
        'post_title'  => $tk_invite_code,
    );

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $generate_codes = isset($_POST['generate_codes']) ? $_POST['generate_codes'] : '';
    $type =  isset($_POST['type']) ? $_POST['type'] : 'any';
    $all_in_one_invite_codes_new_options = array();
    $all_in_one_invite_codes_new_options['email'] =$email;
    $all_in_one_invite_codes_new_options['generate_codes'] = $generate_codes;
    $all_in_one_invite_codes_new_options['$type'] = $generate_codes;

    $new_code_id = wp_insert_post( $args );

    update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', $tk_invite_code );
    update_post_meta( $new_code_id, 'tk_all_in_one_invite_code_status', 'Active' );
    update_post_meta( $new_code_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_new_options );

    $json['message'] = __( 'Invite send out successfully', 'buddyforms' );;
    echo json_encode( $json );
    die();

}
