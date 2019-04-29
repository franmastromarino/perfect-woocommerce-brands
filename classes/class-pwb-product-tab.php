<?php

namespace Perfect_Woocommerce_Brands;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Product_Tab{

  function __construct(){
    add_filter( 'woocommerce_product_tabs', array( $this, 'product_tab' ) );
  }

  public function product_tab( $tabs ){

    global $product;

    if( isset( $product ) ){
      $brands = wp_get_object_terms( $product->get_id(), 'pwb-brand' );

      if( !empty( $brands ) ){
        $show_brand_tab = get_option( 'wc_pwb_admin_tab_brand_single_product_tab' );
        if( $show_brand_tab == 'yes' || !$show_brand_tab ){
          $tabs['pwb_tab'] = array(
            'title' 	  => __( 'Brand', 'perfect-woocommerce-brands' ),
            'priority' 	=> 20,
            'callback' 	=> array( $this, 'product_tab_content' )
          );
        }
      }
    }

  	return $tabs;

  }

  public function product_tab_content(){

    global $product;
    $brands = wp_get_object_terms( $product->get_id(), 'pwb-brand' );

    ob_start();
    ?>

    <h2><?php echo apply_filters( 'woocommerce_product_brand_heading', __('Brand', 'perfect-woocommerce-brands') ); ?></h2>
    <?php foreach( $brands as $brand ): ?>

      <?php
      $brand_logo = get_term_meta( $brand->term_id, 'pwb_brand_image', true );
      $brand_logo = wp_get_attachment_image( $brand_logo, 'thumbnail' );
      ?>

      <div id="tab-pwb_tab-content">
        <h3><?php echo $brand->name;?></h3>
        <?php if( !empty($brand->description) ) echo '<div>'.do_shortcode($brand->description).'</div>';?>
        <?php if( !empty($brand_logo) ) echo '<span>'.$brand_logo.'</span>';?>
      </div>

    <?php endforeach; ?>

    <?php
    echo ob_get_clean();

  }

}
