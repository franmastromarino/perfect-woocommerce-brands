<?php

namespace QuadLayers\PWB\Shortcodes;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Product_Carousel {


	private static $atts;

	public static function product_carousel_shortcode( $atts ) {
		self::$atts = shortcode_atts(
			array(
				'brand'              => 'all',
				'category'           => 'all',
				'products'           => '10',
				'products_to_show'   => '5',
				'products_to_scroll' => '1',
				'autoplay'           => 'false',
				'arrows'             => 'false',
			),
			$atts,
			'pwb-product-carousel'
		);

		/**
		 * Enqueue slick styles and scripts
		 */
		if ( ! wp_style_is( 'pwb-lib-slick' ) ) {
			wp_enqueue_style( 'pwb-lib-slick' );
		}

		if ( ! wp_script_is( 'pwb-lib-slick' ) ) {
			wp_enqueue_script( 'pwb-lib-slick' );
		}

		return \QuadLayers\PWB\WooCommerce::render_template(
			'product-carousel',
			'shortcodes',
			array(
				'slick_settings' => self::slick_settings(),
				'products'       => self::products_data(),
			),
			false
		);
	}

	private static function slick_settings() {
		$slick_settings = array(
			'slidesToShow'   => (int) self::$atts['products_to_show'],
			'slidesToScroll' => (int) self::$atts['products_to_scroll'],
			'autoplay'       => ( 'true' === self::$atts['autoplay'] ) ? true : false,
			'arrows'         => ( 'true' === self::$atts['arrows'] ) ? true : false,
		);

		return htmlspecialchars( json_encode( $slick_settings ), ENT_QUOTES, 'UTF-8' );
	}

	private static function products_data() {
		$products = array();

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => (int) self::$atts['products'],
			'paged'          => false,
		);

		if ( 'all' != self::$atts['brand'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'pwb-brand',
					'field'    => 'slug',
					'terms'    => self::$atts['brand'],
				),
			);
		}
		if ( 'all' != self::$atts['category'] ) {
			$woo_category_query = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => self::$atts['category'],
			);
			if ( isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
				$args['tax_query'][] = $woo_category_query;
			} else {
				$args['tax_query'] = array( $woo_category_query );
			}
		}

		$loop = new \WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) :
				$loop->the_post();
				$product = wc_get_product( get_the_ID() );

				$products[] = array(
					'id'        => get_the_ID(),
					'permalink' => get_the_permalink(),
					'thumbnail' => woocommerce_get_product_thumbnail(),
					'title'     => $product->get_title(),
				);
			endwhile;
		}
		wp_reset_postdata();

		return $products;
	}
}
