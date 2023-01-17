<?php

/**
 *  Plugin Name: Perfect Brands for WooCommerce
 *  Plugin URI: https://quadlayers.com/portfolio/perfect-woocommerce-brands/
 *  Description: Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store.
 *  Version: 3.0.0
 *  Author: QuadLayers
 *  Author URI: https://quadlayers.com
 *  Text Domain: perfect-woocommerce-brands
 *  Domain Path: /lang
 *  WC requires at least: 3.1.0
 *  WC tested up to: 7.2
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Plugin constants
 */
define( 'PWB_PLUGIN_FILE', __FILE__ );
define( 'PWB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'PWB_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'PWB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PWB_PLUGIN_VERSION', '3.0.0' );
define( 'PWB_PLUGIN_NAME', 'Perfect WooCommerce Brands' );
define( 'PWB_PREFIX', 'pwb' );
define( 'PWB_PURCHASE_URL', 'https://quadlayers.com/portfolio/perfect-woocommerce-brands/?utm_source=pwb_admin' );
define( 'PWB_SUPPORT_URL', 'https://quadlayers.com/account/support/?utm_source=pwb_admin' );
define( 'PWB_DOCUMENTATION_URL', 'https://quadlayers.com/documentation/perfect-woocommerce-brands/?utm_source=pwb_admin' );
define( 'PWB_GROUP_URL', 'https://www.facebook.com/groups/quadlayers' );
define( 'PWB_PREMIUM_SELL_URL', 'https://quadlayers.com/portfolio/perfect-woocommerce-brands/?utm_source=pwb_admin' );

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
require_once __DIR__ . '/vendor_packages/wp-notice-plugin-promote.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-table-links.php';
/**
 * Load plugin
 */
require_once __DIR__ . '/lib/class-plugin.php';
require_once __DIR__ . '/autoload-dev.php';

new QuadLayers\Autoload\Autoload( 'QuadLayers\\Perfect_Woocommerce_Brands', __DIR__.'/lib' );

/**
 * Plugin activation hook
 */
register_activation_hook(
	__FILE__,
	function () {
		update_option( 'pwb_activate_on', time() );
	}
);
/**
 * Plugin activation hook
 */
register_deactivation_hook(
	__FILE__,
	function () {
		/**
		 * Clean brands slug
		 */
		update_option( 'old_wc_pwb_admin_tab_slug', 'null' );
	}
);
