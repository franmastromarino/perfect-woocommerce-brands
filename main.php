<?php
/**
 *  Plugin Name: Perfect WooCommerce Brands
 *  Plugin URI: https://wordpress.org/plugins/perfect-woocommerce-brands/
 *  Description: Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store.
 *  Version: 1.6.3
 *  Author: Alberto de Vera Sevilla
 *  Author URI: https://profiles.wordpress.org/titodevera/
 *  Text Domain: perfect-woocommerce-brands
 *  Domain Path: /lang
 *  License: GPL3
 *      Perfect WooCommerce Brands version 1.6.3, Copyright (C) 2018 Alberto de Vera Sevilla
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
 *  WC requires at least: 2.4
 *  WC tested up to: 3.3
 */

namespace Perfect_Woocommerce_Brands;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//plugin constants
define( 'PWB_PLUGIN', plugins_url( '', __FILE__ ) );
define( 'PWB_PLUGIN_PATH', plugin_basename( dirname( __FILE__ ) ) );
define( 'PWB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PWB_PLUGIN_VERSION', '1.6.3' );
define( 'PWB_WP_VERSION', get_bloginfo( 'version' ) );
define( 'PWB_WC_VERSION', get_option( 'woocommerce_version' ) );

register_activation_hook( __FILE__, function(){
  update_option( 'pwb_activate_on', time() );
} );

//clean brands slug on plugin deactivation
register_deactivation_hook( __FILE__, function(){
  update_option( 'old_wc_pwb_admin_tab_slug', 'null' );
} );

//loads textdomain for the translations
add_action( 'plugins_loaded', function(){
  load_plugin_textdomain( 'perfect-woocommerce-brands', false, PWB_PLUGIN_PATH . '/lang' );
} );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( is_plugin_active( 'woocommerce/woocommerce.php' ) ){

  require 'classes/widgets/class-pwb-dropdown.php';
  require 'classes/widgets/class-pwb-list.php';
  require 'classes/widgets/class-pwb-filter-by-brand.php';
  require 'classes/shortcodes/class-pwb-product-carousel.php';
  require 'classes/shortcodes/class-pwb-carousel.php';
  require 'classes/shortcodes/class-pwb-all-brands.php';
  require 'classes/shortcodes/class-pwb-brand.php';
  require 'classes/class-perfect-woocommerce-brands.php';

  if( defined('PWB_WC_VERSION') && version_compare( PWB_WC_VERSION, '2.6', '>=' ) ){
    require 'classes/class-pwb-api-support.php';
    new PWB_API_Support();
    require 'classes/admin/class-pwb-coupon.php';
    new Admin\PWB_Coupon();
  }

  if( is_admin() ){

    require 'classes/admin/class-pwb-system-status.php';
    new Admin\PWB_System_Status();
    require 'classes/admin/class-pwb-admin-tab.php';
    require 'classes/admin/class-pwb-migrate.php';
    new Admin\PWB_Migrate();
    require 'classes/admin/class-pwb-dummy-data.php';
    new Admin\PWB_Dummy_Data();
    require 'classes/admin/class-edit-brands-page.php';
    new Admin\Edit_Brands_Page();

    if( defined('PWB_WC_VERSION') && version_compare( PWB_WC_VERSION, '3.1.0', '>=' ) ){
      require 'classes/admin/class-pwb-importer-support.php';
      new PWB_Importer_Support();
      require 'classes/admin/class-pwb-exporter-support.php';
      new PWB_Exporter_Support();
    }

  }else{
    include_once 'classes/class-pwb-product-tab.php';
    new PWB_Product_Tab();
  }

  new \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands();

}elseif( is_admin() ){

  add_action( 'admin_notices', function() {
      $message = __( 'Perfect WooCommerce Brands needs WooCommerce to run. Please, install and active WooCommerce plugin.', 'perfect-woocommerce-brands' );
      printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', $message );
  });

}
