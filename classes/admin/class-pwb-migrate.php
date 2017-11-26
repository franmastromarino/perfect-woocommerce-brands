<?php
  namespace Perfect_Woocommerce_Brands\Admin;

  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  class PWB_Migrate {

    function __construct(){
      add_action( 'wp_ajax_pwb_admin_migrate_brands', array( $this, 'migrate_from' ) );
    }

    public function migrate_from(){

      if( isset( $_POST['from'] ) ){

        switch( $_POST['from'] ) {
          case 'yith':
            $this->migrate_from_yith();
            break;
          case 'ultimate':
            $this->migrate_from_ultimate();
            break;
	  case 'woobrands':
            $this->migrate_from_woobrands();
            break;
        }


      }

      wp_die();
    }

    public function migrate_from_yith(){

      global $wpdb;
      $terms = $wpdb->get_col( 'SELECT term_id FROM '.$wpdb->prefix.'term_taxonomy WHERE taxonomy LIKE "yith_product_brand"' );

      foreach( $terms as $term_id ) {

        //change taxonomy
        $wpdb->update(
          $wpdb->prefix . 'term_taxonomy',
          array(
            'taxonomy' => 'pwb-brand'
          ),
          array(
            'term_id' => $term_id
          )
        );

        //update term meta
        $wpdb->update(
          $wpdb->prefix . 'termmeta',
          array(
            'meta_key' => 'pwb_brand_image'
          ),
          array(
            'meta_key'         => 'thumbnail_id',
            'term_id'          => $term_id
          )
        );

      }

    }

    public function migrate_from_ultimate(){

      global $wpdb;
      $terms = $wpdb->get_col( 'SELECT term_id FROM '.$wpdb->prefix.'term_taxonomy WHERE taxonomy LIKE "product_brand"' );

      foreach( $terms as $term_id ) {

        //change taxonomy
        $wpdb->update(
          $wpdb->prefix . 'term_taxonomy',
          array(
            'taxonomy' => 'pwb-brand'
          ),
          array(
            'term_id' => $term_id
          )
        );

        /**
        *   Ultimate WooCommerce Brands uses tax-meta-class, tax meta are really options
        *   @link https://github.com/bainternet/Tax-Meta-Class
        */
        $term_meta = get_option('tax_meta_'.$term_id);
        if( isset( $term_meta['mgwb_image_brand_thumb']['id'] ) )
          add_term_meta( $term_id, 'pwb_brand_image', $term_meta['mgwb_image_brand_thumb']['id'] );

      }

    }

    public function migrate_from_woobrands(){

      global $wpdb;
      $terms = $wpdb->get_col( 'SELECT term_id FROM '.$wpdb->prefix.'term_taxonomy WHERE taxonomy LIKE "product_brand"' );

      foreach( $terms as $term_id ) {

        // change taxonomy
        $wpdb->update(
          $wpdb->prefix . 'term_taxonomy',
          array(
            'taxonomy' => 'pwb-brand'
          ),
          array(
            'term_id' => $term_id
          )
        );

      	// add the logo id
      	if( $thumb_id = get_woocommerce_term_meta( $term_id, 'thumbnail_id', true ) )
      		add_term_meta( $term_id, 'pwb_brand_image', $thumb_id );

      }

    }

  }
