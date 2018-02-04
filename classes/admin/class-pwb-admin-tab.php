<?php
    namespace Perfect_Woocommerce_Brands\Admin;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class Pwb_Admin_Tab {

        public static function init() {
            add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
            add_action( 'woocommerce_settings_tabs_pwb_admin_tab', __CLASS__ . '::settings_tab' );
            add_action( 'woocommerce_update_options_pwb_admin_tab', __CLASS__ . '::update_settings' );
        }

        /**
         * Add a new settings tab to the WooCommerce settings tabs array.
         *
         * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
         * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
         */
        public static function add_settings_tab( $settings_tabs ) {
            $settings_tabs['pwb_admin_tab'] = __( 'Brands', 'perfect-woocommerce-brands' );
            return $settings_tabs;
        }


        /**
         * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
         *
         * @uses woocommerce_admin_fields()
         * @uses self::get_settings()
         */
        public static function settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
            woocommerce_admin_fields( self::get_tools() );
        }


        /**
         * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
         *
         * @uses woocommerce_update_options()
         * @uses self::get_settings()
         */
        public static function update_settings() {
            update_option( 'old_wc_pwb_admin_tab_slug', get_taxonomy('pwb-brand')->rewrite['slug'] );

            if(isset($_POST['wc_pwb_admin_tab_slug'])){
                $_POST['wc_pwb_admin_tab_slug'] = sanitize_title($_POST['wc_pwb_admin_tab_slug']);
            }

            woocommerce_update_options( self::get_settings() );

        }


        /**
         * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
         *
         * @return array Array of settings for @see woocommerce_admin_fields() function.
         */
        public static function get_settings() {

            $available_image_sizes_adapted = array();
            $available_image_sizes = get_intermediate_image_sizes();

            foreach($available_image_sizes as $image_size){
                $available_image_sizes_adapted[$image_size] = $image_size;
            }
            $available_image_sizes_adapted['full'] = 'full';

            $settings = array(
                'section_title' => array(
                    'name'  => __( 'Brands settings', 'perfect-woocommerce-brands' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'wc_pwb_admin_tab_section_title'
                ),
                'slug' => array(
                    'name'        => __( 'Slug', 'perfect-woocommerce-brands' ),
                    'type'        => 'text',
                    'class'       => 'pwb-admin-tab-field',
                    'desc'        => __( 'Brands taxonomy slug', 'perfect-woocommerce-brands' ),
                    'id'          => 'wc_pwb_admin_tab_slug',
                    'placeholder' => get_taxonomy('pwb-brand')->rewrite['slug']
                ),
                'brand_description' => array(
                    'name'    => __( 'Show brand description', 'perfect-woocommerce-brands' ),
                    'type'    => 'checkbox',
                    'default' => 'yes',
                    'desc'    => __( 'Show brand description (if is set) on brand archive page', 'perfect-woocommerce-brands' ),
                    'id'      => 'wc_pwb_admin_tab_brand_desc'
                ),
                'brand_single_product_tab' => array(
                    'name'    => __( 'Single product tab', 'perfect-woocommerce-brands' ),
                    'type'    => 'checkbox',
                    'default' => 'yes',
                    'desc'    => __( 'Show brand tab in single product page', 'perfect-woocommerce-brands' ),
                    'id'      => 'wc_pwb_admin_tab_brand_single_product_tab'
                ),
                'show_brand_on_loop' => array(
                    'name'    => __( 'Show brands in loop', 'perfect-woocommerce-brands' ),
                    'type'    => 'select',
                    'class'  => 'pwb-admin-tab-field',
                    'desc'    => __( 'Show brand logo (or name) in product loop', 'perfect-woocommerce-brands' ),
                    'id'      => 'wc_pwb_admin_tab_brands_in_loop',
                    'options' => array(
                      'no'           => __( 'No', 'perfect-woocommerce-brands' ),
                      'brand_link'   => __( 'Show brand link', 'perfect-woocommerce-brands' ),
                      'brand_image'  => __( 'Show brand image (if is set)', 'perfect-woocommerce-brands' )
                    )
                ),
                'show_brand_in_single' => array(
                    'name'    => __( 'Show brands in single product', 'perfect-woocommerce-brands' ),
                    'type'    => 'select',
                    'class'   => 'pwb-admin-tab-field',
                    'desc'    => __( 'Show brand logo (or name) in single product', 'perfect-woocommerce-brands' ),
                    'default' => 'brand_image',
                    'id'      => 'wc_pwb_admin_tab_brands_in_single',
                    'options' => array(
                      'no'           => __( 'No', 'perfect-woocommerce-brands' ),
                      'brand_link'   => __( 'Show brand link', 'perfect-woocommerce-brands' ),
                      'brand_image'  => __( 'Show brand image (if is set)', 'perfect-woocommerce-brands' )
                    )
                ),
                'brand_logo_size' => array(
                    'name'    => __( 'Brand logo size', 'perfect-woocommerce-brands' ),
                    'type'    => 'select',
                    'class'  => 'pwb-admin-tab-field',
                    'desc'    => __( 'Brand logo size for single product view', 'perfect-woocommerce-brands' ),
                    'id'      => 'wc_pwb_admin_tab_brand_logo_size',
                    'options' => $available_image_sizes_adapted
                ),
                'brand_single_position' => array(
                    'name'    => __( 'Brand position', 'perfect-woocommerce-brands' ),
                    'type'    => 'select',
                    'class'  => 'pwb-admin-tab-field',
                    'desc'    => __( 'For single product', 'perfect-woocommerce-brands' ),
                    'id'      => 'wc_pwb_admin_tab_brand_single_position',
                    'options' => array(
                      'before_title'      => __( 'Before title', 'perfect-woocommerce-brands' ),
                      'after_title'       => __( 'After title', 'perfect-woocommerce-brands' ),
                      'after_price'       => __( 'After price', 'perfect-woocommerce-brands' ),
                      'after_excerpt'     => __( 'After excerpt', 'perfect-woocommerce-brands' ),
                      'after_add_to_cart' => __( 'After add to cart', 'perfect-woocommerce-brands' ),
                      'after_meta'        => __( 'After meta', 'perfect-woocommerce-brands' ),
                      'after_sharing'     => __( 'After sharing', 'perfect-woocommerce-brands' )
                    )
                ),
                'section_end' => array(
                     'type' => 'sectionend',
                     'id'   => 'wc_pwb_admin_tab_section_end'
                )
            );

            return apply_filters( 'wc_pwb_admin_tab_settings', $settings );

        }

        public static function get_tools() {

            $tools = array(
              'section_title' => array(
                  'name'  => __( 'Tools', 'perfect-woocommerce-brands' ),
                  'type'  => 'title',
                  'desc'  => '',
                  'id'    => 'wc_pwb_admin_tab_section_tools_title'
              ),
              'brand_import' => array(
                  'name'    => __( 'Import brands', 'perfect-woocommerce-brands' ),
                  'type'    => 'select',
                  'class'  => 'pwb-admin-tab-field',
                  'desc'    => sprintf(
                    __( 'Import brands from other brand plugin. <a href="%s" target="_blank">Click here for more details</a>', 'perfect-woocommerce-brands' ),
                    'https://github.com/titodevera/perfect-woocommerce-brands/wiki/How-to-import-brands-from-other-brands-plugin'
                  ),
                  'id'      => 'wc_pwb_admin_tab_tools_migrate',
                  'options' => array(
                    '-'         => __( '-', 'perfect-woocommerce-brands' ),
                    'yith'      => __( 'YITH WooCommerce Brands Add-On', 'perfect-woocommerce-brands' ),
                    'ultimate'  => __( 'Ultimate WooCommerce Brands', 'perfect-woocommerce-brands' ),
                    'woobrands'  => __( 'Offical WooCommerce Brands', 'perfect-woocommerce-brands' )
                  )
              ),
              'brand_dummy_data' => array(
                  'name'     => __( 'Dummy data', 'perfect-woocommerce-brands' ),
                  'type'     => 'select',
                  'class'    => 'pwb-admin-tab-field',
                  'desc'     => __( 'Import generic brands and assign it to products randomly', 'perfect-woocommerce-brands' ),
                  'id'       => 'wc_pwb_admin_tab_tools_dummy_data',
                  'options'  => array(
                    '-'              => __( '-', 'perfect-woocommerce-brands' ),
                    'start_import'   => __( 'Start import', 'perfect-woocommerce-brands' )
                  )
              ),
              'brands_system_status' => array(
                  'name'     => __( 'System status', 'perfect-woocommerce-brands' ),
                  'type'     => 'textarea',
                  'desc'     => __( 'Show system status', 'perfect-woocommerce-brands' ),
                  'id'       => 'wc_pwb_admin_tab_tools_system_status'
              ),
              'section_end' => array(
                   'type' => 'sectionend',
                   'id'   => 'wc_pwb_admin_tab_section_tools_end'
              )
            );

            return apply_filters( 'wc_pwb_admin_tab_tools_settings', $tools );

        }

    }

    Pwb_Admin_Tab::init();
