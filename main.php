<?php
/*
Plugin Name: Perfect WooCommerce Brands
Plugin URI: https://wordpress.org/plugins/perfect-woocommerce-brands/
Description: Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store.
Version: 1.4.2
Author: Alberto de Vera Sevilla
Author URI: https://profiles.wordpress.org/titodevera/
Text Domain: perfect-woocommerce-brands
Domain Path: /lang
License: GPL3

    Perfect WooCommerce Brands version 1.4.2, Copyright (C) 2016 Alberto de Vera Sevilla

    Perfect WooCommerce Brands is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Perfect WooCommerce Brands is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Perfect WooCommerce Brands.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Perfect_Woocommerce_Brands;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

register_deactivation_hook( __FILE__, function(){update_option( 'old_wc_pwb_admin_tab_slug', 'null' );} );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('woocommerce/woocommerce.php')){

    define('PWB_PLUGIN', plugins_url( '', __FILE__ ));
    define('PWB_PLUGIN_PATH', plugin_basename( dirname( __FILE__ ) ));
    define('PWB_PLUGIN_VERSION', '1.4.2');
    require 'classes/widgets/class-pwb-dropdown-widget.php';
    require 'classes/widgets/class-pwb-list-widget.php';
    require 'classes/shortcodes/class-pwb-product-carousel-shortcode.php';
    require 'classes/shortcodes/class-pwb-carousel-shortcode.php';
    require 'classes/shortcodes/class-pwb-all-brands-shortcode.php';
    require 'classes/shortcodes/class-pwb-brand-shortcode.php';
    require 'classes/class-perfect-woocommerce-brands.php';
    new \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands();
    require 'classes/class-pwb-admin-tab.php';

}elseif(is_admin()){

    add_action( 'admin_notices', function() {
        $message = __( 'Perfect WooCommerce Brands needs WooCommerce to run. Please, install and active WooCommerce plugin.', 'perfect-woocommerce-brands' );
        printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', $message );
    });

}
