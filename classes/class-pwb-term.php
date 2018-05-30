<?php

namespace Perfect_Woocommerce_Brands;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Term{

  protected $term_obj, $id, $name, $link, $image, $banner, $banner_link, $desc, $slug;

  function __construct( $term_obj ){
    $this->term_obj = $term_obj;
    $this->is_wp_term();
  }

  private function is_wp_term(){
    if( is_a( $this->term_obj, 'WP_Term' ) ){
      $this->build_pwb_term( $this->term_obj );
    }else{
      throw new Exception( __('Only WP_Term objects are allowed', 'perfect-woocommerce-brands') );
    }
  }

  protected function build_pwb_term(){
    $this->id          = $this->term_obj->term_id;
    $this->name        = $this->term_obj->name;
    $this->slug        = $this->term_obj->slug;
    $this->desc        = get_term_meta( $this->id, 'pwb_brand_banner_description', true );
    $this->link        = get_term_link( $this->term_obj->term_id );
    $this->image       = htmlentities( wp_get_attachment_image( get_term_meta( $this->id, 'pwb_brand_image', true ), 'full' ) );
    $this->banner      = htmlentities( wp_get_attachment_image( get_term_meta( $this->id, 'pwb_brand_banner', true ), 'full' ) );
    $this->banner_link = get_term_meta( $this->id, 'pwb_brand_banner_link', true) ;
  }

  public function get( $key = 'id' ){
    return ( isset( $this->$key ) ) ? $this->$key : false;
  }

}
