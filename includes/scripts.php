<?php
/**
 * Scripts
 *
 * @package     AffiliateCoupons\Scripts
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function affcoups_admin_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) ? '' : '.min';

    /**
     *	Settings page only
     */
    $screen = get_current_screen();

    if ( ! empty( $screen->base ) && ( $screen->base == 'settings_page_affiliate_coupons' || $screen->base == 'widgets' ) ) {

        wp_enqueue_script( 'affcoups-admin-script', AFFCOUPS_URL . 'public/assets/js/admin' . $suffix . '.js', array( 'jquery' ), AFFCOUPS_VER );
        wp_enqueue_style( 'affcoups-admin-style', AFFCOUPS_URL . 'public/assets/css/admin' . $suffix . '.css', false, AFFCOUPS_VER );
    }
}
add_action( 'admin_enqueue_scripts', 'affcoups_admin_scripts', 100 );

/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function affcoups_scripts( $hook ) {

    if ( affcoups_has_plugin_content() ) {

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_script( 'affcoups-script', AFFCOUPS_URL . 'public/assets/js/scripts' . $suffix . '.js', array( 'jquery' ), AFFCOUPS_VER, true );
        wp_enqueue_style( 'affcoups-style', AFFCOUPS_URL . 'public/assets/css/styles' . $suffix . '.css', false, AFFCOUPS_VER );

    }
}
add_action( 'wp_enqueue_scripts', 'affcoups_scripts' );