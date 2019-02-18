<?php

function all_in_one_invite_codes_list_codes( $atts ) {



	?>

	<script>

        jQuery(document).ready(function (jQuery) {
            jQuery(document.body).on('click', '#tk_all_in_one_invie_code_send_invite', function () {
;
                alert('sad');

                var mail_address = '';

                <?php echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";' ?>

                jQuery.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        "action": "all_in_one_invite_codes_send_invite",
                        "post_id": mail_address
                    },
                    success: function (data) {
                        console.log(data);

                        if (data['error']) {
                            alert(data['error']);
                        } else {
                            location.reload();
                        }

                    },
                    error: function (error) {
                        console.log(error);
                    }
                });


            });
        });

	</script>

	<?php


	$a = shortcode_atts( array(
		'foo' => 'something',
		'bar' => 'something else',
	), $atts );


	$args = array(
		'author'         => get_current_user_id(),
		'posts_per_page' => - 1,
		'post_type'      => 'tk_invite_codes', //you can use also 'any'
	);

	$the_query = new WP_Query( $args );


	if ( $the_query->have_posts() ) :
		echo '<ul>';
		while ( $the_query->have_posts() ) : $the_query->the_post();
			echo '<li>';
				echo 'Code: ';
				echo get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true );
				echo '<br>Status: ';
				echo all_in_one_invite_codes_get_status( get_the_ID() );
				echo '<br>Sent Invite: ';
				echo '<a id="tk_all_in_one_invie_code_send_invite" href="#">Send Now</a><br><br>';
			echo '</li>';
		endwhile;
		echo '</ul>';
	endif;

	wp_reset_postdata();

}

add_shortcode( 'all_in_one_invite_codes_list_codes_by_user', 'all_in_one_invite_codes_list_codes' );