<?php

namespace Perfect_Woocommerce_Brands;
use WP_Error, WP_REST_Server;

defined('ABSPATH') or die('No script kiddies please!');

class PWB_API_Support{

    private $namespaces = array(
        "wc/v1",
        "wc/v2",
    );
    private $base = 'brands';

    function __construct(){
        add_action('rest_api_init', array($this, 'register_endpoints'));

        /**
         *  register_rest_field() was introduced in WordPress 4.7.0
         */
        if (version_compare(PWB_WP_VERSION, '4.7.0', '>=')) {
            add_action('rest_api_init', array($this, 'register_fields'));
        }

    }

    /**
     * Registers the endpoint for all possible $namespaces
     */
    public function register_endpoints(){
        foreach( $this->namespaces as $namespace ) {
            register_rest_route($namespace, '/'.$this->base, array(
              array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => function () {
                    return rest_ensure_response(
                        Perfect_Woocommerce_Brands::get_brands()
                    );
                }
              ),
              array(
                'methods'  => WP_REST_Server::CREATABLE,
                'callback'  => array( $this, 'create_brand' )
              ),
              array(
                'methods'   => WP_REST_Server::DELETABLE,
                'callback'  => array( $this, 'delete_brand' )
              )
            ));
        }
    }

    public function delete_brand( $request ){
      foreach( $request['brands'] as $brand ){
        $delete_result = wp_delete_term( $brand, 'pwb-brand' );
        if( is_wp_error( $delete_result ) ) return $delete_result;
      }
      return true;
    }

    public function create_brand( $request ){
      $new_brand = wp_insert_term( $request['name'], 'pwb-brand', array( 'slug' => $request['slug'] ) );
      if( !is_wp_error( $new_brand ) ){
        return true;
      }else{
        return $new_brand;
      }
    }

    /**
     * Entry point for all rest field settings
     */
    public function register_fields(){
        register_rest_field('product', 'brands', array(
            'get_callback'    => array($this, "get_callback"),
            'update_callback' => array($this, "update_callback"),
            'schema'          => $this->get_schema(),
        ));
    }

    /**
     * Returns the schema of the "brands" field on the /product route
     * To attach a brand to a product just append a "brands" key containing an array of brand id's
     * An empty array wil detach all brands.
     * @return array
     */
    public function get_schema(){
        return array(
            'description' => __('Product brands', 'perfect-woocommerce-brands'),
            'type' => 'array',
            'items' => array(
                "type" => "integer"
            ),
            'context' => array("view", "edit")
        );
    }

    /**
     * Returns all attached brands to a GET request to /products(/id)
     * @param $product
     * @return array|\WP_Error
     */
    public function get_callback($product){
        $brands = wp_get_post_terms($product['id'], 'pwb-brand');

        $result_brands_array = array();
        foreach ($brands as $brand) {
            $result_brands_array[] = array(
              'id'   => $brand->term_id,
              'name' => $brand->name,
              'slug' => $brand->slug
            );
        }

        return $result_brands_array;
    }

    /**
     * Entry point for an update call
     * @param $brands
     * @param $product
     */
    public function update_callback($brands, $product){
        $this->remove_brands($product);
        $this->add_brands($brands, $product);
    }


    /**
     * Detaches all brands from a product
     * @param \WC_Product $product
     */
    private function remove_brands($product){
        $brands = wp_get_post_terms($product->get_id(), 'pwb-brand');
        if (!empty($brands)) {
            wp_set_post_terms($product->get_id(), array(), 'pwb-brand');
        }
    }

    /**
     * Attaches the given brands to a product. Earlier attached brands, not in this array, will be removed
     * @param array $brands
     * @param \WC_Product $product
     */
    private function add_brands($brands, $product){
        wp_set_post_terms($product->get_id(), $brands, "pwb-brand");
    }

}
