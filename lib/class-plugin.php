<?php

namespace QuadLayers\PWB;

final class Plugin {

	protected static $instance;

	/**
	 * Plugin constructor.
	 */
	private function __construct() {

		/**
		 * Load plugin textdomain.
		 */
		load_plugin_textdomain( 'perfect-woocommerce-brands', false, PWB_PLUGIN_DIR . '/languages' );

		/**
		 * Load plugin files on woocommerce_init init
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

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
function INIT() {
	return Plugin::instance();
}

INIT();
