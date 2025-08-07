<?php

if ( class_exists( 'QuadLayers\\WP_Plugin_Table_Links\\Load' ) ) {
	add_action('init', function() {
		new \QuadLayers\WP_Plugin_Table_Links\Load(
			PWB_PLUGIN_FILE,
			array(
				array(
					'text'   => esc_html__( 'Settings', 'perfect-woocommerce-brands' ),
					'url'    => admin_url( 'admin.php?page=wc-settings&tab=pwb_admin_tab' ),
					'target' => '_self',
				),
				array(
					'text' => esc_html__( 'Premium', 'perfect-woocommerce-brands' ),
					'url'  => 'https://quadlayers.com/products/perfect-woocommerce-brands/?utm_source=pwb_plugin&utm_medium=plugin_table&utm_campaign=premium_upgrade&utm_content=premium_link',
					'color' => 'green',
					'target' => '_blank',
				),
				array(
					'place' => 'row_meta',
					'text'  => esc_html__( 'Support', 'perfect-woocommerce-brands' ),
					'url'   => 'https://quadlayers.com/account/support/?utm_source=pwb_plugin&utm_medium=plugin_table&utm_campaign=support&utm_content=support_link',
				),
				array(
					'place' => 'row_meta',
					'text'  => esc_html__( 'Documentation', 'perfect-woocommerce-brands' ),
					'url'   => 'https://quadlayers.com/documentation/perfect-woocommerce-brands/?utm_source=pwb_plugin&utm_medium=plugin_table&utm_campaign=documentation&utm_content=documentation_link',
				),
			)
		);
	});

}
