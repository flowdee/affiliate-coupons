<?php
/**
 * Settings
 *
 * Source: https://codex.wordpress.org/Settings_API
 *
 * @package     AffiliateCoupons\Settings
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Affcoups_Settings' ) ) {

	class Affcoups_Settings {

        /**
         * Options
         *
         * @var array
         */
		public $options;

        /**
         * Active tab
         *
         * @var string
         */
		public $active_tab = '';

        /**
         * Affcoups_Settings constructor.
         */
		public function __construct() {
			// Options
			$this->options = affcoups_get_options();

			add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( &$this, 'init_settings' ) );

			add_action( 'affcoups_admin_header_after', array( &$this, 'render_settings_nav' ) );

			$this->init_active_tab();
		}

        /**
         * Add admin menu
         */
		function add_admin_menu() {

			add_submenu_page(
				'edit.php?post_type=affcoups_coupon',
				__( 'Settings', 'affiliate-coupons' ), // Page title
				__( 'Settings', 'affiliate-coupons' ), // Menu title
				'manage_options', // Capabilities
				'affcoups_settings', // Menu slug
				array( &$this, 'options_page' ) // Callback
			);
		}

        /**
         * Render page header
         */
        function render_settings_nav() {

            if ( ! affcoups_is_plugin_admin_area_settings() )
                return;
            ?>
            <div class="wrap">
                <div id="affcoups-admin-page">
                    <div class="affcoups-settings-nav nav-tab-wrapper">
                        <ul>
                            <?php foreach ( $this->get_registered_settings() as $section_key => $section ) : ?>
                                <li class="affcoups-settings-nav-item<?php if ( $section_key === $this->active_tab ) echo ' active'; ?>">
                                    <a href="#" data-affcoups-settings-nav="<?php echo  esc_html( $section_key ); ?>" class="nav-tab<?php if ( $section_key === $this->active_tab ) echo ' active'; ?>">
                                        <?php if ( ! empty( $section['icon'] ) ) { ?><span class="dashicons dashicons-<?php echo  esc_html( $section['icon'] ); ?>"></span><?php } ?>
                                        <?php echo esc_html( $section['title'] ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div><!-- /.affcoups-admin-page -->
            </div><!-- /.wrap -->
            <?php
        }

        /**
         * Init active settings tab
         */
        private function init_active_tab() {

		    if ( isset( $_GET['tab'] ) ) {
		        $active_tab = $_GET['tab'];
                //affcoups_debug_log( 'init_active_tab >> $_GET:  ' . $active_tab );
            } else {
                $active_tab = get_transient( 'affcoups_settings_active_tab' );
                //affcoups_debug_log( 'init_active_tab >> transient:  ' . $active_tab );
            }

            //affcoups_debug_log( $active_tab );

            $this->active_tab = ( ! empty( $active_tab ) ) ? $active_tab : 'quickstart';

            //affcoups_debug_log( $this->active_tab );
        }

        /**
         * Get registered settings
         *
         * @return array
         */
        function get_registered_settings() {

            $settings = array(
                /**
                 * Filters the default "Quickstart" settings.
                 *
                 * @param array $settings
                 */
                'quickstart' => apply_filters( 'affcoups_settings_quickstart',
                    array(
                        'icon' => 'welcome-learn-more',
                        'title' => __( 'Quickstart Guide', 'affiliate-coupons' ),
                        'callback' => array( &$this, 'section_quickstart_render' ),
                        'fields' => array()
                    )
                ),
                /**
                 * Filters the default "General" settings.
                 *
                 * @param array $settings
                 */
                'general' => apply_filters( 'affcoups_settings_general',
                    array(
                        'icon' => 'admin-generic',
                        'title' => __( 'General', 'affiliate-coupons' ),
                        'fields' => array(
                            'order' => array(
                                'title' => __( 'Sorting', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'order_render' )
                            ),
                            'search_filters' => array(
                                'title' => __( 'Coupon Search Filters', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'search_filters_render' )
                            ),
                            'pagination' => array(
                                'title' => __( 'Pagination', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'pagination_render' )
                            ),
                            'templates' => array(
                                'title' => __( 'Templates', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'templates_render' )
                            ),
                            'styles' => array(
                                'title' => __( 'Styles', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'styles_render' )
                            ),
                            'title_linked' => array(
                                'title' => __( 'Title', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'title_render' )
                            ),
                            'description' => array(
                                'title' => __( 'Description', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'description_render' )
                            ),
                            'discount' => array(
                                'title' => __( 'Discount', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'discount_render' )
                            ),
                            'codes' => array(
                                'title' => __( 'Codes', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'code_render' )
                            ),
                            'button' => array(
                                'title' => __( 'Button', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'button_render' )
                            ),
                            'clipboard' => array(
                                'title' => __( 'Clipboard', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'clipboard_render' )
                            ),
                            'dates' => array(
                                'title' => __( 'Dates', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'dates_render' )
                            ),
                            'credits' => array(
                                'title' => __( 'Credits', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'credits_render' )
                            ),
                            'custom_css' => array(
                                'title' => __( 'Custom CSS', 'affiliate-coupons' ),
                                'callback' => array( &$this, 'custom_css_render' )
                            )
                        )
                    )
                ),
                /**
                 * Filters the default "Help" settings.
                 *
                 * @param array $settings
                 */
                'help' => apply_filters( 'affcoups_settings_quickstart',
                    array(
                        'icon' => 'sos',
                        'title' => __( 'Help & Support', 'affiliate-coupons' ),
                        'callback' => array( &$this, 'section_help_render' ),
                        'fields' => array()
                    )
                ),
            );

            $settings = apply_filters( 'affcoups_settings', $settings );

            return $settings;
        }

        /**
         * Register settings
         */
		function init_settings() {

		    // Register setting
			register_setting(
				'affcoups_settings',
				'affcoups_settings',
				array( &$this, 'validate_input_callback' )
			);

			// Define settings page slug
            $settings_page = 'affcoups_settings';

            // Register sections
            foreach( $this->get_registered_settings() as $section_key => $section ) {

                add_settings_section(
                    'affcoups_settings_' . $section_key,
                    ( ! empty( $section['title'] ) ) ? $section['title'] : __return_null(),
                    ( isset( $section['callback'] ) ) ? $section['callback'] : '__return_false',
                    $settings_page
                );

                // Register fields
                if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {

                    foreach ( $section['fields'] as $field_key => $field ) {

                        add_settings_field(
                            'affcoups_settings_' . $field_key,
                            ( ! empty( $field['title'] ) ) ? $field['title'] : __return_null(),
                            ( isset( $field['callback'] ) ) ? $field['callback'] : '__return_false',
                            $settings_page,
                            'affcoups_settings_' . $section_key
                        );
                    }
                }
            }
		}

        /**
         * Validate settings input
         *
         * @param $input
         * @return mixed
         */
		function validate_input_callback( $input ) {

		    //affcoups_debug_log( $input );

		    // Handle Active Tab
            if ( ! empty( $input['active_tab'] ) ) {
                set_transient( 'affcoups_settings_active_tab', $input['active_tab'], 20 ); // Remember for 20 seconds only
                $input['active_tab'] = '';
            }

            // Handle Delete Log Action
            if ( isset ( $input['delete_log'] ) && '1' === $input['delete_log'] ) {
                delete_option( 'affcoups_log' );
                $input['delete_log'] = '0';
            }

            $input = apply_filters( 'affcoups_settings_validate_input', $input );

			return $input;
		}

        /**
         * Render quickstart section
         */
		function section_quickstart_render() {

			?>
            <h3 style="margin-top: 2em">
                <?php esc_html_e( 'First Steps', 'affiliate-coupons' ); ?>
            </h3>
            <ol>
                <li><?php esc_html_e( 'Create vendors', 'affiliate-coupons' ); ?></li>
                <li><?php esc_html_e( 'Create coupons', 'affiliate-coupons' ); ?></li>
                <li><?php esc_html_e( 'Link coupons to vendors', 'affiliate-coupons' ); ?></li>
                <li><?php esc_html_e( 'Assign categories and/or types to coupons if needed', 'affiliate-coupons' ); ?></li>
                <li><?php esc_html_e( 'Display coupons inside your posts/pages by using shortcodes', 'affiliate-coupons' ); ?></li>
            </ol>

            <hr style="margin: 3em 0;" />

            <h3>
                <?php esc_html_e( 'Available features', 'affiliate-coupons' ); ?>
            </h3>
            <ul style="margin-left: 1em">
                <li><a href="#show_all"><?php esc_html_e( 'Show all coupons', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#show_single"><?php esc_html_e( 'Show single coupons', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#filter"><?php esc_html_e( 'Filter coupons', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#sorting"><?php esc_html_e( 'Sorting coupons', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#search_filters"><?php esc_html_e( 'Frontend Search & Frontend Filter', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#pagination"><?php esc_html_e( 'Frontend Pagination', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#show_hide_invalid"><?php esc_html_e( 'Show/hide invalid coupons', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#show_hide_expired"><?php esc_html_e( 'Show/hide expired coupons', 'affiliate-coupons' ); ?></a></li>
                <li><a href="#templates"><?php esc_html_e( 'Templates', 'affiliate-coupons' ); ?></a></li>

                <?php do_action( 'affcoups_settings_section_quickstart_available_features' ); ?>

            </ul>

            <hr style="margin: 3em 0;" />

            <h3>
                <?php esc_html_e( 'Features', 'affiliate-coupons' ); ?>
            </h3>

            <p id="show_all" style="margin-top: 0; padding-top: 2.4em">
                <strong><?php esc_html_e( 'Show all coupons', 'affiliate-coupons' ); ?></strong>
            </p>
            <p>
                <code>[affcoups]</code> <?php esc_html_e( 'or', 'affiliate-coupons' ); ?> <code>[affcoups max="10"]</code>
            </p>

            <p id="show_single" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Show single coupons', 'affiliate-coupons' ); ?></strong>
            </p>
            <p>
                <code>[affcoups id="123"]</code> <?php esc_html_e( 'or', 'affiliate-coupons' ); ?> <code>[affcoups
                    id="123,456,789"]</code>
            </p>

            <p id="filter" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Filter coupons', 'affiliate-coupons' ); ?></strong><br/>
                <?php esc_html_e( 'By passing the id or slug (only if specified) you can filter the results individually.', 'affiliate-coupons' ); ?>
            </p>
            <ul>
                <li>
                    <?php esc_html_e( 'Filter by vendor:', 'affiliate-coupons' ); ?>
                    <code>[affcoups vendor="123"]</code>
                </li>
                <li>
                    <?php esc_html_e( 'Filter by category:', 'affiliate-coupons' ); ?>
                    <code>[affcoups category="123"]</code> <?php esc_html_e( 'or', 'affiliate-coupons' ); ?>
                    <code>[affcoups category="group-xyz"]</code>
                </li>
                <li>
                    <?php esc_html_e( 'Filter by type:', 'affiliate-coupons' ); ?>
                    <code>[affcoups type="123"]</code> <?php esc_html_e( 'or', 'affiliate-coupons' ); ?> <code>[affcoups
                        type="type-xyz"]</code>
                </li>
            </ul>
            <p>
                <small><?php esc_html_e( 'All filter options above can be combined inside the same shortcode.', 'affiliate-coupons' ); ?></small>
            </p>

            <p id="sorting" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Sorting coupons', 'affiliate-coupons' ); ?></strong>
            </p>
            <ul>
                <li>
                    <?php esc_html_e( 'Order:', 'affiliate-coupons' ); ?>
                    <code>[affcoups order="asc"]</code> (asc, desc)
                </li>
                <li>
                    <?php esc_html_e( 'Order by:', 'affiliate-coupons' ); ?>
                    <code>[affcoups orderby="title"]</code> (name, date, random, title, description, discount,
                    valid_from, valid_until)
                </li>
            </ul>

            <p id="search_filters" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Frontend Search & Frontend Filter', 'affiliate-coupons' ); ?></strong>
            </p>
            <p>
                <?php esc_html_e( 'You can easily add the frontend search bar and search filters on a shortcode basis as follows:', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups search_filters="true"]</code> <?php esc_html_e( 'or', 'affiliate-coupons' ); ?>
                <code>[affcoups search_filters="false"]</code>
            </p>
            <p>
                <small><?php esc_html_e( 'The shortcode ”search_filters” attribute has higher priority than “Coupon Search Filters” checkboxes at the ”General” settings tab.', 'affiliate-coupons' ); ?></small>
            </p>
            <p>
                <?php esc_html_e( 'You can also set global settings at the ”General” settings tab.', 'affiliate-coupons' ); ?>
            </p>

            <p id="pagination" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Frontend Pagination', 'affiliate-coupons' ); ?></strong>
            </p>
            <p>
                <?php esc_html_e( 'You can easily allow or forbid pagination on a shortcode basis as follows:', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups pagination="true"]</code> <?php esc_html_e( 'or', 'affiliate-coupons' ); ?>
                <code>[affcoups pagination="false"]</code>
            </p>
            <p>
                <small><?php esc_html_e( 'The shortcode ”pagination” attribute has higher priority than “Activate coupons pagination” checkbox at the ”General” settings tab.', 'affiliate-coupons' ); ?></small>
            </p>

            <p>
                <?php esc_html_e( 'You can easily set count of coupons per page on a shortcode basis as follows:', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups pagination_per_page="5"]</code>
            </p>
            <p>
                <small><?php esc_html_e( 'The shortcode ”pagination_per_page” attribute has higher priority than “Coupons per page” input field at the ”General” settings tab.', 'affiliate-coupons' ); ?></small>
            </p>

            <p>
                <?php esc_html_e( 'You can also set global settings at the ”General” settings tab.', 'affiliate-coupons' ); ?>
            </p>

            <p id="show_hide_invalid" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Show/hide invalid coupons', 'affiliate-coupons' ); ?></strong><br/>
                <?php esc_html_e( 'Contrary to the settings below you can show/hide coupons which are not valid yet for each shortcode individually.', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups hide_invalid="true"]</code> <?php esc_html_e( 'vs.', 'affiliate-coupons' ); ?>
                <code>[affcoups hide_invalid="false"]</code>
            </p>

            <p id="show_hide_expired" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Show/hide expired coupons', 'affiliate-coupons' ); ?></strong><br/>
                <?php esc_html_e( 'Contrary to the settings below you can show only the expired coupons or only the active ones for each shortcode individually.', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups expired="true"]</code> <?php esc_html_e( 'vs.', 'affiliate-coupons' ); ?>
                <code>[affcoups expired="false"]</code>
            </p>

            <p id="templates" style="padding-top: 2.4em">
                <strong><?php esc_html_e( 'Templates', 'affiliate-coupons' ); ?></strong><br/>
                <?php esc_html_e( 'You can easily select a template on a shortcode basis as follows:', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups template="standard"]</code>
            </p>
            <p>
                <?php esc_html_e( 'The following templates are available right now:', 'affiliate-coupons' ); ?>
                <code>standard</code>, <code>grid</code>, <code>list</code> & <code>widget</code>
            </p>
            <p>
                <?php esc_html_e( 'In order to display multiple coupons side by side, make use of the grid functionality:', 'affiliate-coupons' ); ?>
            </p>
            <p>
                <code>[affcoups grid="2"]</code>, <code>[affcoups grid="3"]</code> etc.
            </p>
            <p>
                <?php esc_html_e( 'When passing the grid size parameter, the plugin will automatically choose the grid template.', 'affiliate-coupons' ); ?>
            </p>

            <?php do_action( 'affcoups_settings_section_quickstart_render' ); ?>

            <hr style="margin: 3em 0;" />

            <p><?php
                printf( wp_kses( __( 'Please take a look into the <a href="%s" target="_blank">documentation</a> for more options.', 'affiliate-coupons' ), array(
                            'a' => array(
                                'href' => array(),
                                'target' => array()
                            )
                        )
                    ), esc_url( add_query_arg( array(
                        'utm_source'   => 'settings-page',
                        'utm_medium'   => 'quickstart',
                        'utm_campaign' => 'Affiliate Coupons',
                        ), 'https://affcoups.com/docs/' )
                    )
                );
            ?></p>
            <?php
		}

        /**
         * Render code settings
         */
        function code_render() {

            $code_options = array(
                '' => __( 'Show', 'affiliate-coupons' ),
                'hide' => __( 'Hide', 'affiliate-coupons' )
            );

            $code_options = apply_filters( 'affcoups_settings_code_options', $code_options );

            $code = ( isset( $this->options['code'] ) ) ? $this->options['code'] : '';
            ?>
            <p>
                <select id="affcoups_code" name="affcoups_settings[code]">
                    <?php foreach ( $code_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $code, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                    <?php } ?>
                </select>
            </p>
            <?php affcoups_the_pro_feature_note( __( 'Click to reveal', 'affiliate-coupons' ), true );

            do_action( 'affcoups_settings_click_to_reveal_button_text' );
        }

        /**
         * Render code settings
         */
		function clipboard_render() {

            $clipboard_icon_options = array(
                ''           => __( 'None', 'affiliate-coupons' ),
                'cut' => __( 'Cut', 'affiliate-coupons' ),
                'cut-white' => __( 'Cut (White)', 'affiliate-coupons' ),
                'copy' => __( 'Copy', 'affiliate-coupons' ),
                'copy-white' => __( 'Copy (White)', 'affiliate-coupons' ),
                'paste' => __( 'Paste', 'affiliate-coupons' ),
                'paste-white' => __( 'Paste (White)', 'affiliate-coupons' )
            );

            $clipboard_icon     = ( isset( $this->options['clipboard_icon'] ) ) ? $this->options['clipboard_icon'] : '';
            $clipboard_bg_color = ( isset( $this->options['clipboard_bg_color'] ) ) ? $this->options['clipboard_bg_color'] : '';
            $clipboard_color    = ( isset( $this->options['clipboard_color'] ) ) ? $this->options['clipboard_color'] : '';
		    ?>
            <h4><?php esc_html_e( 'Icon', 'affiliate-coupons' ); ?></h4>
            <p>
                <select id="affcoups_clipboard_icon" name="affcoups_settings[clipboard_icon]">
                    <?php foreach ( $clipboard_icon_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $clipboard_icon, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                    <?php } ?>
                </select>
            </p>
            <h4><?php esc_html_e( 'Background Color', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" class="affcoups-input-colorpicker" name="affcoups_settings[clipboard_bg_color]" value="<?php echo esc_attr( $clipboard_bg_color ); ?>"/>
            </p>
            <h4><?php esc_html_e( 'Text Color', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" class="affcoups-input-colorpicker" name="affcoups_settings[clipboard_color]" value="<?php echo esc_attr( $clipboard_color ); ?>"/>
            </p>
            <?php $this->the_color_picker_note(); ?>
            <?php
        }

        /**
         * Render date settings
         */
		function dates_render() {

            $hide_dates = ( isset( $this->options['hide_dates'] ) && $this->options['hide_dates'] == '1' ) ? 1 : 0;
            $hide_invalid_coupons = ( isset( $this->options['hide_invalid_coupons'] ) && $this->options['hide_invalid_coupons'] == '1' ) ? 1 : 0;
			?>
            <p>
                <input type="checkbox" id="affcoups_hide_dates" name="affcoups_settings[hide_dates]" value="1" <?php echo( $hide_dates == 1 ? 'checked' : '' ); ?> />
                <label for="affcoups_hide_dates"><?php esc_html_e( 'Hide coupon dates on the front end', 'affiliate-coupons' ); ?></label>
            </p>
            <p>
                <input type="checkbox" id="affcoups_hide_invalid_coupons" name="affcoups_settings[hide_invalid_coupons]" value="1" <?php echo( $hide_invalid_coupons == 1 ? 'checked' : '' ); ?> />
                <label for="affcoups_hide_invalid_coupons"><?php esc_html_e( 'Hide coupons which are not yet valid', 'affiliate-coupons' ); ?></label>
            </p>
			<?php
		}

        /**
         * Render order settings
         */
		function order_render() {

            $orderby_options = affcoups_get_orderby_options();
			$order_options = array(
				'asc'  => __( 'Ascending ', 'affiliate-coupons' ),
				'desc' => __( 'Descending', 'affiliate-coupons' )
			);

			$order = ( isset ( $this->options['order'] ) ) ? $this->options['order'] : 'desc';
			$orderby = ( isset ( $this->options['orderby'] ) ) ? $this->options['orderby'] : 'date';
			?>
            <!-- Order by -->
            <h4><?php esc_html_e( 'Order by', 'affiliate-coupons' ); ?></h4>
            <p>
                <select id="affcoups_orderby" name="affcoups_settings[orderby]">
					<?php foreach ( $orderby_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $orderby, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <!-- Order -->
            <h4><?php esc_html_e( 'Order', 'affiliate-coupons' ); ?></h4>
            <p>
                <select id="affcoups_order" name="affcoups_settings[order]">
                    <?php foreach ( $order_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $order, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                    <?php } ?>
                </select>
            </p>
			<?php
		}

        /**
         * Render templates settings
         */
		function templates_render() {

			$template_options = affcoups_get_template_options();
			$template = ( isset( $this->options['template'] ) ) ? $this->options['template'] : 'grid';

			$grid_size = ( ! empty( $this->options['grid_size'] ) && is_numeric( $this->options['grid_size'] ) ) ? intval( $this->options['grid_size'] ) : 3;
			?>
            <!-- Templates -->
            <h4><?php esc_html_e( 'Template', 'affiliate-coupons' ); ?></h4>
            <p>
                <select id="affcoups_template" name="affcoups_settings[template]">
					<?php foreach ( $template_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $template, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>
            <p class="desc">
                <?php esc_html_e( 'The default template which will be used for displaying coupons (widgets excepted).', 'affiliate-coupons' ); ?>
            </p>

            <!-- Grid -->
            <h4><?php esc_html_e( 'Grid size', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="number" name="affcoups_settings[grid_size]" id="affcoups_grid_size" value="<?php echo esc_attr( trim( $grid_size ) ); ?>"/>
            </p>
            <p class="desc">
                <?php esc_html_e( 'The default grid size which will be used for displaying coupons (widgets excepted).', 'affiliate-coupons' ); ?>
            </p>
			<?php
		}

        /**
         * Render styles settings
         */
        function styles_render() {

            $style_options = affcoups_get_style_options();
            $style = ( isset( $this->options['style'] ) ) ? $this->options['style'] : 'standard';
            ?>
            <!-- Styles -->
            <h4><?php esc_html_e( 'Style', 'affiliate-coupons' ); ?></h4>
            <p>
                <select id="affcoups_style" name="affcoups_settings[style]">
                    <?php foreach ( $style_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $style, $key ); ?>><?php echo esc_attr( $label ); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="desc">
                <?php esc_html_e( 'The default style which will be used for displaying coupons (widgets excepted).', 'affiliate-coupons' ); ?>
            </p>
            <?php affcoups_the_pro_feature_note( __( 'More styles', 'affiliate-coupons' ), false ); ?>
            <?php

            do_action( 'affcoups_settings_styles_render' );
        }

        /**
         * Coupon Search Filters settings
         */
        function search_filters_render() {

            ?>
            <!-- Coupon Search Filters -->

            <h4><?php esc_html_e( 'Coupon Search Filters', 'affiliate-coupons' ); ?></h4>

            <?php affcoups_the_pro_feature_note( __( 'Coupon Search Filters', 'affiliate-coupons' ), false );

            do_action('affcoups_settings_search_filters_render');
        }

        /**
         * Remove Pagination settings if PRO version was deactivated (e.g. on license expiration, etc.)
         */
        private function maybe_unset_pagination_settings() {

            if ( affcoups_is_pro_version() )
                return;

            $update = false;

            $pagination = array(
                'pagination',
                'pagination_per_page',
                'pagination_button_text'
            );

            foreach ( $pagination as $key ) {

                if ( isset( $this->options[$key] ) ) {

                    unset( $this->options[$key] );
                    $update = true;
                }
            }

            if ( $update ) {
                update_option( 'affcoups_settings', $this->options );
            }
        }

        /**
         * Pagination settings
         */
        function pagination_render() {

            // Early check the (PRO) Pagination settings
            $this->maybe_unset_pagination_settings();
            ?>
            <!-- Pagination -->

<?php // @TODO: ? Remove title from here ? ?>

            <h4><?php esc_html_e( 'Coupon Pagination', 'affiliate-coupons' ); ?></h4>

            <?php affcoups_the_pro_feature_note( __( 'Pagination', 'affiliate-coupons' ) );

            do_action('affcoups_settings_pagination_render');
        }

        /**
         * Render title settings
         */
		function title_render() {

            $title_linked = ( isset( $this->options['title_linked'] ) && $this->options['title_linked'] == '1' ) ? 1 : 0;
            ?>
            <p>
                <input type="checkbox" id="affcoups_title_linked" name="affcoups_settings[title_linked]" value="1" <?php echo( $title_linked == 1 ? 'checked' : '' ); ?>>
                <label for="affcoups_title_linked"><?php esc_html_e( 'Activate to redirect the page visitor to the corresponding URL when clicking on the coupon title', 'affiliate-coupons' ); ?></label>
            </p>
            <?php
        }

        /**
         * Render description settings
         */
		function description_render() {

			$excerpt_length = ( ! empty( $this->options['excerpt_length'] ) && is_numeric( $this->options['excerpt_length'] ) ) ? intval( $this->options['excerpt_length'] ) : 90;

			?>
            <h4><?php esc_html_e( 'Excerpt Length', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="number" name="affcoups_settings[excerpt_length]" id="affcoups_excerpt_length" value="<?php echo esc_attr( trim( $excerpt_length ) ); ?>"/>
            </p>
            <p class="desc">
				<?php esc_html_e( 'Some templates are displaying an excerpt instead of the whole coupon description.', 'affiliate-coupons' ); ?>
            </p>
			<?php
		}

        /**
         * Render discount settings
         */
		function discount_render() {

			$discount_bg_color = ( isset( $this->options['discount_bg_color'] ) ) ? $this->options['discount_bg_color'] : '';
			$discount_color    = ( isset( $this->options['discount_color'] ) ) ? $this->options['discount_color'] : '';

			?>
            <h4><?php esc_html_e( 'Background Color', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" class="affcoups-input-colorpicker" name="affcoups_settings[discount_bg_color]" value="<?php echo esc_attr( $discount_bg_color ); ?>"/>
            </p>
            <h4><?php esc_html_e( 'Text Color', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" class="affcoups-input-colorpicker" name="affcoups_settings[discount_color]" value="<?php echo esc_attr( $discount_color ); ?>"/>
            </p>
			<?php $this->the_color_picker_note(); ?>
			<?php
		}

        /**
         * Render button settings
         */
		function button_render() {

			$button_text = ( ! empty( $this->options['button_text'] ) ) ? esc_attr( trim( $this->options['button_text'] ) ) : __( 'Go to the deal', 'affiliate-coupons' );

			$button_icon_options = array(
				''           => __( 'None', 'affiliate-coupons' ),
                'thumbs-up' => __( 'Thumbs up', 'affiliate-coupons' ),
                'thumbs-up-white' => __( 'Thumbs up (White)', 'affiliate-coupons' ),
				'hand-right' => __( 'Hand right', 'affiliate-coupons' ),
                'hand-right-white' => __( 'Hand right (White)', 'affiliate-coupons' ),
				'gavel'      => __( 'Gavel', 'affiliate-coupons' ),
                'gavel-white'      => __( 'Gavel (White)', 'affiliate-coupons' ),
				'cart'       => __( 'Shopping cart', 'affiliate-coupons' ),
                'cart-white'       => __( 'Shopping cart (White)', 'affiliate-coupons' )
			);

			$button_icon     = ( isset( $this->options['button_icon'] ) ) ? $this->options['button_icon'] : '';
			$button_bg_color = ( isset( $this->options['button_bg_color'] ) ) ? $this->options['button_bg_color'] : '';
			$button_color    = ( isset( $this->options['button_color'] ) ) ? $this->options['button_color'] : '';
			?>
            <h4><?php esc_html_e( 'Text', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" name="affcoups_settings[button_text]" id="affcoups_button_text" value="<?php echo esc_attr( trim( $button_text ) ); ?>"/>
            </p>

            <h4><?php esc_html_e( 'Icon', 'affiliate-coupons' ); ?></h4>
            <p>
                <select id="affcoups_button_icon" name="affcoups_settings[button_icon]">
					<?php foreach ( $button_icon_options as $key => $label ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $button_icon, $key ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php } ?>
                </select>
            </p>

            <h4><?php esc_html_e( 'Background Color', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" class="affcoups-input-colorpicker" name="affcoups_settings[button_bg_color]" value="<?php echo esc_attr( $button_bg_color ); ?>"/>
            </p>
            <h4><?php esc_html_e( 'Text Color', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="text" class="affcoups-input-colorpicker" name="affcoups_settings[button_color]" value="<?php echo esc_attr( $button_color ); ?>"/>
            </p>
			<?php $this->the_color_picker_note(); ?>
			<?php
		}

        /**
         * Render credits settings
         */
        function credits_render() {

            $show_credits = ( isset( $this->options['show_credits'] ) && $this->options['show_credits'] == '1' ) ? 1 : 0;
            ?>
            <p>
                <input type="checkbox" id="affcoups_show_credits" name="affcoups_settings[show_credits]" value="1" <?php echo( $show_credits == 1 ? 'checked' : '' ); ?> />
                <label for="affcoups_show_credits"><?php esc_html_e( 'I like the plugin and I want to support you', 'affiliate-coupons' ); ?></label>
            </p>
            <p class="desc">
                <?php esc_html_e( 'By activating the checkbox a small note will be displayed right after the coupons. This will let your site visitors know, that you are using our plugin.', 'affiliate-coupons' ); ?>
            </p>
            <?php
        }

        /**
         * Render custom CSS settings
         */
		function custom_css_render() {

			$custom_css_activated = ( isset( $this->options['custom_css_activated'] ) && $this->options['custom_css_activated'] == '1' ) ? 1 : 0;
			$custom_css           = ( ! empty( $this->options['custom_css'] ) ) ? $this->options['custom_css'] : '';
			?>

            <p>
                <input type="checkbox" id="affcoups_custom_css_activated" name="affcoups_settings[custom_css_activated]" value="1" <?php echo( $custom_css_activated == 1 ? 'checked' : '' ); ?>>
                <label for="affcoups_custom_css_activated"><?php esc_html_e( 'Output custom CSS styles', 'affiliate-coupons' ); ?></label>
            </p>
            <br/>
            <textarea id="affcoups_custom_css" name="affcoups_settings[custom_css]" rows="10" cols="80" style="width: 100%;"><?php echo esc_attr( stripslashes( $custom_css ) ); ?></textarea>
            <p>
                <small><?php esc_html_e( "Please don't use the HTML style tag. Simply paste you CSS classes/definitions. For example:", 'affiliate-coupons' ) ?><br /><code>.affcoups .affcoups-coupon { background-color: #333; }</code></small>
            </p>

			<?php
		}

        /**
         * Output the color picker note
         */
		function the_color_picker_note() {
			?>
            <p class="desc">
				<?php esc_html_e( 'In case you want to change the colors, you must pick a new color for all fields above.', 'affiliate-coupons' ); ?>
            </p>
			<?php
		}

        /**
         * Section help render
         */
        function section_help_render() {

            global $wp_version;

            $curl = $this->check_curl();

            $enabled = '<span style="color: green;"><strong><span class="dashicons dashicons-yes"></span> ' . __('Enabled', 'affiliate-coupons') . '</strong></span>';
            $disabled = '<span style="color: red;"><strong><span class="dashicons dashicons-no"></span> ' . __('Disabled', 'affiliate-coupons') . '</strong></span>';

            $uninstall_on_delete = ( isset( $this->options['uninstall_on_delete'] ) && $this->options['uninstall_on_delete'] == '1' ) ? 1 : 0;
            ?>
            <p>
                <?php _e( 'Here you can find additional information which may help in case you experience some issue with our plugin.', 'affiliate-coupons' ); ?>
            </p>

            <table class="widefat affcoups-settings-table">
                <thead>
                <tr>
                    <th width="300"><?php _e('Setting', 'affiliate-coupons'); ?></th>
                    <th><?php _e('Values', 'affiliate-coupons'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>WordPress</th>
                    <td>Version <?php echo $wp_version; ?></td>
                </tr>
                <tr class="alternate">
                    <th>PHP</th>
                    <td>Version <strong><?php echo phpversion(); ?></strong></td>
                </tr>
                <tr>
                    <th><?php printf( esc_html__( 'PHP "%1$s" extension', 'affiliate-coupons' ), 'cURL' ); ?></th>
                    <td>
                        <?php echo (isset ($curl['enabled']) && $curl['enabled']) ? $enabled : $disabled; ?>
                        <?php if (isset ($curl['version'])) echo ' (Version ' . $curl['version'] . ')'; ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <p>
                <?php _e('In case one of the values above is <span style="color: red;"><strong>red</strong></span>, please get in contact with your webhoster in order to enable the missing PHP extensions.', 'affiliate-coupons'); ?>
            </p>

            <p>
                <strong><?php _e('Log file', 'affiliate-coupons'); ?></strong><br />
                <textarea rows="5" style="width: 100%;"><?php echo get_option( 'affcoups_log', __( 'No entries yet. ', 'affiliate-coupons' ) ); ?></textarea>
            </p>
            <p>
                <input type="hidden" id="affcoups-delete-log" name="affcoups_settings[delete_log]" value="0" />
                <?php submit_button( 'Delete log', 'delete button-secondary', 'affcoups-delete-log-submit', false ); ?>
            </p>

            <h4 style="margin-bottom: -1em;"><?php _e('Other', 'affiliate-coupons' ); ?></h4>
            <p>
                <input type="checkbox" id="affcoups_uninstall_on_delete" name="affcoups_settings[uninstall_on_delete]" value="1" <?php echo( $uninstall_on_delete == 1 ? 'checked' : '' ); ?>>
                <label for="affcoups_uninstall_on_delete"><?php esc_html_e( 'Remove all saved data for Affiliate Coupons when the plugin is deleted', 'affiliate-coupons' ); ?></label>
            </p>
            <?php
        }

        /**
         * Check cURL
         *
         * @return array|bool
         */
        private function check_curl() {

            if ( ( function_exists('curl_version') ) ) {

                $curl_data = curl_version();
                $version = ( isset ( $curl_data['version'] ) ) ? $curl_data['version'] : null;

                return array(
                    'enabled' => true,
                    'version' => $version
                );
            } else {
                return false;
            }
        }

        /**
         * Output the options page HTML
         */
		function options_page() {

			?>
            <div class="affcoups affcoups-page affcoups-settings">

                <div class="wrap">
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="meta-box-sortables ui-sortable">
                                    <form action="options.php" method="post">
                                        <input type="hidden" id="affcoups_settings_active_tab" name="affcoups_settings[active_tab]" value="<?php echo esc_html( $this->active_tab ); ?>" />
										<?php
										settings_fields( 'affcoups_settings' );
										$this->affcoups_do_settings_sections( 'affcoups_settings' );
										?>

                                        <p><?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?></p>
                                    </form>
                                </div>

                            </div>
                            <!-- /#post-body-content -->
                            <div id="postbox-container-1" class="postbox-container">
                                <div class="meta-box-sortables">
                                    <!-- Resources & Support -->
                                    <div class="card">
                                        <h3><span><span class="dashicons dashicons-star-filled"></span>&nbsp;<?php esc_html_e( 'Do You Enjoy our Plugin?', 'affiliate-coupons' ); ?></span></h3>
                                        <div class="inside">
                                            <p><?php _e( 'It would be great if you <strong>do us a big favor and give us a review</strong> for our plugin.', 'affiliate-coupons' ); ?></p>
                                            <p><?php esc_html_e( 'This will help us to make others aware of our plugin and we can continue to provide it with great features in long term.', 'affiliate-coupons' ); ?></p>
                                            <p>
                                                <a class="affcoups-settings-button affcoups-settings-button--secondary affcoups-settings-button--block" target="_blank" href="<?php echo esc_url( 'https://wordpress.org/support/plugin/affiliate-coupons/reviews/?filter=5#new-post' ); ?>" rel="nofollow"><?php _e('Submit a review', 'affiliate-coupons'); ?></a>
                                            </p>
                                        </div>
                                    </div>

                                    <?php if ( ! affcoups_is_pro_version() ) { ?>
                                        <div class="card">
                                            <h3><span><?php _e('Upgrade to PRO Version', 'affiliate-coupons' ); ?></span></h3>
                                            <div class="inside">

                                                <p><?php _e('The PRO version extends the plugin exclusively with a variety of different styles and some exclusively features.', 'affiliate-coupons'); ?></p>

                                                <ul>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Click to reveal discount codes', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('View, copy & click statistics', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Powerful & comprehensive search & filter function', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Comfortable pagination', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Choose from different styles', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Additional templates', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Feature & highlight single coupons', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Popular coupons widget', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Event Tracking via Google & Piwik', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('Create custom templates', 'affiliate-coupons'); ?></strong></li>
                                                    <li><span class="dashicons dashicons-star-filled"></span> <strong><?php _e('And more!', 'affiliate-coupons'); ?></strong></li>
                                                </ul>

                                                <p>
                                                    <?php _e('We would be happy if you give it a chance!', 'affiliate-coupons'); ?>
                                                </p>

                                                <p>
                                                    <?php $upgrade_link = affcoups_get_pro_version_url( 'features', 'settings-page', 'infobox-upgrade' ); ?>
                                                    <a class="affcoups-settings-button affcoups-settings-button--block" target="_blank" href="<?php echo $upgrade_link; ?>" rel="nofollow"><?php _e('Upgrade to Pro Version', 'affiliate-coupons'); ?></a>
                                                </p>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>
                                <!-- /.meta-box-sortables -->
                            </div>
                            <!-- /.postbox-container -->
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}

        /**
         * Custom settings section output
         *
         * Replacing: do_settings_sections('affcoups_settings');
         *
         * @param $page
         */
        function affcoups_do_settings_sections( $page ) {

            global $wp_settings_sections, $wp_settings_fields;

            if ( ! isset( $wp_settings_sections[ $page ] ) ) {
                return;
            }

            foreach ( (array) $wp_settings_sections[ $page ] as $section ) {

                $section_key = str_replace('affcoups_settings_', '', $section['id'] );

                $content_classes = 'affcoups-settings-content';

                if ( $section_key === $this->active_tab )
                    $content_classes .= ' active';

                echo '<div data-affcoups-settings-content="' . $section_key . '" class="' . $content_classes . '">';

                echo '<div class="card">';

                if ( $section['title'] ) {
                    echo "<h2 class='title'>{$section['title']}</h2>\n";
                }

                echo '<div class="inside">';

                if ( $section['callback'] ) {
                    call_user_func( $section['callback'], $section );
                }

                echo '<table class="form-table">';
                do_settings_fields( $page, $section['id'] );
                echo '</table>';

                echo '</div>'; // .inside
                echo '</div>'; // .card

                echo '</div>'; // .affcoups-settings-content
            }
        }
	}
}