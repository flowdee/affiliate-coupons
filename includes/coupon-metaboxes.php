<?php
/**
 * Coupon Meta Boxes
 *
 * @package     AffiliateCoupons\Coupons\Metaboxes
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Coupon Meta Boxes
 *
 * @param $meta_boxes
 * @return array|mixed
 */
function affcoups_register_coupon_meta_boxes( $meta_boxes ) {

	$fields = array(
		array(
			'name'        => esc_html__( 'Vendor', 'affiliate-coupons' ),
			'id'          => AFFCOUPS_PREFIX . 'coupon_vendor',
			'type'        => 'post',
			'post_type'   => AFFCOUPS_VENDOR_POST_TYPE,
			'field_type'  => 'select_advanced',
			'placeholder' => esc_html__( 'Please select...', 'affiliate-coupons' ),
			'query_args'  => array(
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			),
		),
		array(
			'name'             => esc_html__( 'Image', 'affiliate-coupons' ),
			'id'               => AFFCOUPS_PREFIX . 'coupon_image',
			'desc'             => __( 'By default the vendor image will be taken.', 'affiliate-coupons' ) . ' ' . sprintf( esc_html__( 'Recommended size: %1$d * %2$d px', 'affiliate-coupons' ), 480, 270 ),
			'type'             => 'image_advanced',
			'max_file_uploads' => 1,
		),
		array(
			'name'        => esc_html__( 'Discount', 'affiliate-coupons' ),
			'id'          => AFFCOUPS_PREFIX . 'coupon_discount',
			'type'        => 'text',
			'placeholder' => esc_html__( 'e.g. 50% OFF', 'affiliate-coupons' ),
		),
		array(
			'name'        => esc_html__( 'Discount Code', 'affiliate-coupons' ),
			'id'          => AFFCOUPS_PREFIX . 'coupon_code',
			'type'        => 'text',
			'placeholder' => esc_html__( 'e.g. SUMMERTIME50OFF', 'affiliate-coupons' ),
		),
		array(
			'name'       => esc_html__( 'Valid from', 'affiliate-coupons' ),
			'id'         => AFFCOUPS_PREFIX . 'coupon_valid_from',
			'type'       => 'datetime',
			'timestamp'  => true,
			// jQuery date picker options. See here http://api.jqueryui.com/datepicker
			'js_options' => array(
				'dateFormat'      => esc_html__( 'yy-mm-dd', 'affiliate-coupons' ),
				'changeMonth'     => true,
				'changeYear'      => true,
				'showButtonPanel' => true,
				'showTimepicker'  => true,
			),
		),
		array(
			'name'       => esc_html__( 'Valid until', 'affiliate-coupons' ),
			'id'         => AFFCOUPS_PREFIX . 'coupon_valid_until',
			'type'       => 'datetime',
			'timestamp'  => true,
			// jQuery date picker options. See here http://api.jqueryui.com/datepicker
			'js_options' => array(
				'dateFormat'      => esc_html__( 'yy-mm-dd', 'affiliate-coupons' ),
				'changeMonth'     => true,
				'changeYear'      => true,
				'showButtonPanel' => true,
				'showTimepicker'  => true,
                // Define default time
                'hour'            => 23,
                'minute'          => 59
			),
		),
		array(
			'name' => esc_html__( 'URL', 'affiliate-coupons' ),
			'id'   => AFFCOUPS_PREFIX . 'coupon_url',
			'desc' => esc_html__( 'By default the vendor url will be taken.', 'affiliate-coupons' ),
			'type' => 'url',
		),
		array(
			'name' => esc_html__( 'Title', 'affiliate-coupons' ),
			'id'   => AFFCOUPS_PREFIX . 'coupon_title',
			'type' => 'text',
			'desc' => esc_html__( 'By default the vendor title will be taken.', 'affiliate-coupons' ),
		),
		array(
			'name' => esc_html__( 'Description', 'affiliate-coupons' ),
			'id'   => AFFCOUPS_PREFIX . 'coupon_description',
			'type' => 'textarea',
			'desc' => esc_html__( 'By default the vendor description will be taken.', 'affiliate-coupons' ),
			'cols' => 20,
			'rows' => 3,
		),
	);

	$fields = apply_filters( 'affcoups_coupon_meta_box_details_fields', $fields );

	$meta_boxes[] = array(
		'id'         => AFFCOUPS_PREFIX . 'coupon_details',
		'title'      => __( 'Coupon: Details', 'affiliate-coupons' ),
		'post_types' => array( AFFCOUPS_COUPON_POST_TYPE ),
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => $fields, /*
        'validation' => array(
            'rules'    => array(
                AFFCOUPS_PREFIX . 'coupon_vendor' => array(
                    'required'  => true
                ),
            ),
            'messages' => array(
                AFFCOUPS_PREFIX . 'coupon_vendor' => array(
                    'required'  => esc_html__( 'Please select a vendor.', 'affiliate-coupons' )
                ),
            )
        )*/
	);

	$meta_boxes = apply_filters( 'affcoups_coupon_meta_boxes', $meta_boxes );

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'affcoups_register_coupon_meta_boxes' );