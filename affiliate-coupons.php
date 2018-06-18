<?php
/**
 * Plugin Name:     Affiliate Coupons
 * Plugin URI:      https://affcoups.com
 * Description:     Promote coupons and deals of products and earn money with affiliate referrals.
 * Version:         1.4.0
 * Author:          fdmedia
 * Author URI:      https://fdmedia.io
 * Text Domain:     affiliate-coupons
 *
 * @package         AffiliateCoupons
 * @author          flowdee
 * @copyright       Copyright (c) flowdee
 *
 * Copyright (c) 2018 - flowdee ( https://twitter.com/flowdee )
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Affiliate_Coupons' ) ) {

	/**
	 * Main Affiliate_Coupons class
	 *
	 * @since       1.0.0
	 */
	class Affiliate_Coupons {

		/**
		 * @var         Affiliate_Coupons $instance The one true Affiliate_Coupons
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      object self::$instance The one true Affiliate_Coupons
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new Affiliate_Coupons();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {

			// Plugin name
			define( 'AFFCOUPS_NAME', 'Affiliate Coupons' );

			// Plugin version
			define( 'AFFCOUPS_VER', '1.4.0' );

			// Plugin path
			define( 'AFFCOUPS_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'AFFCOUPS_URL', plugin_dir_url( __FILE__ ) );

			// Plugin prefix
			define( 'AFFCOUPS_PREFIX', 'affcoups_' );
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {

			// Dependencies
			require_once AFFCOUPS_DIR . 'includes/bootstrap.php';

			// Basic
			require_once AFFCOUPS_DIR . 'includes/helper.php';
			require_once AFFCOUPS_DIR . 'includes/scripts.php';

			// Admin only
			if ( is_admin() ) {
				require_once AFFCOUPS_DIR . 'includes/admin/plugins.php';
				require_once AFFCOUPS_DIR . 'includes/admin/class.settings.php';
				require_once AFFCOUPS_DIR . 'includes/admin/manage-coupons.php';
				require_once AFFCOUPS_DIR . 'includes/admin/manage-vendors.php';
				require_once AFFCOUPS_DIR . 'includes/admin/manage-categories.php';
				require_once AFFCOUPS_DIR . 'includes/admin/manage-types.php';
				require_once AFFCOUPS_DIR . 'includes/admin/hooks.php';
			}

			// Coupons
			require_once AFFCOUPS_DIR . 'includes/coupon-post-type.php';
			require_once AFFCOUPS_DIR . 'includes/coupon-type-taxonomy.php';
			require_once AFFCOUPS_DIR . 'includes/coupon-category-taxonomy.php';
			require_once AFFCOUPS_DIR . 'includes/coupon-functions.php';
			require_once AFFCOUPS_DIR . 'includes/coupon-metaboxes.php';

			// Vendors
			require_once AFFCOUPS_DIR . 'includes/vendor-post-type.php';
			require_once AFFCOUPS_DIR . 'includes/vendor-functions.php';
			require_once AFFCOUPS_DIR . 'includes/vendor-metaboxes.php';

			// Anything else
			require_once AFFCOUPS_DIR . 'includes/hooks.php';
			require_once AFFCOUPS_DIR . 'includes/functions.php';
			require_once AFFCOUPS_DIR . 'includes/assets.php';
			require_once AFFCOUPS_DIR . 'includes/shortcodes.php';
			require_once AFFCOUPS_DIR . 'includes/template-functions.php';
			require_once AFFCOUPS_DIR . 'includes/widgets.php';
		}

		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = AFFCOUPS_DIR . '/languages/';
			$lang_dir = apply_filters( 'affiliate_coupons_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'affiliate-coupons' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'affiliate-coupons', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliate-coupons/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliate-coupons/ folder
				load_textdomain( 'affiliate-coupons', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliate-coupons/languages/ folder
				load_textdomain( 'affiliate-coupons', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliate-coupons', false, $lang_dir );
			}
		}
	}
} // End if class_exists check

/**
 * The main function responsible for returning the one true Affiliate_Coupons
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Affiliate_Coupons The one true Affiliate_Coupons
 *
 */
function affcoups_load() {
	return Affiliate_Coupons::instance();
}

add_action( 'plugins_loaded', 'affcoups_load' );

/**
 * The activation hook
 */
function affcoups_activation() {
	// Create your tables here
}

register_activation_hook( __FILE__, 'affcoups_activation' );

/**
 * The deactivation hook
 */
function affcoups_deactivation() {
	// Cleanup your tables, transients etc. here
}

register_deactivation_hook( __FILE__, 'affcoups_deactivation' );
