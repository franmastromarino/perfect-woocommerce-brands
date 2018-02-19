<?php
namespace Perfect_Woocommerce_Brands\Shortcodes;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Product_Carousel_Shortcode{

  private static $atts;

  public static function product_carousel_shortcode( $atts ) {

    self::$atts = shortcode_atts( array(
        'brand'               => "all",
        'products'            => "10",
        'products_to_show'    => "5",
        'products_to_scroll'  => "1",
        'autoplay'            => "false",
        'arrows'              => "false"
    ), $atts, 'pwb-product-carousel' );

    return \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
      'product-carousel',
      'shortcodes',
      array( 'slick_settings' => self::slick_settings(), 'products' => self::products_data() )
    );

  }

  private static function slick_settings(){

    $slick_settings = array(
      'slidesToShow'   => (int)self::$atts['products_to_show'],
      'slidesToScroll' => (int)self::$atts['products_to_scroll'],
      'autoplay'       => ( self::$atts['autoplay'] === 'true' ) ? true: false,
      'arrows'         => ( self::$atts['arrows'] === 'true' ) ? true: false
    );
    return htmlspecialchars( json_encode( $slick_settings ), ENT_QUOTES, 'UTF-8' );

  }

  private static function products_data(){

    $products = array();

    $args = array(
      'post_type'      => 'product',
      'posts_per_page' => (int)self::$atts['products'],
      'paged'          => false
    );

    if( self::$atts['brand'] != 'all' ){
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'pwb-brand',
          'field'    => 'slug',
          'terms'    => self::$atts['brand']
        )
      );
    }

		$loop = new \WP_Query( $args );
		if( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
        $product = wc_get_product( get_the_ID() );

        $products[] = array(
          'id'          => get_the_ID(),
          'permalink'   => get_the_permalink(),
          'thumbnail'   => woocommerce_get_product_thumbnail(),
          'title'       => $product->get_title()
        );
			endwhile;
		}
		wp_reset_postdata();

    return $products;

  }

}
