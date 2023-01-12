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
define( 'PWB_REVIEW_URL', 'https://wordpress.org/support/plugin/perfect-woocommerce-brands/reviews/?filter=5#new-post' );
define( 'PWB_DEMO_URL', 'https://quadlayers.com/portfolio/perfect-woocommerce-brands/?utm_source=pwb_admin' );
define( 'PWB_PURCHASE_URL', PWB_DEMO_URL );
define( 'PWB_SUPPORT_URL', 'https://quadlayers.com/account/support/?utm_source=pwb_admin' );
define( 'PWB_DOCUMENTATION_URL', 'https://quadlayers.com/documentation/perfect-woocommerce-brands/?utm_source=pwb_admin' );
define( 'PWB_GITHUB_URL', 'https://github.com/quadlayers/perfect-woocommerce-brands/' );
define( 'PWB_GROUP_URL', 'https://www.facebook.com/groups/quadlayers' );

define( 'PWB_PREMIUM_SELL_SLUG', 'perfect-woocommerce-brands-pro' );
define( 'PWB_PREMIUM_SELL_NAME', 'Perfect WooCommerce Brands' );
define( 'PWB_PREMIUM_SELL_URL', 'https://quadlayers.com/portfolio/perfect-woocommerce-brands/?utm_source=pwb_admin' );

define( 'PWB_CROSS_INSTALL_SLUG', 'woocommerce-checkout-manager' );
define( 'PWB_CROSS_INSTALL_NAME', 'Checkout Manager' );
define( 'PWB_CROSS_INSTALL_DESCRIPTION', esc_html__( 'Checkout Field Manager( Checkout Manager ) for WooCommerce allows you to add custom fields to the checkout page, related to billing, Shipping or Additional fields sections.', 'perfect-woocommerce-brands' ) );
define( 'PWB_CROSS_INSTALL_URL', 'https://quadlayers.com/portfolio/woocommerce-checkout-manager/?utm_source=pwb_admin' );

/**
 * Load composer autoload.
 */
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Load composer packages.
 */
require_once __DIR__ . '/composer/wp-i18n-map.php';
require_once __DIR__ . '/composer/wp-dashboard-widget-news.php';
require_once __DIR__ . '/composer/wp-notice-plugin-required.php';
require_once __DIR__ . '/composer/wp-plugin-table-links.php';
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
