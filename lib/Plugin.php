<?php

namespace QuadLayers\Perfect_Woocommerce_Brands;

class Plugin {

	protected static $instance;
	
	/**
	 * Plugin constructor.
	 */
	private function __construct() {

		/**
		 * Load plugin textdomain.
		 */
		add_action(
			'plugins_loaded',
			function () {
				load_plugin_textdomain( 'perfect-woocommerce-brands', false, PWB_PLUGIN_DIR . '/languages' );
			}
		);

		/**
		 * Load plugin files on WooCommerce init
		 */
		add_action(
			'woocommerce_init',
			function () {
				new Rest_Api();
				new Admin\Coupon();
				if ( is_admin() ) {
					new Admin\Admin_Tab();
					new Admin\System_Status();
					new Admin\Migrate();
					new Admin\Dummy_Data();
					new Admin\Edit_Brands_Page();
					new Admin\Brands_Custom_Fields();
					new Admin\Brands_Exporter();
					new Admin\Importer_Support();
					new Admin\Exporter_Support();
				} else {
					new Product();
				}
				new WooCommerce();
				do_action( 'pwb_init' );
			}
		);
		
	}

	public static function get_instance(){

		if( ! self::$instance || !self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Plugin::get_instance();
