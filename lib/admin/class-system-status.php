<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class System_Status {

	public function __construct() {
		add_action( 'wp_ajax_pwb_system_status', array( $this, 'system_status' ) );
	}

	public function system_status() {
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['nonce'] ) ), 'pwb_system_status' )
		&& current_user_can( 'manage_options' ) ) {

			print_r(
				array(
					'home_url'               => get_option( 'home' ),
					'site_url'               => get_option( 'siteurl' ),
					'version'                => WC()->version,
					'wp_version'             => get_bloginfo( 'version' ),
					'wp_multisite'           => is_multisite(),
					'wp_memory_limit'        => WP_MEMORY_LIMIT,
					'wp_debug_mode'          => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
					'wp_cron'                => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
					'language'               => get_locale(),
					'server_info'            => isset( $_SERVER['SERVER_SOFTWARE'] ) ? wc_clean( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : array(),
					'php_version'            => phpversion(),
					'php_post_max_size'      => ini_get( 'post_max_size' ),
					'php_max_execution_time' => ini_get( 'max_execution_time' ),
					'php_max_input_vars'     => ini_get( 'max_input_vars' ),
					'max_upload_size'        => wp_max_upload_size(),
					'default_timezone'       => date_default_timezone_get(),
					'theme'                  => $this->theme_info(),
					'active_plugins'         => get_option( 'active_plugins' ),
					'pwb_options'            => $this->pwb_options(),
				)
			);
		}

		wp_die();
	}

	private function theme_info() {
		$current_theme = wp_get_theme();
		return array(
			'name'         => $current_theme->__get( 'name' ),
			'version'      => $current_theme->__get( 'version' ),
			'parent_theme' => $current_theme->__get( 'parent_theme' ),
		);
	}

	private function pwb_options() {
		return array(
			'version'                                   => PWB_PLUGIN_VERSION,
			'wc_pwb_admin_tab_brand_single_position'    => get_option( 'wc_pwb_admin_tab_brand_single_position' ),
			'old_wc_pwb_admin_tab_slug'                 => get_option( 'old_wc_pwb_admin_tab_slug' ),
			'wc_pwb_notice_plugin_review'               => get_option( 'wc_pwb_notice_plugin_review' ),
			'wc_pwb_admin_tab_slug'                     => get_option( 'wc_pwb_admin_tab_slug' ),
			'wc_pwb_admin_tab_brand_desc'               => get_option( 'wc_pwb_admin_tab_brand_desc' ),
			'wc_pwb_admin_tab_brand_single_product_tab' => get_option( 'wc_pwb_admin_tab_brand_single_product_tab' ),
			'wc_pwb_admin_tab_brands_in_loop'           => get_option( 'wc_pwb_admin_tab_brands_in_loop' ),
			'wc_pwb_admin_tab_brands_in_single'         => get_option( 'wc_pwb_admin_tab_brands_in_single' ),
			'wc_pwb_admin_tab_brand_logo_size'          => get_option( 'wc_pwb_admin_tab_brand_logo_size' ),
		);
	}
}
