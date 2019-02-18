<?php

function all_in_one_invite_codes_list_codes( $atts ) { ?>

	<script>

        jQuery(document).ready(function (jQuery) {
            jQuery(document.body).on('click', '#tk_all_in_one_invite_code_send_invite_submit', function () {

                var code_id = jQuery(this).attr('data-send_code_id');

                <?php echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";' ?>

                jQuery.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        "action": "all_in_one_invite_codes_send_invite",
                        "post_id": code_id
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

            jQuery(document.body).on('click', '#tk_all_in_one_invite_code_open_invite_form', function () {
                var code_id = jQuery(this).attr('data-code_id');

                alert(code_id);

                jQuery("#tk_all_in_one_invite_code_send_invite_form").appendTo("#tk_all_in_one_invie_code_open_invite_form_id_" + code_id);
                jQuery("#tk_all_in_one_invite_code_send_invite_form").show();

                jQuery("#tk_all_in_one_invite_code_send_invite_submit").attr('data-send_code_id',code_id);
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
				echo '<br>';
				echo '<a data-code_id="' . get_the_ID() . '" id="tk_all_in_one_invite_code_open_invite_form" href="#">Invite a Friend Now</a><div id="tk_all_in_one_invie_code_open_invite_form_id_' . get_the_ID() . '"></div><br>';
			echo '</li>';
		endwhile;
		echo '</ul>';


		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' )

		?>

        <div style="display: none" id="tk_all_in_one_invite_code_send_invite_form">
            <p>To: <input type="text" name="tk_all_in_one_invie_code_send_invite[to]" value=""></p>
            <p>Subject: <input type="text" name="tk_all_in_one_invie_code_send_invite[subject]" value="<?php echo empty($all_in_one_invite_codes_mail_templates['subject']) ? '' : $all_in_one_invite_codes_mail_templates['subject']; ?>"></p>
            <p>Message Text:<textarea cols="70" rows="5" name=tk_all_in_one_invie_code_send_invite[message_text]"><?php echo empty($all_in_one_invite_codes_mail_templates['message_text']) ? '' : $all_in_one_invite_codes_mail_templates['message_text']; ?></textarea></p>
            <a href="#" data-send_code_id="0" id="tk_all_in_one_invite_code_send_invite_submit" class="button">Send</a>
        </div>

	<?php

	endif;

	wp_reset_postdata();

}

add_shortcode( 'all_in_one_invite_codes_list_codes_by_user', 'all_in_one_invite_codes_list_codes' );