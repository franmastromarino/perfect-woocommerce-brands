<?php

if ( class_exists( 'QuadLayers\\WP_Notice_Plugin_Promote\\Load' ) ) {
	/**
	 *  Promote constants
	 */
	define( 'PWB_PROMOTE_LOGO_SRC', plugins_url( '/assets/backend/img/logo.jpg', PWB_PLUGIN_FILE ) );
	/**
	 * Notice review
	 */
	define( 'PWB_PROMOTE_REVIEW_URL', 'https://wordpress.org/support/plugin/perfect-woocommerce-brands/reviews/?filter=5#new-post' );
	/**
	 * Notice premium sell
	 */
	define( 'PWB_PROMOTE_PREMIUM_SELL_SLUG', 'perfect-woocommerce-brands-pro' );
	define( 'PWB_PROMOTE_PREMIUM_SELL_NAME', 'Perfect WooCommerce Brands PRO' );
	define( 'PWB_PROMOTE_PREMIUM_INSTALL_URL', 'https://quadlayers.com/product/perfect-woocommerce-brands/?utm_source=pwb_admin' );
	define( 'PWB_PROMOTE_PREMIUM_SELL_URL', PWB_PREMIUM_SELL_URL );
	/**
	 * Notice cross sell 1
	 */
	define( 'PWB_PROMOTE_CROSS_INSTALL_1_SLUG', 'woocommerce-checkout-manager' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_1_NAME', 'WooCommerce Checkout Manager' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_1_DESCRIPTION', esc_html__( 'This plugin allows you to add custom fields to the checkout page, related to billing, shipping or additional fields sections.', 'perfect-woocommerce-brands' ) );
	define( 'PWB_PROMOTE_CROSS_INSTALL_1_URL', 'https://quadlayers.com/products/woocommerce-checkout-manager/?utm_source=pwb_admin' );
	/**
	 * Notice cross sell 2
	 */
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_SLUG', 'woocommerce-direct-checkout' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_NAME', 'WooCommerce Direct Checkout' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_DESCRIPTION', esc_html__( 'It allows you to reduce the steps in the checkout process by skipping the shopping cart page. This can encourage buyers to shop more and quickly. You will increase your sales reducing cart abandonment.', 'perfect-woocommerce-brands' ) );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_URL', 'https://quadlayers.com/products/woocommerce-direct-checkout/?utm_source=pwb_admin' );

	new \QuadLayers\WP_Notice_Plugin_Promote\Load(
		PWB_PLUGIN_FILE,
		array(
			array(
				'type'               => 'ranking',
				'notice_delay'       => MONTH_IN_SECONDS,
				'notice_logo'        => PWB_PROMOTE_LOGO_SRC,
				'notice_title'       => sprintf(
					esc_html__(
						'Hello! Thank you for choosing the %s plugin!',
						'perfect-woocommerce-brands'
					),
					PWB_PLUGIN_NAME
				),
				'notice_description' => esc_html__( 'Could you please give it a 5-star rating on WordPress? Your feedback boosts our motivation, helps us promote, and continues to improve this product. Your support matters!', 'perfect-woocommerce-brands' ),
				'notice_link'        => PWB_PROMOTE_REVIEW_URL,
				'notice_link_label'  => esc_html__(
					'Yes, of course!',
					'perfect-woocommerce-brands'
				),
				'notice_more_link'   => PWB_SUPPORT_URL,
				'notice_more_label'  => esc_html__(
					'Report a bug',
					'perfect-woocommerce-brands'
				),
			),
			array(
				'plugin_slug'        => PWB_PROMOTE_PREMIUM_SELL_SLUG,
				'plugin_install_link'   => PWB_PROMOTE_PREMIUM_INSTALL_URL,
				'plugin_install_label'  => esc_html__(
					'Purchase Now',
					'perfect-woocommerce-brands'
				),
				'notice_delay'       => MONTH_IN_SECONDS,
				'notice_logo'        => PWB_PROMOTE_LOGO_SRC,
				'notice_title'       => esc_html__(
					'Hello! We have a special gift!',
					'perfect-woocommerce-brands'
				),
				'notice_description' => sprintf(
					esc_html__(
						'Today we want to make you a special gift. Using the coupon code %1$s before the next 48 hours you can get a 20 percent discount on the premium version of the %2$s plugin.',
						'perfect-woocommerce-brands'
					),
					'ADMINPANEL20%',
					PWB_PROMOTE_PREMIUM_SELL_NAME
				),
				'notice_more_link'   => PWB_PROMOTE_PREMIUM_SELL_URL,
				'notice_more_label'  => esc_html__(
					'More info!',
					'perfect-woocommerce-brands'
				),
			),
			array(
				'plugin_slug'        => PWB_PROMOTE_CROSS_INSTALL_1_SLUG,
				'notice_delay'       => MONTH_IN_SECONDS * 4,
				'notice_logo'        => PWB_PROMOTE_LOGO_SRC,
				'notice_title'       => sprintf(
					esc_html__(
						'Hello! We want to invite you to try our %s plugin!',
						'perfect-woocommerce-brands'
					),
					PWB_PROMOTE_CROSS_INSTALL_1_NAME
				),
				'notice_description' => PWB_PROMOTE_CROSS_INSTALL_1_DESCRIPTION,
				'notice_more_link'   => PWB_PROMOTE_CROSS_INSTALL_1_URL,
				'notice_more_label'  => esc_html__(
					'More info!',
					'perfect-woocommerce-brands'
				),
			),
			array(
				'plugin_slug'        => PWB_PROMOTE_CROSS_INSTALL_2_SLUG,
				'notice_delay'       => MONTH_IN_SECONDS * 6,
				'notice_logo'        => PWB_PROMOTE_LOGO_SRC,
				'notice_title'       => sprintf(
					esc_html__(
						'Hello! We want to invite you to try our %s plugin!',
						'perfect-woocommerce-brands'
					),
					PWB_PROMOTE_CROSS_INSTALL_2_NAME
				),
				'notice_description' => PWB_PROMOTE_CROSS_INSTALL_2_DESCRIPTION,
				'notice_more_link'   => PWB_PROMOTE_CROSS_INSTALL_2_URL,
				'notice_more_label'  => esc_html__(
					'More info!',
					'perfect-woocommerce-brands'
				),
			),
		)
	);
}
