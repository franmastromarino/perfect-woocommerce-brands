<?php
namespace Perfect_Woocommerce_Brands\Shortcodes;
use WP_Query;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_AZ_Listing_Shortcode {

  public static function shortcode( $atts ) {

    $grouped_brands = get_transient('pwb_az_listing_cache');

    if ( ! $grouped_brands ) {

      $atts = shortcode_atts( array(
        'only_parents' => false,
      ), $atts, 'pwb-az-listing' );

      $only_parents = filter_var( $atts['only_parents'], FILTER_VALIDATE_BOOLEAN );

      $brands         = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands( true, 'name', 'ASC', false, false, $only_parents );
      $grouped_brands = array();

      foreach ( $brands as $brand ) {

        if ( self::has_products( $brand->term_id ) ) {

          $letter = mb_substr( htmlspecialchars_decode( $brand->name ), 0, 1 );
          $letter = strtolower( $letter );
          $grouped_brands[$letter][] = [ 'brand_term' => $brand ];

        }

      }

      set_transient( 'pwb_az_listing_cache', $grouped_brands, 43200 );//12 hours

    }

    return \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
      'az-listing',
      'shortcodes',
      array( 'grouped_brands' => $grouped_brands ),
      false
    );

  }

  private static function has_products( $brand_id ){

    $args = array(
      'posts_per_page' => -1,
      'post_type'      => 'product',
      'tax_query'      => array(
        array(
          'taxonomy' => 'pwb-brand',
          'field'    => 'term_id',
          'terms'    => array( $brand_id )
        )
      ),
      'fields' => 'ids'
    );

    if( get_option('woocommerce_hide_out_of_stock_items') === 'yes' ){
      $args['meta_query'] = array(
        array(
          'key'     => '_stock_status',
          'value'   => 'outofstock',
          'compare' => 'NOT IN'
        )
      );
    }

    $wp_query = new WP_Query($args);
    wp_reset_postdata();
    return $wp_query->posts;

  }

}
