<?php
/**
 * List template
 *
 * @package AffiliateCoupons\Templates
 *
 * @var Affcoups_Coupon $coupon
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Check if coupons were found
if ( ! isset( $coupons ) ) {
    return;
}

if ( ! isset( $args ) ) {
    $args = array();
}

do_action( 'affcoups_template_begin', $coupons, $args ); ?>

<div class="affcoups-coupons-list">

    <?php if ( sizeof( $coupons ) > 0 ) {

        foreach( $coupons as $coupon ): ?>

            <div class="<?php $coupon->the_classes('affcoups-coupon' ); ?>"<?php $coupon->the_container(); ?>>

                <?php affcoups_tpl_the_coupon_discount( $coupon ); ?>

                <div class="affcoups-coupon__header">
                    <?php affcoups_tpl_the_coupon_image( $coupon ); ?>
                    <?php affcoups_tpl_the_coupon_types( $coupon ); ?>
                </div>

                <div class="affcoups-coupon__content">
                    <?php affcoups_tpl_the_coupon_title( $coupon ); ?>
                    <?php affcoups_tpl_the_coupon_description_with_excerpt( $coupon ); ?>
                </div>

                <div class="affcoups-coupon__footer">
                    <?php affcoups_tpl_the_coupon_code( $coupon ); ?>
                    <?php affcoups_tpl_the_coupon_button( $coupon ); ?>
                    <?php affcoups_tpl_the_coupon_valid_dates( $coupon ); ?>
                </div>

            </div>

        <?php endforeach;

    } else {
        esc_html_e( 'No coupons found.', 'affiliate-coupons-pro' );
    } ?>

</div>

<?php do_action( 'affcoups_after_template', $coupons, $args ); ?>