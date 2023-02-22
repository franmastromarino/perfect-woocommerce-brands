<?php

if ( class_exists( 'QuadLayers\\WP_Plugin_Suggestions\\Load' ) ) {
	new QuadLayers\WP_Plugin_Suggestions\Load(
		array(
			'exclude'          => array( 'perfect-woocommerce-brands' ),
			'parent_menu_slug' => 'wc-settings',
			'promote_links'    => array(
				array(
					'text'   => 'QuadLayers',
					'url'    => 'https://quadlayers.com',
					'target' => '_blank',
				),
				array(
					'text'   => 'Community',
					'url'    => 'https://www.facebook.com/groups/quadlayers',
					'target' => '_blank',
				),
			),
		)
	);
}
