<?php
/**
 * Manage Coupons
 *
 * @package     AffiliateCoupons\Coupons\ManageCoupons
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add new columns
 *
 * @param $defaults
 *
 * @return mixed
 */
function affcoups_coupon_extend_columns( $defaults ) {

    /**
     *  @see affcoups_array_insert_after()
     */
    $defaults['affcoups_coupon_vendor'] = __( 'Vendor', 'affiliate-coupons' );

	$defaults['affcoups_coupon_thumb'] = __( 'Thumbnail', 'affiliate-coupons' );
    $defaults['affcoups_coupon_shortcode'] = __( 'Shortcode', 'affiliate-coupons' );

    return apply_filters( 'affcoups_coupon_posts_columns', $defaults );
}

add_filter( 'manage_affcoups_coupon_posts_columns', 'affcoups_coupon_extend_columns', 10 );

/**
 * Add columns content
 *
 * @param $column_name
 * @param $postid
 */
function affcoups_coupon_extend_columns_content( $column_name, $postid ) {

    if ( 'affcoups_coupon_vendor' === $column_name ) {

        // alternative approach for vendor id:
        $vendor_id = get_post_meta( $postid, AFFCOUPS_PREFIX . 'coupon_vendor', true );
        $Vendor = ( ! empty ( $vendor_id ) ) ? new Affcoups_Vendor( $vendor_id ) : null;

        if ( ! empty ( $Vendor ) && ! empty( $vendor_title = $Vendor->get_title() ) ) {
            echo '<a href="' . esc_url( admin_url( 'edit.php?s=' . esc_attr( $vendor_title ) . '&post_status=all&post_type=affcoups_coupon' ) ) . '">' . esc_html( $vendor_title ) . '</a>';
        } else {
            echo '&mdash;';
        }

    } elseif ( 'affcoups_coupon_thumb' === $column_name ) {

	    $Coupon = new Affcoups_Coupon( $postid );
		$image = $Coupon->get_image( 'small' );

		if ( ! empty ( $image['url'] ) ) {
			?>
            <img src="<?php echo esc_attr( $image['url'] ); ?>" alt="thumbnail" style="display: inline-block; max-width: 125px; height: auto;"/>
			<?php
		}

	} elseif ( 'affcoups_coupon_shortcode' === $column_name ) {
		?>
        <p>
            <input type='text' onClick="this.select();" value='[affcoups id="<?php echo esc_attr( $postid ); ?>"]' readonly='readonly' style="display: block; width: 100%;" />
        </p>
        <?php
	}

    do_action( 'affcoups_coupon_posts_columns_content', $column_name, $postid );
}
add_action( 'manage_affcoups_coupon_posts_custom_column', 'affcoups_coupon_extend_columns_content', 10, 2 );