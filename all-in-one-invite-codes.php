<?php

/**
 * Plugin Name: All in One Invite Codes
 * Plugin URI:  https://themekraft.com/all-in-one-invite-codes/
 * Description: Create Invite only Registration Funnels and Products. Boost your site launch and get the attention you desire by creating an intelligent invite only Platform.
 * Version: 1.0.4
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/
 * Licence: GPLv3
 * Network: false
 * Text Domain: all-in-one-invite-codes
 * Domain Path: /languages
 *
 * ****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 ****************************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'AllinOneInviteCodes' ) ) {
	/**
	 * Class AllinOneInviteCodes
	 */
	class AllinOneInviteCodes {

		/**
		 * @var string
		 */
		public $version = '1.0.4';

		/**
		 * @var string Assets URL
		 */
		public static $assets;

		/**
		 * Initiate the class
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		public function __construct() {

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

			$this->load_constants();

			add_action( 'init', array( $this, 'init_hook' ), 1, 1 );
			add_action( 'init', array( $this, 'includes' ), 4, 1 );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ), 102, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ), 102, 1 );


			add_action( 'wp_enqueue_scripts', array( $this, 'front_js_loader' ), 102, 1 );


			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
		}

		/**
		 * Defines constants needed throughout AllinOneInviteCodes
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		public function load_constants() {

			/**
			 * Define the plugin version
			 */
			define( 'TK_ALL_IN_ONE_INVITE_CODES_VERSION', $this->version );

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL' ) ) {
				/**
				 * Define the plugin url
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
			}

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH' ) ) {
				/**
				 * Define the install path
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH', dirname( __FILE__ ) . '/' );
			}

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH' ) ) {
				/**
				 * Define the include path
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH', TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH . 'includes/' );
			}

			if ( ! defined( 'TK_ALL_IN_ONE_INVITE_CODES_TEMPLATE_PATH' ) ) {
				/**
				 * Define the template path
				 */
				define( 'TK_ALL_IN_ONE_INVITE_CODES_TEMPLATE_PATH', TK_ALL_IN_ONE_INVITE_CODES_INSTALL_PATH . 'templates/' );
			}

		}

		/**
		 * Defines all_in_one_invite_codes_init action
		 *
		 * This action fires on WP's init action and provides a way for the rest of WP,
		 * as well as other dependent plugins, to hook into the loading process in an
		 * orderly fashion.
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		public function init_hook() {
			$this->set_globals();
			do_action( 'all_in_one_invite_codes_init' );
		}

		/**
		 * Setup all globals
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		static function set_globals() {
			global $all_in_one_invite_codes;

			/*
			 * Get AllinOneInviteCodes options
			 *
			 * @filter: all_in_one_invite_codes_set_globals
			 *
			 */
			$all_in_one_invite_codes = apply_filters( 'tk_all_in_one_invite_codes_set_globals', get_option( 'tk_all_in_one_invite_codes_options' ) );

			return $all_in_one_invite_codes;
		}

		/**
		 * Include files needed by AllinOneInviteCodes
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		public function includes() {

			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'functions.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'default-registration.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'process-invite-code.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'generate-invite-codes.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'shortcodes.php' );
			require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . 'send-invite-email.php' );

			if ( is_admin() ) {
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/admin-settings.php' );
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/tree.php' );
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/admin-ajax.php' );
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/invite-codes-post-type.php' );
				require_once( TK_ALL_IN_ONE_INVITE_CODES_INCLUDES_PATH . '/admin/invite-codes-options.php' );
			}
		}

		/**
		 * Load the textdomain for the plugin
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'all_in_one_invite_codes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Enqueue the needed CSS for the admin screen
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 *
		 * @param $hook_suffix
		 */
		function admin_styles( $hook_suffix ) {

		}

		/**
		 * Enqueue the needed JS for the admin screen
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 *
		 * @param $hook_suffix
		 */
		function admin_js( $hook_suffix ) {

            wp_register_script( 'all-in-one-invite_codes-admin-js', plugins_url( 'assets/admin/js/admin.js', __FILE__ ), array(), $this->version );
            wp_enqueue_script( 'all-in-one-invite_codes-admin-js' );

            wp_localize_script('all-in-one-invite_codes-admin-js', 'allInOneInviteCodesAdminJs', array( 'nonce' => wp_create_nonce('all_in_one_invite_code_nonce') ) );

		}

		/**
		 * Check if a all_in_one_invite_codes view is displayed and load the needed styles and scripts
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		function front_js_loader() {
			wp_register_script( 'all-in-one-invite_codes-front-js', plugins_url( 'assets/js/front.js', __FILE__ ), array('jquery'), $this->version );
			wp_enqueue_script( 'all-in-one-invite_codes-front-js' );

			wp_localize_script('all-in-one-invite_codes-front-js', 'allInOneInviteCodesFrontJs', array( 'nonce' => wp_create_nonce('all_in_one_invite_code_nonce') ) );

		}

		/**
		 * Enqueue the needed JS for the form in the frontend
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		function front_js_css() {

		}

		/**
		 * Update form 1.x version
		 *
		 * @package all_in_one_invite_codes
		 * @since  0.1
		 */
		function update_db_check() {

			if ( ! is_admin() ) {
				return;
			}

		}


		/**
		 * Plugin activation
		 * @since  0.1
		 */
		function plugin_activation() {

		}

		/**
		 * Plugin deactivation
		 * @since  0.1
		 */
		function plugin_deactivation() {

		}
	}

	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return Freemius
	 */
	function all_in_one_invite_codes_core_fs() {
		global $all_in_one_invite_codes_core_fs;

		$first_path = get_option( 'all_in_one_invite_codes_first_path_after_install' );

		if ( ! isset( $all_in_one_invite_codes_core_fs ) ) {

			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/includes/resources/freemius/start.php';

			$all_in_one_invite_codes_core_fs = fs_dynamic_init( array(
				'id'             => '3322',
				'slug'           => 'all-in-one-invite-codes',
				'type'           => 'plugin',
				'public_key'     => 'pk_955be38b0c4d2a2914a9f4bc98355',
				'is_premium'     => false,
				'has_addons'     => true,
				'has_paid_plans' => false,
				'menu'           => array(
					'slug'    => 'edit.php?post_type=tk_invite_codes',
					'support' => false,

				),
			) );
		}

		return $all_in_one_invite_codes_core_fs;
	}

	function all_in_one_invite_codes_php_version_admin_notice() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'PHP Version Update Required!', 'all_in_one_invite_codes' ); ?></p>
            <p><?php _e( 'You are using PHP Version ' . PHP_VERSION, 'all_in_one_invite_codes' ); ?></p>
            <p><?php _e( 'Please make sure you have at least php version 5.3 installed.', 'all_in_one_invite_codes' ); ?></p>
        </div>
		<?php
	}

	function activate_all_in_one_invite_codes_at_plugin_loader() {
		// AllinOneInviteCodes requires php version 5.3 or higher.
		if ( PHP_VERSION < 5.3 ) {
			add_action( 'admin_notices', 'all_in_one_invite_codes_php_version_admin_notice' );
		} else {
			// Init AllinOneInviteCodes.
			$GLOBALS['all_in_one_invite_codes_new'] = new AllinOneInviteCodes();
			// Init Freemius.
			all_in_one_invite_codes_core_fs();
			// Signal that parent SDK was initiated.
			do_action( 'all_in_one_invite_codes_core_fs_loaded' );
			// GDPR Admin Notice
			all_in_one_invite_codes_core_fs()->add_filter( 'handle_gdpr_admin_notice', '__return_true' );

			if ( all_in_one_invite_codes_core_fs()->is__premium_only() ) {
				define( 'TK_ALL_IN_ONE_INVITE_CODES_PRO_VERSION', 'pro' );
			}
		}
	}

	activate_all_in_one_invite_codes_at_plugin_loader();
}
