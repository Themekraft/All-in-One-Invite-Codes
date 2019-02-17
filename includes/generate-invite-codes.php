<?php

add_action( 'user_register', 'all_in_one_invite_codesuser_register' );
function all_in_one_invite_codesuser_register( $user_id ) {
	if ( ! empty( $_POST['tk_invite_code'] ) ) {
		update_user_meta( $user_id, 'tk_all_in_one_invite_code', sanitize_text_field( $_POST['tk_invite_code'] ) );


	}
}
