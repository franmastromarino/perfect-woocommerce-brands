<?php
require_once 'wp-notice-plugin-promote/Load.php';

if ( class_exists( 'QuadLayers\\WP_Plugin_Table_Links\\Load' ) ) {
	new \QuadLayers\WP_Notice_Plugin_Promote\Load(
		PWB_PLUGIN_FILE,
		array(
			array(
				'type' => 'ranking',
				'logo' => plugins_url( '/assets/backend/img/logo.jpg', PWB_PLUGIN_FILE ),
				'url'  => PWB_REVIEW_URL,
			),
			array(
				'type' => 'promote',
				'logo' => plugins_url( '/assets/backend/img/logo.jpg', PWB_PLUGIN_FILE ),
				'slug'   => PWB_PREMIUM_SELL_SLUG,
				'name' => PWB_PREMIUM_SELL_NAME,
				'url'    => PWB_PREMIUM_SELL_URL
			),
			array(
				'type' => 'install',
				'slug'   => PWB_CROSS_INSTALL_SLUG,
				'name' => PWB_CROSS_INSTALL_NAME,
				'description'    => PWB_CROSS_INSTALL_DESCRIPTION,
				'url'    => PWB_CROSS_INSTALL_URL
			),
		),
	);
}
