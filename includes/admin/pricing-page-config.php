<?php
/**
 * Pricing page filter registration for the All in One Invite Codes plugin.
 * Provides bundle credentials, copy, and tier data to the shared pricing-page submodule.
 *
 * @package all_in_one_invite_codes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'all_in_one_invite_codes_pricing_page_config' ) ) {
	/**
	 * @param array<string,mixed> $config Default config from the submodule.
	 * @return array<string,mixed>
	 */
	function all_in_one_invite_codes_pricing_page_config( $config ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! str_contains( $screen->id, 'tk_invite_codes_bundle_screen' ) ) {
			return $config;
		}

		$config['heading']    = __( 'Get the Invite Codes Bundle', 'all_in_one_invite_codes' );
		$config['subheading'] = __( 'Unlock the full Invite Codes family — BuddyPress, BuddyForms and WooCommerce extensions, with a year of updates and support.', 'all_in_one_invite_codes' );

		$config['bundle'] = array(
			'product_id' => '8013',
			'plan_id'    => '13146',
			'public_key' => 'pk_b8b8e319fd537d6d44d73a448f64e',
			'name'       => __( 'Invite Codes Bundle', 'all_in_one_invite_codes' ),
		);

		$bullets = array(
			array(
				'label'     => __( 'All Invite Codes add-ons included', 'all_in_one_invite_codes' ),
				'highlight' => true,
			),
			array(
				'label' => __( 'All in One Invite Codes', 'all_in_one_invite_codes' ),
				'url'   => 'https://themekraft.com/wordpress-products/all-in-one-invite-codes/',
			),
			array(
				'label' => __( 'All in One Invite Codes BuddyPress', 'all_in_one_invite_codes' ),
				'url'   => 'https://themekraft.com/wordpress-products/invite-codes-buddypress/',
			),
			array(
				'label' => __( 'All in One Invite Codes BuddyForms', 'all_in_one_invite_codes' ),
				'url'   => 'https://themekraft.com/wordpress-products/restrict-forms-invite-codes/',
			),
			array(
				'label' => __( 'All in One Invite Codes WooCommerce Checkout', 'all_in_one_invite_codes' ),
				'url'   => 'https://themekraft.com/wordpress-products/invite-codes-woocommerce/',
			),
			__( 'One year of support', 'all_in_one_invite_codes' ),
			__( 'One year of updates', 'all_in_one_invite_codes' ),
		);

		$config['tiers'] = array(
			array(
				'id'       => 'personal',
				'name'     => __( 'Personal Plan', 'all_in_one_invite_codes' ),
				'sites'    => __( 'One Site', 'all_in_one_invite_codes' ),
				'licenses' => '1',
				'price'    => '99.99',
				'bullets'  => $bullets,
			),
			array(
				'id'        => 'professional',
				'name'      => __( 'Professional Plan', 'all_in_one_invite_codes' ),
				'sites'     => __( 'Five Sites', 'all_in_one_invite_codes' ),
				'licenses'  => '5',
				'price'     => '149.99',
				'highlight' => true,
				'bullets'   => $bullets,
			),
			array(
				'id'       => 'agency',
				'name'     => __( 'Agency Plan', 'all_in_one_invite_codes' ),
				'sites'    => __( 'Unlimited Sites', 'all_in_one_invite_codes' ),
				'licenses' => 'unlimited',
				'price'    => '249.99',
				'bullets'  => $bullets,
			),
		);

		return $config;
	}
}
add_filter( 'tk_pricing_page_config', 'all_in_one_invite_codes_pricing_page_config' );
