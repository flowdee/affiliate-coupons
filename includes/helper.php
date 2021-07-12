<?php
/**
 * Helper
 *
 * @package     AffiliateCoupons\Helper
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cleanup string in order to be used inside html attributes (title, alt etc.) without breaking the markup
 *
 * @param $str
 *
 * @return mixed|string
 */
function affcoups_cleanup_html_attribute( $str ) {

	// Remove html.
	$str = strip_tags( $str );

	// Remove apostrophes.
	$str = str_replace( array( '"', "'" ), '', $str );

	return $str;
}

/**
 * Convert separated strings to array
 *
 * @param $string
 *
 * @return array
 */
function affcoups_convert_separated_strings_to_array( $string ) {

	// Remove spaces.
	$string = str_replace( ' ', '', $string );

	// Convert to array.
	$array = explode( ',', $string );

	return $array;
}

function affcoups_array_insert_after($array, $findAfter, $key, $new) {
    $pos = (int) array_search($findAfter, array_keys($array)) + 1;
    return array_merge(
        array_slice($array, 0, $pos),
        array($key => $new),
        array_slice($array, $pos)
    );
}

/**
 * Get options
 *
 * return array options or empty when not available
 */
function affcoups_get_options() {
	return get_option( 'affcoups_settings', array() );
}

/**
 * Get single option
 *
 * @param   string $key
 * @param   mixed $default
 *
 * @return  mixed
 */
function affcoups_get_option( $key, $default = null ) {
	$options = affcoups_get_options();

	return ( isset( $options[ $key ] ) ) ? $options[ $key ] : $default;
}

/**
 * Set or Update single option
 *
 * @param   string $key
 * @param   mixed $value
 *
 * @return  void
 */
function affcoups_update_option( $key, $value ) {
	$options = affcoups_get_options();

    $options[$key] = $value;

	update_option( 'affcoups_settings', $options );
}

/**
 * Output public assets folder url
 */
function affcoups_the_assets() {
	echo esc_url( affcoups_get_assets_url() );
}

/**
 * Get public assets folder url
 *
 * @return string
 */
function affcoups_get_assets_url() {
	return AFFCOUPS_PLUGIN_URL . 'assets/';
}

/**
 * Check whether it's development environment or not
 */
function affcoups_is_development() {

	return ( strpos( get_bloginfo( 'url' ), 'affcoups-downloads.local' ) !== false
         ||  strpos( get_bloginfo( 'url' ), 'test.loc' ) !== false
         ||  strpos( get_bloginfo( 'url' ), 'dev.flowdee.de' ) !== false
    ) ? true : false;
}

/**
 * Check if AMP endpoint
 */
function affcoups_is_amp() {

	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return true;
	}

	if ( function_exists( 'is_wp_amp' ) && is_wp_amp() ) {
		return true;
	}

	return false;
}

/**
 * Check whether we are on our admin pages or not
 *
 * @return bool
 */
function affcoups_is_plugin_admin_area() {

	$screen = get_current_screen();

	return ( strpos( $screen->id, 'affcoups' ) !== false ) ? true : false;
}

/**
 * Check whether we are on a block editor page
 *
 * @return bool
 */
function affcoups_is_block_editor() {

    $screen = get_current_screen();

    return $screen->is_block_editor();
}

/**
 * Check whether we are on our plugin coupons page or not
 *
 * @return bool
 */
function affcoups_is_plugin_admin_area_coupons() {

    $screen = get_current_screen();

    return ( isset ( $screen->post_type ) && AFFCOUPS_COUPON_POST_TYPE === $screen->post_type ) ? true : false;
}

/**
 * Check whether we are on our plugin vendors page or not
 *
 * @return bool
 */
function affcoups_is_plugin_admin_area_vendors() {

    $screen = get_current_screen();

    return ( isset ( $screen->post_type ) && AFFCOUPS_VENDOR_POST_TYPE === $screen->post_type ) ? true : false;
}

/**
 * Check whether we are on our plugin settings page or not
 *
 * @return bool
 */
function affcoups_is_plugin_admin_area_settings() {

    $screen = get_current_screen();

    return ( strpos( $screen->id, 'affcoups_settings' ) !== false ) ? true : false;
}

/**
 * Get website url
 *
 * @param $path
 * @param string $source
 *
 * @return string
 */
function affcoups_get_website_url( $path = '', $source = 'plugin-settings' ) {

	$url = 'https://affcoups.com/';

	if ( ! empty( $path ) ) {
		$url .= trim( $path, '/' ) . '/';
	}

	return esc_url( add_query_arg( array(
		'utm_source'   => $source,
		'utm_medium'   => 'plugin-row',
		'utm_campaign' => 'Affiliate Coupons',
	), $url ) );
}

/**
 * Truncate string
 *
 * @param $str
 * @param int $limit
 * @param string $pad
 *
 * @return mixed|string
 */
function affcoups_truncate_string( $str, $limit = 200, $pad = '...' ) {

	$limit = intval( $limit );

	if ( strlen( $str ) > $limit ) {
		$str = preg_replace( '/\s+?(\S+)?$/', '', substr( $str, 0, $limit + 1 ) );

		$str = rtrim( $str, '.' );
		$str = rtrim( $str, ',' );
		$str = rtrim( $str, '-' );

		$str .= $pad;
	}

	return $str;
}

/**
 * Remove url parameter
 *
 * @param $url
 * @return null|string|string[]
 */
function affcoups_remove_url_params( $url ) {

    $url = preg_replace('/\\?.*/', '', $url);

    return $url;
}

/**
 * Output data to a log for debugging reasons
 *
 * @param $string
 */
function affcoups_addlog( $string ) {

    $log = get_option( 'affcoups_log', '' );
    $string = date( 'd.m.Y H:i:s' ) . " >>> " . $string . "\n";
    $log .= $string;
    update_option( 'affcoups_log', $log );
}

// @codingStandardsIgnoreStart
/**
 * Debug
 *
 * @param $args
 * @param bool $title
 */
function affcoups_debug( $args, $title = false ) {

    if ( ! affcoups_is_development() ) {
        return;
    }

	if ( $title ) {
		echo '<h3>' . esc_html( $title ) . '</h3>';
	}

	if ( $args ) {
		echo '<pre>';
		print_r( $args );
		echo '</pre>';
	}
}

/**
 * Debug log
 *
 * @param $log
 */
function affcoups_debug_log( $log ) {

	if ( ! affcoups_is_development() ) {
		return;
	}

	if ( is_array( $log ) || is_object( $log ) ) {
		error_log( print_r( $log, true ) );
	} else {
		error_log( $log );
	}
}
// @codingStandardsIgnoreEnd

/**
 * Fallback for missing "rwmb_meta" function
 * Source: https://docs.metabox.io/rwmb-meta/
 */
if ( ! function_exists( 'rwmb_meta' ) ) {
	function rwmb_meta( $key, $args = '', $post_id = null ) {
		return false;
	}
}
