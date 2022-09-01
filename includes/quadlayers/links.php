<?php

namespace Perfect_Woocommerce_Brands\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class PWB_Admin_Links {

	protected static $_instance;

	function __construct() {
		add_filter( 'plugin_action_links_' . PWB_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
	}

	public function add_action_links( $links ) {
		$links[] = '<a target="_blank" href="' . PWB_DOCUMENTATION_URL . '">' . esc_html__( 'Documentation', 'perfect-woocommerce-brands' ) . '</a>';
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=pwb_admin_tab' ) ) . '">' . esc_html__( 'Settings', 'perfect-woocommerce-brands' ) . '</a>';
		return $links;
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}

PWB_Admin_Links::instance();
