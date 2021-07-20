<?php
/**
 * Plugin Name:     Affiliate Coupons
 * Plugin URI:      https://affcoups.com
 * Description:     The best WordPress coupon plugin which helps you to earn more affiliate money!
 * Version:         1.6.7.1
 * Author:          Affiliate Coupons
 * Author URI:      https://affcoups.com
 * Text Domain:     affiliate-coupons
 *
 * @author          Affiliate Coupons
 * @copyright       Copyright (c) Affiliate Coupons
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Affiliate_Coupons' ) ) :

    /**
     * Main Affiliate_Coupons class
     *
     * @since       1.0.0
     */
    final class Affiliate_Coupons {
        /** Singleton *************************************************************/

        /**
         * Affiliate_Coupons instance.
         *
         * @access private
         * @since  1.0.0
         * @var    Affiliate_Coupons The one true Affiliate_Coupons
         */
        private static $instance;

        /**
         * The settings instance variable.
         *
         * @access public
         * @since  1.0.0
         * @var    Affcoups_Settings
         */
        public $settings;

        /**
         * The version number of Affiliate_Coupons.
         *
         * @access private
         * @since  1.0.0
         * @var    string
         */
        private $version = '1.6.7.1';

        /**
         * Main Affiliate_Coupons Instance
         *
         * Insures that only one instance of Affiliate_Coupons exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0
         * @static
         * @staticvar array $instance
         * @uses Affiliate_Coupons::setup_globals() Setup the globals needed
         * @uses Affiliate_Coupons::includes() Include the required files
         * @uses Affiliate_Coupons::setup_actions() Setup the hooks and actions
         * @return Affiliate_Coupons
         */
        public static function instance() {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Affiliate_Coupons ) ) {
                self::$instance = new Affiliate_Coupons;

                if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {

                    add_action( 'admin_notices', array( 'Affiliate_Coupons', 'below_php_version_notice' ) );

                    return self::$instance;
                }

                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->setup_objects();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.0.0
         * @access protected
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-coupons' ), '1.0' );
        }

        /**
         * Disable unserializing of the class
         *
         * @since 1.0.0
         * @access protected
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-coupons' ), '1.0' );
        }

        /**
         * Show a warning to sites running PHP < 5.3
         *
         * @static
         * @access private
         * @since 1.5.0
         * @return void
         */
        public static function below_php_version_notice() {
            ?>
            <div class="error">
                <p>
                    <?php sprintf( esc_html__( 'Your version of PHP is below the minimum version of PHP required by our plugin. Please contact your hosting company and request that your version will be upgraded to %1$s or later.', 'affiliate-coupons' ), '5.3' ); ?>
                </p>
            </div>
            <?php
        }

        /**
         * Setup plugin constants
         *
         * @access private
         * @since 1.0.0
         * @return void
         */
        private function setup_constants() {
            // Plugin version
            if ( ! defined( 'AFFCOUPS_VERSION' ) ) {
                define( 'AFFCOUPS_VERSION', $this->version );
            }

            // Plugin Folder Path
            if ( ! defined( 'AFFCOUPS_PLUGIN_DIR' ) ) {
                define( 'AFFCOUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }

            // Plugin Folder URL
            if ( ! defined( 'AFFCOUPS_PLUGIN_URL' ) ) {
                define( 'AFFCOUPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }

            // Plugin Root File
            if ( ! defined( 'AFFCOUPS_PLUGIN_FILE' ) ) {
                define( 'AFFCOUPS_PLUGIN_FILE', __FILE__ );
            }

            // Docs URL
            if ( ! defined( 'AFFCOUPS_DOCS_URL' ) ) {
                define( 'AFFCOUPS_DOCS_URL', 'https://affcoups.com/support/knb/' );
            }

            // WordPress.org URL
            if ( ! defined( 'AFFCOUPS_WP_ORG_URL' ) ) {
                define( 'AFFCOUPS_WP_ORG_URL', 'https://wordpress.org/plugins/affiliate-coupons/');
            }

            // Plugin prefix
            if ( ! defined( 'AFFCOUPS_PREFIX' ) ) {
                define( 'AFFCOUPS_PREFIX', 'affcoups_' );
            }

            // Post Types
            if ( ! defined( 'AFFCOUPS_COUPON_POST_TYPE' ) ) {
                define( 'AFFCOUPS_COUPON_POST_TYPE', 'affcoups_coupon' );
            }

            if ( ! defined( 'AFFCOUPS_VENDOR_POST_TYPE' ) ) {
                define( 'AFFCOUPS_VENDOR_POST_TYPE', 'affcoups_vendor' );
            }

            // Taxonomies
            if ( ! defined( 'AFFCOUPS_COUPON_CATEGORY_TAXONOMY' ) ) {
                define( 'AFFCOUPS_COUPON_CATEGORY_TAXONOMY', 'affcoups_coupon_category' );
            }

            if ( ! defined( 'AFFCOUPS_COUPON_TYPE_TAXONOMY' ) ) {
                define( 'AFFCOUPS_COUPON_TYPE_TAXONOMY', 'affcoups_coupon_type' );
            }
        }

        /**
         * Include required files
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function includes() {

            // Dependencies
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/vendor/meta-box/meta-box.php';

            // Basic
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/helper.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/scripts.php';

            // Admin only
            if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/plugins.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/class-pages.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/class-settings.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/manage-coupons.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/manage-vendors.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/manage-categories.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/manage-types.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/hooks.php';
                require_once AFFCOUPS_PLUGIN_DIR . 'includes/admin/upgrades.php';
            }

            // Coupons
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/coupon-post-type.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/coupon-type-taxonomy.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/coupon-category-taxonomy.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/coupon-metaboxes.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/class-coupon.php';

            // Vendors
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/vendor-post-type.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/vendor-metaboxes.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/class-vendor.php';

            // Anything else
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/hooks.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/functions.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/assets.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/shortcodes.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/template-functions.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/widgets.php';
            require_once AFFCOUPS_PLUGIN_DIR . 'includes/pro-functions.php';
        }

        /**
         * Setup all objects
         *
         * @access public
         * @since 1.6.2
         * @return void
         */
        public function setup_objects() {

            if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                self::$instance->settings = new Affcoups_Settings();
            }
        }

        /**
         * Loads the plugin language files
         *
         * @access public
         * @since 1.0
         * @return void
         */
        public function load_textdomain() {

            // Set filter for plugin's languages directory
            $lang_dir = dirname( plugin_basename( AFFCOUPS_PLUGIN_FILE ) ) . '/languages/';

            /**
             * Filters the languages directory path to use for Affiliate_Coupons.
             *
             * @param string $lang_dir The languages directory path.
             */
            $lang_dir = apply_filters( 'affiliate_coupons_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter

            global $wp_version;

            $get_locale = get_locale();

            if ( $wp_version >= 4.7 ) {
                $get_locale = get_user_locale();
            }

            /**
             * Defines the plugin language locale used in Affiliate_Coupons.
             *
             * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
             *                  otherwise uses `get_locale()`.
             */
            $locale = apply_filters( 'plugin_locale', $get_locale, 'affiliate-coupons' );
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
endif; // End if class_exists check

/**
 * The main function responsible for returning the one true Affiliate_Coupons
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $Affiliate_Coupons = Affiliate_Coupons(); ?>
 *
 * @since 1.0
 * @return Affiliate_Coupons The one true Affiliate_Coupons Instance
 */
function Affiliate_Coupons() {
    return Affiliate_Coupons::instance();
}

/**
 * Init plugin
 *
 * @return bool
 */
function affcoups_load() {
    Affiliate_Coupons();

    return true;
}
add_action( 'plugins_loaded', 'affcoups_load', 10 );