<?php
/**
 * Maybe cleanup content in order to remove empty p and br tags for our shortcodes
 */
function affcoups_maybe_cleanup_shortcode_output( $content ) {

	// array of custom shortcodes requiring the fix
	$block = join( '|', array(
		'affcoups',
		'affcoups_coupons',
	) );

	// opening tag
	$rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );

	// closing tag
	$rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", '[/$2]', $rep );

	return $rep;
}

add_filter( 'affcoups_the_content', 'affcoups_maybe_cleanup_shortcode_output' );

/**
 * Basic shortcode
 *
 * @param $atts
 * @param $content
 *
 * @return string
 */
function affcoups_add_shortcode( $atts, $content ) {

    //affcoups_debug_log( '' );
    //affcoups_debug_log( '------- ADD SHORTCODE START -------' );

    //affcoups_debug_log( '$atts:' );
    //affcoups_debug_log( $atts );

    extract( shortcode_atts( apply_filters( 'affcoups_shortcode_default_atts', array(
		'id'           => null,
		'category'     => null,
		'type'         => null,
		'vendor'       => null,
		'max'          => null,
		'grid'         => null,
		'hide_dates'   => null,
        'hide_invalid' => null,
        'hide_expired' => null, // 'hide_expired' is deprecated: 'expired' is in action instead. Do not remove for backward compatibility
        'expired'      => null,
		'template'     => null,
        'style'        => null,
        'code'         => null,
		'order'        => null,
		'orderby'      => null,
        'is_widget'    => false
    ) ), $atts ) );

	global $affcoups_shortcode_atts;

	$affcoups_shortcode_atts = $atts;

	$is_widget = ( isset ( $atts['is_widget'] ) && 'true' == $atts['is_widget'] ) ? true : false;

	// Prepare options
	$options = affcoups_get_options();

	// Defaults
	$args = array(
		'posts_per_page' => - 1,
		'orderby'        => 'name',
		'order'          => 'ASC',
	);

	//-- Max
	$args['posts_per_page'] = ( ! empty( $max ) && is_numeric( $max ) ) ? intval( $max ) : '-1';
	$args['affcoups_max'] = ( ! empty( $max ) && is_numeric( $max ) ) ? intval( $max ) : 0;

	//-- Order
	if ( ! empty( $order ) ) {
		$args['affcoups_order'] = esc_html( $order );
	} else {
		$args['affcoups_order'] = ( ! empty( $options['order'] ) ) ? esc_html( $options['order'] ) : 'desc';
	}

	if ( ! empty( $orderby ) ) {
		$args['affcoups_orderby'] = esc_attr( $orderby );
	} else {
		$args['affcoups_orderby'] = ( ! empty( $options['orderby'] ) ) ? esc_html( $options['orderby'] ) : 'date';
	}

	//-- ID
	if ( ! empty( $id ) ) {
		$args['affcoups_coupon_id'] = esc_html( $id );
	}

	//-- Category
	if ( ! empty( $category ) ) {
		$args['affcoups_coupon_category'] = esc_html( $category );
	}

	//-- Type
	if ( ! empty( $type ) ) {
		$args['affcoups_coupon_type'] = esc_html( $type );
	}

	//-- Vendor
	if ( ! empty( $vendor ) ) {
		$args['affcoups_coupon_vendor'] = esc_html( $vendor );
	}

	//-- Expiration
    if ( ! empty( $hide_invalid ) ) {
        $args['affcoups_coupon_hide_invalid'] = ( 'true' === $hide_invalid ) ? true : false;
    } else {
        $args['affcoups_coupon_hide_invalid'] = ( isset( $options['hide_invalid_coupons'] ) && '1' === $options['hide_invalid_coupons'] ) ? true : false;
    }

	if ( ! is_null( $expired ) ) {

	    if ( 'true' === $expired ) {
            $args['affcoups_coupon_expired'] = 1;

        } elseif ( 'false' === $expired ) {
            $args['affcoups_coupon_expired'] = 0;
        }

    // Fallback for deprecated 'hide_expired' shortcode attr
    } elseif ( isset( $hide_expired ) && 'true' === $hide_expired ) {
        $args['affcoups_coupon_expired'] = 0;

    // 1. Fallback for deprecated $args['affcoups_coupon_hide_expired']
    // 2. 'hide_expired_coupons' setting is enhanced via radio buttons now: 'Show all coupons' / 'Show activate coupons only'
    } elseif ( isset( $options['hide_expired_coupons'] ) && '1' === $options['hide_expired_coupons'] ) {
        $args['affcoups_coupon_expired'] = 0;
    }

    $args = apply_filters( 'affcoups_shortcode_args', $args, $atts );

	//affcoups_debug( $args );

	//-- Get coupons
	$coupons = affcoups_get_coupons( $args );

	//affcoups_debug( $coupons );

	ob_start();

    if ( $coupons ) {

        //-- Apply filters
        $coupons = apply_filters( 'affcoups_coupons', $coupons, $args );

        //echo 'Coupons found: ' . $coupons->post_count . '<br>';

        if ( affcoups_is_amp() ) {
            $template = 'amp';
            $style = 'standard';

        } else {
            // Defaults
            $template_default  = ( ! empty( $options['template'] ) ) ? esc_html( $options['template'] ) : 'standard';
            $grid_size_default = ( ! empty( $options['grid_size'] ) && is_numeric( $options['grid_size'] ) ) ? esc_html( $options['grid_size'] ) : 2;
            $style_default  = ( ! empty( $options['style'] ) ) ? esc_html( $options['style'] ) : 'standard';

            // Collect template settings
            $template = ( ! empty( $template ) ) ? esc_html( $template ) : $template_default;
            $grid_size = $grid_size_default;
            $style = ( ! empty( $style ) ) ? esc_html( $style ) : $style_default;

            // Grid Layout?
            if ( ! empty( $grid ) && is_numeric( $grid ) ) {
                $template  = 'grid';
                $grid_size = $grid;
            }

            //echo '$template_default: ' .  $template_default . ' - $template: ' . $template . ' - $grid_size_default: ' . $grid_size_default . ' - $grid_size: ' . $grid_size . '<br>';
        }

        // Store template variables
        global $affcoups_template_args;

        $affcoups_template_args['template'] = $template;
        $affcoups_template_args['grid_size'] = ( ! empty( $grid_size ) ) ? $grid_size : 0;
        $affcoups_template_args['style'] = $style;

        if ( isset( $atts['hide_dates'] ) && in_array( $atts['hide_dates'], array( 'true', 'false' ) ) ) {
            $affcoups_template_args['hide_dates'] = $atts['hide_dates'];
        }

        if ( isset( $atts['float'] ) && in_array( $atts['float'], array( 'left', 'right' ) ) ) {
            $affcoups_template_args['float'] = $atts['float'];
        }

        if ( isset( $atts['style'] ) ) {
            $affcoups_template_args['style'] = $atts['style'];
        }

        if ( isset( $atts['code'] ) ) {
            $affcoups_template_args['code'] = $atts['code'];
        }

        //affcoups_debug( $affcoups_template_args, 'shortcode > $affcoups_template_args' );

        //echo 'Grid: ' . $grid . '<br>';
        //echo 'Template: ' . $template . '<br>';

        if ( ! affcoups_is_amp() ) {
            echo '<div class="affcoups" data-template="' . $template . '">';
        }

        // Get template file
        $file = affcoups_get_template_file( $template, 'coupons' );

        if ( file_exists( $file ) ) {

            if ( ! empty( $atts['search_filters'] ) || ! empty( $atts['pagination'] ) ) {
                $args = array_merge( $affcoups_template_args, $args );

            } else {
                $args['template'] = $template;
                $args['is_widget'] = $is_widget;

                // Grid
                if ( 'grid' == $template ) {
                    $args['grid_size'] = $affcoups_template_args['grid_size'];
                }
            }

            if ( affcoups_is_pro_version() ) {

                if ( $args['search_filters'] ) {
                    $filtered = apply_filters( 'affcoups_coupons_search_filters', $coupons, $args );
                    extract( $filtered );
                }

                if ( $args['pagination'] ) {
                    $filtered = apply_filters( 'affcoups_coupons_pagination', $coupons, $args );
                    extract( $filtered );
                }
            }

            include $file;

        } else {
            esc_html_e( 'Template not found.', 'affiliate-coupons' );
        }

        if ( ! affcoups_is_amp() ) {
            echo '</div><!-- /.affcoups -->';
        }

    } else {
        echo '<div class="affcoups">';
        esc_html_e( 'No coupons found.', 'affiliate-coupons' );
        echo '</div><!-- /.affcoups -->';
    }

	$output = ob_get_clean();

    $output = apply_filters( 'affcoups_shortcode_output', $output, $is_widget );

	// Remove unwanted line breaks from output
    $output = preg_replace( '/^\s+|\n|\r|\s+$/m', '', $output );

    //affcoups_debug_log( '' );
    //affcoups_debug_log( '------- ADD SHORTCODE END -------' );

    // Return output
	return $output;
}
add_shortcode( 'affcoups', 'affcoups_add_shortcode' );

/**
 * Fallback for old shortcode
 */
add_shortcode( 'affcoups_coupons', 'affcoups_add_shortcode' );

/*
 * Debug
 */
add_shortcode( 'affcoups_debug', function ( $atts ) {

	/*
	$args = array(
	    'affcoups_coupon_id' => array( 312, 314 )
	);

	$posts = affcoups_get_coupons( $args );

	affcoups_debug( $posts );
	*/

	//affcoups_the_coupon_thumbnail( 310 );
	//$image = rwmb_meta( AFFCOUPS_PREFIX . 'vendor_image', 'type=image&size=affcoups-thumb&limit=1', 278 );
	//affcoups_debug($image);

	$expiration = get_post_meta( 556, AFFCOUPS_PREFIX . 'coupon_valid_until', true );

	//var_dump( $expiration );

} );