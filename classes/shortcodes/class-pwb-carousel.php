<?php
namespace Perfect_Woocommerce_Brands\Shortcodes;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Carousel_Shortcode{

  private static $atts;

  public static function carousel_shortcode( $atts ) {

    self::$atts = shortcode_atts( array(
        'items'             => "10",
        'items_to_show'     => "5",
        'items_to_scroll'   => "1",
        'image_size'        => "thumbnail",
        'autoplay'          => "false",
        'arrows'            => "false",
        'hide_empty'        => false
    ), $atts, 'pwb-carousel' );

    //enqueue deps
    if( !wp_style_is('pwb-lib-slick') ) wp_enqueue_style('pwb-lib-slick');
    if( !wp_script_is('pwb-lib-slick') ) wp_enqueue_script('pwb-lib-slick');

    return \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
      'carousel',
      'shortcodes',
      array( 'slick_settings' => self::slick_settings(), 'brands' => self::brands_data() ),
      false
    );

  }

  private static function slick_settings(){

    $slick_settings = array(
      'slidesToShow'   => (int)self::$atts['items_to_show'],
      'slidesToScroll' => (int)self::$atts['items_to_scroll'],
      'autoplay'       => ( self::$atts['autoplay'] === 'true' ) ? true: false,
      'arrows'         => ( self::$atts['arrows'] === 'true' ) ? true: false
    );
    return htmlspecialchars( json_encode( $slick_settings ), ENT_QUOTES, 'UTF-8' );

  }

  private static function brands_data(){

    $brands = array();
    $foreach_i = 0;
    if( self::$atts['items'] == 'featured' ){
      $brands_array = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands( self::$atts['items'], 'name', 'ASC', true );
    }else{
      $brands_array = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands( self::$atts['items'] );
    }
    foreach( $brands_array as $brand ){
        if( self::$atts['items'] != 'featured' && $foreach_i >= (int)self::$atts['items'] ) break;

        $brand_id = $brand->term_id;
        $brand_link = get_term_link($brand_id);
        $attachment_id = get_term_meta( $brand_id, 'pwb_brand_image', 1 );
        $attachment_html = $brand->name;
        if($attachment_id!='') $attachment_html = wp_get_attachment_image( $attachment_id, self::$atts['image_size'] );

        $brands[] = array( 'link' => $brand_link, 'attachment_html' => $attachment_html, 'name' => $brand->name );

        $foreach_i++;
    }

    return $brands;

  }

}
