<?php

namespace Perfect_Woocommerce_Brands;

defined('ABSPATH') or die('No script kiddies please!');

class PWB_Exporter_Support{

    function __construct(){
      add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_export_column' ) );
      add_filter( 'woocommerce_product_export_product_default_columns',  array( $this, 'add_export_column' ) );
      add_filter( 'woocommerce_product_export_product_column_pwb-brand', array( $this, 'add_export_data' ), 10, 2 );
    }

    /**
     * Add the custom column to the exporter and the exporter column menu.
     *
     * @param array $columns
     * @return array $columns
     */
    public function add_export_column( $columns ) {
    	$columns['pwb-brand'] = __('Brand', 'perfect-woocommerce-brands');
    	return $columns;
    }

    /**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    public function add_export_data( $value, $product ) {
      $brands = wp_get_post_terms( $product->get_id(), 'pwb-brand' );
      $brand_names = array();
      foreach( $brands as $brand ) $brand_names[] = $brand->name;
    	return implode( ',', $brand_names );
    }

}
