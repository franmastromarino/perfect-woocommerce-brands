<?php

/**
 *  Plugin Name: Perfect Brands for WooCommerce
 *  Plugin URI: https://quadlayers.com/portfolio/perfect-woocommerce-brands/
 *  Description: Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store.
 *  Version: 2.5.3
 *  Author: QuadLayers
 *  Author URI: https://quadlayers.com
 *  Text Domain: perfect-woocommerce-brands
 *  Domain Path: /lang
 *  License: GPLv3
 *      Perfect WooCommerce Brands version 1.9.0, Copyright (C) 2019 QuadLayers
 *      Perfect WooCommerce Brands is free software: you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation, either version 3 of the License, or
 *      (at your option) any later version.
 *
 *      Perfect WooCommerce Brands is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      You should have received a copy of the GNU General Public License
 *      along with Perfect WooCommerce Brands.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  WC requires at least: 3.1.0
 *  WC tested up to: 7.2
 */

namespace Perfect_Woocommerce_Brands;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

// plugin constants
define( 'PWB_PLUGIN_FILE', __FILE__ );
define( 'PWB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'PWB_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'PWB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PWB_PLUGIN_VERSION', '2.5.3' );
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

register_activation_hook(
	__FILE__,
	function () {
		update_option( 'pwb_activate_on', time() );
	}
);

// clean brands slug on plugin deactivation
register_deactivation_hook(
	__FILE__,
	function () {
		update_option( 'old_wc_pwb_admin_tab_slug', 'null' );
	}
);

// loads textdomain for the translations
add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'perfect-woocommerce-brands', false, PWB_PLUGIN_DIR . '/lang' );
	}
);

add_action(
	'woocommerce_init',
	function () {

		require_once 'classes/class-pwb-term.php';
		require_once 'classes/widgets/class-pwb-dropdown-widget.php';
		require_once 'classes/widgets/class-pwb-list-widget.php';
		require_once 'classes/widgets/class-pwb-filter-by-brand-widget.php';
		require_once 'classes/shortcodes/class-pwb-product-carousel.php';
		require_once 'classes/shortcodes/class-pwb-carousel.php';
		require_once 'classes/shortcodes/class-pwb-all-brands.php';
		require_once 'classes/shortcodes/class-pwb-az-listing.php';
		require_once 'classes/shortcodes/class-pwb-brand.php';

		require_once 'classes/class-pwb-api-support.php';
		new PWB_API_Support();
		require_once 'classes/admin/class-pwb-coupon.php';
		new Admin\PWB_Coupon();
		if ( is_admin() ) {
			require_once 'classes/admin/class-pwb-suggestions.php';
			new Admin\PWB_Suggestions();
			require_once 'classes/admin/class-pwb-system-status.php';
			new Admin\PWB_System_Status();
			require_once 'classes/admin/class-pwb-admin-tab.php';
			require_once 'classes/admin/class-pwb-migrate.php';
			new Admin\PWB_Migrate();
			require_once 'classes/admin/class-pwb-dummy-data.php';
			new Admin\PWB_Dummy_Data();
			require_once 'classes/admin/class-edit-brands-page.php';
			new Admin\Edit_Brands_Page();
			require_once 'classes/admin/class-brands-custom-fields.php';
			new Admin\Brands_Custom_Fields();
			require_once 'classes/admin/class-brands-exporter.php';
			new Admin\Brands_Exporter();
			require_once 'classes/admin/class-pwb-importer-support.php';
			new PWB_Importer_Support();
			require_once 'classes/admin/class-pwb-exporter-support.php';
			new PWB_Exporter_Support();
		} else {
			require_once 'classes/class-pwb-product-tab.php';
			new PWB_Product_Tab();
		}
		require_once 'classes/class-perfect-woocommerce-brands.php';
		new \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands();
		do_action( 'pwb_init' );
	}
);

require_once PWB_PLUGIN_DIR . 'includes/quadlayers/widget.php';
require_once PWB_PLUGIN_DIR . 'includes/quadlayers/notices.php';
require_once PWB_PLUGIN_DIR . 'includes/quadlayers/links.php';
