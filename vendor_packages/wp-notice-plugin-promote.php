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
	define( 'PWB_PROMOTE_CROSS_INSTALL_1_LOGO_SRC', plugins_url( '/assets/backend/img/woocommerce-checkout-manager.jpg', PWB_PLUGIN_FILE ) );
	/**
	 * Notice cross sell 2
	 */
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_SLUG', 'woocommerce-direct-checkout' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_NAME', 'WooCommerce Direct Checkout' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_DESCRIPTION', esc_html__( 'It allows you to reduce the steps in the checkout process by skipping the shopping cart page. This can encourage buyers to shop more and quickly. You will increase your sales reducing cart abandonment.', 'perfect-woocommerce-brands' ) );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_URL', 'https://quadlayers.com/products/woocommerce-direct-checkout/?utm_source=pwb_admin' );
	define( 'PWB_PROMOTE_CROSS_INSTALL_2_LOGO_SRC', plugins_url( '/assets/backend/img/woocommerce-direct-checkout.jpg', PWB_PLUGIN_FILE ) );

	new \QuadLayers\WP_Notice_Plugin_Promote\Load(
		PWB_PLUGIN_FILE,
		array(
			array(
				'type'               => 'ranking',
				'notice_delay'       => 0,
				'notice_logo'        => PWB_PROMOTE_LOGO_SRC,
				'notice_description' => sprintf(
								esc_html__( 'Hello! %2$s We\'ve spent countless hours developing this free plugin for you and would really appreciate it if you could drop us a quick rating. Your feedback is extremely valuable to us. %3$s It helps us to get better. Thanks for using %1$s.', 'perfect-woocommerce-brands' ),
								'<b>'.PWB_PLUGIN_NAME.'</b>',
								'<span style="font-size: 16px;">🙂</span>',
								'<br>'
				),
				'notice_link'        => PWB_PROMOTE_REVIEW_URL,
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
				'notice_delay'       => WEEK_IN_SECONDS,
				'notice_logo'        => PWB_PROMOTE_LOGO_SRC,
				'notice_title'       => esc_html__(
					'Hello! We have a special gift!',
					'perfect-woocommerce-brands'
				),
				'notice_description' => sprintf(
					esc_html__(
						'Today we have a special gift for you. Use the coupon code %1$s within the next 48 hours to receive a %2$s discount on the premium version of the %3$s plugin.',
						'perfect-woocommerce-brands'
					),
					'ADMINPANEL20%',
					'20%',
					PWB_PROMOTE_PREMIUM_SELL_NAME
				),
				'notice_more_link'   => PWB_PROMOTE_PREMIUM_SELL_URL,
			),
			array(
				'plugin_slug'        => PWB_PROMOTE_CROSS_INSTALL_1_SLUG,
				'notice_delay'       => MONTH_IN_SECONDS * 3,
				'notice_logo'        => PWB_PROMOTE_CROSS_INSTALL_1_LOGO_SRC,
				'notice_title'       => sprintf(
					esc_html__(
						'Hello! We want to invite you to try our %s plugin!',
						'perfect-woocommerce-brands'
					),
					PWB_PROMOTE_CROSS_INSTALL_1_NAME
				),
				'notice_description' => PWB_PROMOTE_CROSS_INSTALL_1_DESCRIPTION,
				'notice_more_link'   => PWB_PROMOTE_CROSS_INSTALL_1_URL
			),
			array(
				'plugin_slug'        => PWB_PROMOTE_CROSS_INSTALL_2_SLUG,
				'notice_delay'       => MONTH_IN_SECONDS * 6,
				'notice_logo'        => PWB_PROMOTE_CROSS_INSTALL_2_LOGO_SRC,
				'notice_title'       => sprintf(
					esc_html__(
						'Hello! We want to invite you to try our %s plugin!',
						'perfect-woocommerce-brands'
					),
					PWB_PROMOTE_CROSS_INSTALL_2_NAME
				),
				'notice_description' => PWB_PROMOTE_CROSS_INSTALL_2_DESCRIPTION,
				'notice_more_link'   => PWB_PROMOTE_CROSS_INSTALL_2_URL
			),
		)
	);
}
