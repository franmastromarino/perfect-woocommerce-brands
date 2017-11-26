<?php

namespace Perfect_Woocommerce_Brands;

defined('ABSPATH') or die('No script kiddies please!');

class PWB_Importer_Support{

    function __construct(){
      add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_column_to_importer' ) );
      add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'add_column_to_mapping_screen' ) );
      add_filter( 'woocommerce_product_import_pre_insert_product_object', array( $this, 'process_import' ), 10, 2 );
    }

    /**
     * Register the 'Custom Column' column in the importer.
     *
     * @param array $options
     * @return array $options
     */
    public function add_column_to_importer( $options ) {
      $options['pwb-brand'] = __('Brand', 'perfect-woocommerce-brands');
    	return $options;
    }

    /**
     * Add automatic mapping support for 'Custom Column'.
     * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
     *
     * @param array $columns
     * @return array $columns
     */
    public function add_column_to_mapping_screen( $columns ) {
      $columns['pwb-brand'] = __('Brand', 'perfect-woocommerce-brands');
    	return $columns;
    }

    /**
     * Process the data read from the CSV file.
     * This just saves the value in meta data, but you can do anything you want here with the data.
     *
     * @param WC_Product $object - Product being imported or updated.
     * @param array $data - CSV data read for the product.
     * @return WC_Product $object
     */
    public function process_import( $object, $data ) {
      $brands = explode( ',', $data['pwb-brand'] );
      foreach( $brands as $brand ){
        $brand_id = term_exists( $brand, 'pwb-brand' );
        if( $brand_id !== 0 && $brand_id !== null ) {
          //brand exists
          wp_set_object_terms( $object->get_id(), $brand, 'pwb-brand' );
        }else{
          //insert new brand and assign it to the product
          $new_brand = wp_insert_term( $brand, 'pwb-brand' );
          if( !is_wp_error( $new_brand ) ) wp_set_object_terms( $object->get_id(), $new_brand->term_id, 'pwb-brand' );
        }
      }
    	return $object;
    }
    
}
