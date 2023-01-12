<?php

namespace QuadLayers\WP_Notice_Plugin_Required;

if ( class_exists( 'QuadLayers\\WP_Notice_Plugin_Required\\Load' ) ) {
	new \QuadLayers\WP_Notice_Plugin_Required\Load(
		PWB_PLUGIN_NAME,
		array(
			array(
				'slug' => 'woocommerce',
				'name' => 'WooCommerce',
			),
		)
	);
}
