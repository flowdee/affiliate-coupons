<?php
/**
 * Vendor Meta Boxes
 *
 * @package     AffiliateCoupons\Vendors\Metaboxes
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Vendor Meta Boxes
 *
 * @param $meta_boxes
 * @return array|mixed
 */
function affcoups_register_vendor_meta_boxes( $meta_boxes ) {

	$fields = array(
		array(
			'name'             => esc_html__( 'Image', 'affiliate-coupons' ),
			'id'               => AFFCOUPS_PREFIX . 'vendor_image',
			'type'             => 'image_advanced',
			'desc'             => sprintf( esc_html__( 'Recommended size: %1$d * %2$d px', 'affiliate-coupons' ), 480, 270 ),
			'max_file_uploads' => 1,
		),
		array(
			'name' => esc_html__( 'URL', 'affiliate-coupons' ),
			'id'   => AFFCOUPS_PREFIX . 'vendor_url',
			'desc' => esc_html__( 'This will be the default url of the selected vendor or its coupons.', 'affiliate-coupons' ),
			'type' => 'url',
		),
		array(
			'name' => esc_html__( 'Description', 'affiliate-coupons' ),
			'id'   => AFFCOUPS_PREFIX . 'vendor_description',
			'type' => 'textarea',
			'cols' => 20,
			'rows' => 3,
		),
	);

	$fields = apply_filters( 'affcoups_vendor_meta_box_details_fields', $fields );

	$meta_boxes[] = array(
		'id'         => AFFCOUPS_PREFIX . 'vendor_details',
		'title'      => __( 'Vendor: Details', 'affiliate-coupons' ),
		'post_types' => array( AFFCOUPS_VENDOR_POST_TYPE ),
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => $fields,
	);

	$meta_boxes = apply_filters( 'affcoups_vendor_meta_boxes', $meta_boxes );

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'affcoups_register_vendor_meta_boxes' );