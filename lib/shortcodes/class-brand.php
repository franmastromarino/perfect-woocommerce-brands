<?php

namespace QuadLayers\PWB\Shortcodes;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Brand {

	public static function brand_shortcode( $atts ) {

		$atts = shortcode_atts(
			array(
				'product_id'  => null,
				'as_link'     => false,
				'image_size'  => 'thumbnail',
				'description' => false,
			),
			$atts,
			'pwb-brand'
		);

		if ( ! $atts['product_id'] && is_singular( 'product' ) ) {
			$atts['product_id'] = get_the_ID();
		}

		$brands = wp_get_post_terms( $atts['product_id'], 'pwb-brand' );

		foreach ( $brands as $key => $brand ) {
			$brands[ $key ]->term_link = get_term_link( $brand->term_id, 'pwb-brand' );
			$brands[ $key ]->image     = wp_get_attachment_image( get_term_meta( $brand->term_id, 'pwb_brand_image', 1 ), $atts['image_size'] );
		}

		return \QuadLayers\PWB\WooCommerce::render_template(
			'brand',
			'shortcodes',
			array(
				'brands'      => $brands,
				'as_link'     => $atts['as_link'],
				'description' => $atts['description'],
			),
			false
		);

	}

}
