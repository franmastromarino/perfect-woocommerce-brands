<?php

if ( class_exists( 'QuadLayers\\WP_Plugin_Table_Links\\Load' ) ) {
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
				'url'  => PWB_PURCHASE_URL,
			),
			array(
				'place' => 'row_meta',
				'text'  => esc_html__( 'Support', 'perfect-woocommerce-brands' ),
				'url'   => PWB_SUPPORT_URL,
			),
			array(
				'place' => 'row_meta',
				'text'  => esc_html__( 'Documentation', 'perfect-woocommerce-brands' ),
				'url'   => PWB_DOCUMENTATION_URL,
			),
		)
	);
}
