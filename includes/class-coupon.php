<?php
/**
 * Coupon Class
 *
 * @package     AffiliateCoupons\CouponClass
 * @since       1.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Affcoups_Coupon' ) ) {

    class Affcoups_Coupon {

        /**
         * @var mixed
         */
        public $options;

        /**
         * @var WP_Post|int
         */
        public $post;

        /**
         * @var int
         */
        public $id;

        /**
         * @var mixed
         */
        public $vendor_id = 0;

        /**
         * @var Affcoups_Vendor
         */
        public $vendor;

        /**
         * Affcoups_Coupon constructor
         *
         * @param WP_Post|int Post or Post ID
         */
        public function __construct( $post ) {

            $this->options = affcoups_get_options();

            if ( is_numeric( $post ) )
                $post = get_post( $post );

            // Setup coupon
            if ( $post instanceof WP_Post ) {
                $this->post = $post;
                $this->id = $post->ID;
            }

            // Setup vendor
            $vendor_id = get_post_meta( $post->ID, AFFCOUPS_PREFIX . 'coupon_vendor', true );

            if ( ! empty( $vendor_id ) ) {
                $this->vendor_id = $vendor_id;
                $this->vendor = new Affcoups_Vendor( $vendor_id );
            }
        }

        /**
         * Get coupon id
         *
         * @return int
         */
        public function get_id() {
            return $this->id;
        }

        /**
         * Output coupon classes
         *
         * @param $classes
         */
        public function the_classes( $classes ) {

            $add_classes = apply_filters( 'affcoups_coupon_add_classes', array(), $this );

            // Maybe add extra classes
            if ( sizeof( $add_classes ) > 0 ) {
                foreach ( $add_classes as $class ) {
                    $classes .= ' affcoups-coupon--' . $class;
                }
            }

            // Output
            echo $classes;
        }

        /**
         * Output container attributes
         *
         * @param bool $echo
         * @return string
         */
        public function the_container( $echo = true ) {

            $output = '';

            $attributes = array();

            // HTML ID
            $output .= ' id="affcoups-coupon-' . $this->id . '"';

            // Coupon ID
            $attributes['coupon-id'] = $this->id;

            // Coupon Title
            $attributes['coupon-title'] = $this->get_post_title();

            // Add more via filter
            $attributes = apply_filters( 'affcoups_coupon_container_attributes', $attributes, $this );

            if ( sizeof( $attributes ) != 0 ) {

                foreach ( $attributes as $key => $value ) {

                    $value = strip_tags( $value );
                    $value = str_replace('"', "'", $value );

                    // Add attribute to output
                    if ( ! empty ( $value ) )
                        $output .= ' data-affcoups-' . $key . '="' . $value . '"';
                }
            }

            if ( ! $echo )
                return $output;

            if ( ! empty ( $output ) )
                echo $output;
        }

        /**
         * Get coupon image
         *
         * @param null $size
         *
         * @return bool|mixed
         */
        function get_image( $size = null ) {

            // Get thumbnail from coupon
            $image_size = ( 'small' === $size ) ? 'affcoups-thumb-small' : 'affcoups-thumb';

            if ( has_post_thumbnail( $this->id ) ) {

                $coupon_thumbnail_id = get_post_thumbnail_id( $this->id );
                $coupon_image_alt    = get_post_meta( $coupon_thumbnail_id, '_wp_attachment_image_alt', true );
                $coupon_image_url    = get_the_post_thumbnail_url( $this->id, $image_size );

                $coupon_feature_image = array(
                    'url' => $coupon_image_url,
                    'alt' => $coupon_image_alt
                );

                return $coupon_feature_image;

            } else {

                $coupon_images = rwmb_meta( AFFCOUPS_PREFIX . 'coupon_image', 'type=image&size=' . $image_size, $this->id );

                if ( ! empty ( $coupon_images ) && is_array( $coupon_images ) ) {
                    return array_shift( $coupon_images );

                // Get thumbnail from vendor
                } else {

                    $vendor_image = ( $this->vendor ) ? $this->vendor->get_image( $size ) : null;

                    if ( ! empty( $vendor_image ) )
                        return $vendor_image;
                }
            }

            // No image found
            return null;
        }

        /**
         * Display the coupon image
         */
        function the_image() {

            $image = $this->get_image();

            $coupon_url = $this->get_url();
            $coupon_title = $this->get_title();
            $coupon_title = affcoups_cleanup_html_attribute( $coupon_title );

            $image_args = array(
                'src' => ( ! empty ( $image['url'] ) ) ? $image['url'] : AFFCOUPS_PLUGIN_URL . 'assets/img/placeholder-thumb.png',
                'alt' => ( ! empty ( $image['alt'] ) ) ? $image['alt'] : $this->get_title(),
                'url' => ( ! empty( $coupon_url ) ) ? $coupon_url : '',
                'title' => ( ! empty( $coupon_title ) ) ? $coupon_title : '',
                'target' => '_blank',
                'rel' => 'nofollow'
            );

            $image_args = apply_filters( 'affcoups_the_image_args', $image_args, $this );

            // Build image
            $image_html = '<img class="affcoups-coupon__image" src="' . esc_html( $image_args['src'] ) . '" alt="' . esc_html( $image_args['alt'] ) . '" />';

            // Build thumbnail
            if ( ! empty( $image_args['url'] ) ) {

                $image_output = '<a class="affcoups-coupon__thumbnail" href="' . esc_url( $image_args['url'] ) . '" title="' . esc_html( $image_args['title'] ) . '" target="' . esc_html( $image_args['target'] ) . '" rel="' . esc_html( $image_args['rel'] ) . '">';

                $image_output .= $image_html;
                $image_output .= '</a>';
            } else {
                $image_output = '<span class="affcoups-coupon__thumbnail">';
                $image_output .= $image_html;
                $image_output .= '</span>';
            }

            // Output
            echo wp_kses_post( $image_output );
        }

        /**
         * Get coupon title
         *
         * @return mixed|string
         */
        function get_title() {

            // Coupon title
            $title = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_title', true );

            if ( ! empty ( $title ) )
                return $title;

            // Vendor title
            $vendor_title = ( $this->vendor ) ? $this->vendor->get_title() : null;

            if ( ! empty( $vendor_title ) )
                return $vendor_title;

            // Fallback: Coupon post title
            return get_the_title( $this->id );
        }

        /**
         * Get coupon post title
         */
        function get_post_title() {
            return get_the_title( $this->id );
        }

        /**
         * Get coupon url
         *
         * @return bool|mixed
         */
        function get_url() {

            // Coupon url
            $url = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_url', true );

            // Vendor url
            if ( empty( $url ) ) {
                $vendor_url = ( $this->vendor ) ? $this->vendor->get_url() : null;

                if ( ! empty ( $vendor_url ) )
                    $url = $vendor_url;
            }

            $url = apply_filters( 'affcoups_coupon_url', $url, $this );

            return $url;
        }

        /**
         * Get coupon description
         *
         * @return string
         */
        function get_description() {

            // Coupon description
            $description = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_description', true );

            if ( ! empty ( $description ) )
                return $description;

            // Vendor description
            $vendor_description = get_post_meta( $this->vendor_id, AFFCOUPS_PREFIX . 'vendor_description', true );

            if ( ! empty( $vendor_description ) )
                return $vendor_description;

            // Fallback: Coupon post content
            if ( ! isset( $this->post->post_content ) )
                return null;

            $post_content = apply_filters( 'affcoups_the_content', $this->post->post_content );
            $post_content = str_replace( ']]>', ']]&gt;', $post_content );

            return $post_content;
        }

        /**
         * Get coupon excerpt
         *
         * @return mixed
         */
        function get_excerpt() {

            $description = $this->get_description();

            $description = trim( $description );

            $excerpt_length = affcoups_get_option( 'excerpt_length', 90 );

            $excerpt = affcoups_truncate_string( $description, $excerpt_length );

            return $excerpt;
        }

        /**
         * Output the coupon excerpt
         */
        function the_excerpt() {

            $description = $this->get_description();
            $excerpt     = $this->get_excerpt();

            echo wp_kses_post( $excerpt );

            if ( $excerpt != $description ) {
                echo '<a href="#" class="affcoups-toggle-desc" data-affcoups-toggle-desc="true">' . __( 'Show More', 'affiliate-coupons' ) . '</a>';
            }
        }

        /**
         * Get coupon types
         *
         * @return array|bool|WP_Error
         */
        function get_types() {

            $term_list = wp_get_post_terms( $this->id, 'affcoups_coupon_type', array( "fields" => "all" ) );

            if ( sizeof( $term_list ) > 0 ) {
                return $term_list;
            }

            return null;
        }

        /**
         * Display coupon types
         */
        function the_types() {

            $types = '';

            $term_list = $this->get_types();

            if ( is_array( $term_list ) && sizeof( $term_list ) > 0 ) {

                foreach ( $term_list as $term_single ) {
                    echo '<span class="affcoups-type affcoups-type--' . esc_html( $term_single->slug ) . '">';
                    echo esc_attr( $term_single->name );
                    echo '</span>';
                }
            }

            echo esc_attr( $types );
        }

        /**
         * Get coupon discount
         *
         * @return bool|mixed
         */
        function get_discount() {

            $discount = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_discount', true );

            return ( ! empty ( $discount ) ) ? $discount : null;
        }

        /**
         * Check whether valid dates should be shown or not
         */
        function show_valid_dates() {

            if ( ! $this->has_valid_dates() )
                return false;

            // Check shortcode atts
            global $affcoups_template_args;

            if ( isset( $affcoups_template_args['hide_dates'] ) ) {

                if ( 'true' == $affcoups_template_args['hide_dates'] )
                    return false;

                if ( 'false' == $affcoups_template_args['hide_dates'] )
                    return true;
            }

            // Check settings
            if ( isset( $this->options['hide_dates'] ) && '1' == $this->options['hide_dates'] )
                return false;

            return true;
        }

        /**
         * Check if coupon has valid dates
         *
         * @return bool
         */
        function has_valid_dates() {

            $valid_from  = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_valid_from', true );
            $valid_until = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_valid_until', true );

            return ( ! empty ( $valid_from ) || ! empty ( $valid_until ) ) ? true : false;
        }

        /**
         * Display coupon valid dates
         */
        function the_valid_dates() {

            $date_format = get_option( 'date_format' );
            $date_format = apply_filters( 'affcoups_coupon_validation_date_format', $date_format );

            $dates = '';

            $valid_from  = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_valid_from', true );
            $valid_until = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_valid_until', true );

            if ( ! empty ( $valid_from ) && time() < $valid_from ) {
                $dates .= __( 'Valid from', 'affiliate-coupons' ) . ' ' . date_i18n( $date_format, $valid_from );
            }

            if ( ! empty ( $valid_until ) ) {
                $dates .= ( empty ( $dates ) ) ? __( 'Valid until', 'affiliate-coupons' ) : ' ' . __( 'until', 'affiliate-coupons' );
                $dates .= ' ' . date_i18n( $date_format, $valid_until );
            }

            echo esc_attr( $dates );
        }

        /**
         * Check whether code should be shown or not
         *
         * @return bool
         */
        function show_code() {

            if ( ! $this->get_code() )
                return false;

            if ( isset( $this->options['code'] ) && 'hide' === $this->options['code'] )
                return false;

            return apply_filters( 'affcoups_show_code', true, $this );
        }

        /**
         * Get coupon code
         *
         * @return bool|string
         */
        function get_code() {

            $code = get_post_meta( $this->id, AFFCOUPS_PREFIX . 'coupon_code', true );

            return ( ! empty ( $code ) ) ? $code : null;
        }

        /**
         * Output the code
         */
        function the_code() {
            $this->the_clipboard();
        }

        /**
         * Output the clipboard
         */
        function the_clipboard() {

            $code = $this->get_code();

            if ( empty( $code ) )
                return;

            $icon = ( ! empty( $this->options['clipboard_icon'] ) ) ? $this->options['clipboard_icon'] : '';

            $classes = 'affcoups-clipboard';

            if ( ! empty( $icon ) )
                $classes .= ' affcoups-clipboard--icon';
            ?>
            <div class="<?php echo esc_attr( $classes ); ?>" data-affcoups-coupon-id="<?php echo $this->get_id(); ?>" data-affcoups-clipboard="true" data-clipboard-text="<?php echo esc_attr( $code ); ?>" data-affcoups-clipboard-confirmation-text="<?php esc_attr_e( 'Copied!', 'affiliate-coupons' ); ?>">
                <?php if ( ! empty( $icon ) ) { ?>
                    <span class="affcoups-clipboard__icon affcoups-clipboard__icon--<?php echo esc_attr( $icon ); ?>"></span>
                <?php } ?>
                <span class="affcoups-clipboard__text"><?php echo esc_attr( $code ); ?></span>
            </div>
            <?php
        }

        /**
         * Display coupon button
         * @param array $args
         */
        function the_button( $args = array() ) {

            $default_text = ( ! empty( $this->options['button_text'] ) ) ? esc_html( $this->options['button_text'] ) : __( 'Go to the deal', 'affiliate-coupons' );

            $defaults = array(
                'url' => $this->get_url(),
                'text' => $default_text,
                'title' => $default_text,
                'target' => '_blank',
                'rel' => 'nofollow',
                'icon' => ( ! empty( $this->options['button_icon'] ) ) ? $this->options['button_icon'] : '',
                'text_forced' => false
            );

            $button = wp_parse_args( $args, $defaults );

            global $affcoups_shortcode_atts;

            // Button text
            if ( ! empty( $affcoups_shortcode_atts['button_text'] ) ) {
                $button['text'] = esc_html( $affcoups_shortcode_atts['button_text'] );
                $button['title'] = $button['text'];
            }

            $button['title'] = affcoups_cleanup_html_attribute( $button['title'] );

            // Hook
            $button = apply_filters( 'affcoups_button_args', $button, $this );

            // Build HTML markup
            ob_start();
            ?>
            <a class="affcoups-coupon__button" href="<?php echo esc_url( $button['url'] ); ?>" title="<?php echo esc_attr( $button['title'] ); ?>" rel="<?php echo esc_attr( $button['rel'] ); ?>" target="<?php echo esc_attr( $button['target'] ); ?>">
                <?php if ( ! empty( $button['icon'] ) ) { ?>
                    <span class="affcoups-icon-<?php echo esc_attr( $button['icon'] ); ?> affcoups-coupon__button-icon"></span>
                <?php } ?>

                <span class="affcoups-coupon__button-text"><?php echo esc_attr( $button['text'] ); ?></span>
            </a>
            <?php
            $button_html = ob_get_clean();

            // Maybe apply filters
            $button_html = apply_filters( 'affcoups_button_html', $button_html, $button, $this );

            // Output HTML
            echo $button_html;
        }
    }
}