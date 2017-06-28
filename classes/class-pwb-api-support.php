<?php
  namespace Perfect_Woocommerce_Brands;

  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  class PWB_API_Support{

    function __construct(){
      add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );

      /**
      *  register_rest_field() was introduced in WordPress 4.7.0
      */
      if( version_compare( PWB_WP_VERSION, '4.7.0', '>=' ) ){
        add_action( 'rest_api_init', array( $this, 'register_fields' ) );
      }

    }

    public function register_endpoints(){

      register_rest_route( 'wc/v1', '/brands', array(
          // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
          'methods'  => \WP_REST_Server::READABLE,
          'callback' => function(){
            return rest_ensure_response(
              \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands()
            );
          }
      ) );

    }

    public function register_fields(){

      register_rest_field( 'product', 'brands', array(
          'get_callback' => function( $product ) {
              $result_brands_array = array();
              $brands = wp_get_post_terms($product['id'], 'pwb-brand' );
              foreach($brands as $brand) {
                $result_brands_array[$brand->term_id] = $brand->name;
              }
              return $result_brands_array;
          },
          'schema' => array(
              'description' => __( 'Product brands' , 'perfect-woocommerce-brands' ),
              'type'        => 'text'
          )
      ) );

    }

  }
