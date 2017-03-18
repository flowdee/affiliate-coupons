<?php
/**
 * Functions
 *
 * @package     AffiliateCoupons\Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Get content from a single post
 */
function affcoups_get_post_content( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $post = get_post( $postid );
    $content = $post->post_content;
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);

    return $content;
}

/*
 * Get template file
 */
function affcoups_get_template_file( $template, $type = '' ) {

    $template_file = AFFCOUPS_DIR . 'templates/' . $template . '.php';

    $template_file = apply_filters( 'affcoups_template_file', $template_file, $template, $type );

    if ( file_exists( $template_file ) )
        return $template_file;

    return ( 'widget' === $type ) ? AFFCOUPS_DIR . 'templates/widget.php' : AFFCOUPS_DIR . 'templates/standard.php';
}

/**
 * Template loader
 *
 * @param $template
 */
function affcoups_get_template( $template, $wrap = false ) {

    // Get template file
    $file = affcoups_get_template_file( $template );

    if ( file_exists( $file ) ) {

        if ( $wrap )
            echo '<div class="affcoups">';

        include( $file );

        if ( $wrap )
            echo '</div>';

    } else {
        echo '<p>' . __('Template not found.', 'affiliate-coupons') . '</p>';
    }
}

/**
 * Check content if scripts must be loaded
 */
function affcoups_has_plugin_content() {

    global $post;

    if( ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'affcoups_coupons') || is_singular( 'affcoups_coupon' ) ) ) ) {
        return true;
    }

    return false;
}