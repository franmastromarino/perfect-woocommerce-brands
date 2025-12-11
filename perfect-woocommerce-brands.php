<?php

/**
 * Plugin Name:             Perfect Brands WooCommerce
 * Plugin URI:              https://quadlayers.com/products/perfect-woocommerce-brands/
 * Description:             Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store.
 * Version:                 3.6.8
 * Text Domain:             perfect-woocommerce-brands
 * Author:                  QuadLayers
 * Author URI:              https://quadlayers.com
 * License:                 GPLv3
 * Domain Path:             /languages
 * Request at least:        4.7
 * Tested up to:            6.9
 * Requires PHP:            5.6
 * WC requires at least:    4.0
 * WC tested up to:         10.4
 */

defined( 'ABSPATH' ) || exit;

define( 'PWB_PLUGIN_FILE', __FILE__ );
define( 'PWB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'PWB_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'PWB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PWB_PLUGIN_VERSION', '3.6.8' );
define( 'PWB_PLUGIN_NAME', 'Perfect WooCommerce Brands' );
define( 'PWB_PREFIX', 'pwb' );
define( 'PWB_GROUP_URL', 'https://www.facebook.com/groups/quadlayers' );

/**
 * Load composer autoload.
 */
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Load composer packages.
 */
require_once __DIR__ . '/vendor_packages/wp-i18n-map.php';
require_once __DIR__ . '/vendor_packages/wp-dashboard-widget-news.php';
require_once __DIR__ . '/vendor_packages/wp-notice-plugin-required.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-table-links.php';
require_once __DIR__ . '/vendor_packages/wp-notice-plugin-promote.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-suggestions.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-feedback.php';
/**
 * Load plugin
 */
require_once __DIR__ . '/lib/class-plugin.php';

/**
 * Plugin activation hook
 */
register_activation_hook(
	__FILE__,
	function () {
		do_action( 'pwb_activation' );
		update_option( 'pwb_activate_on', time() );
	}
);

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(
	__FILE__,
	function () {
		do_action( 'pwb_deactivation' );
		/**
		 * Clean brands slug
		 */
		update_option( 'old_wc_pwb_admin_tab_slug', 'null' );
	}
);

/**
 * Declarate compatibility with WooCommerce Custom Order Tables
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
