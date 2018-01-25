<?php
namespace Perfect_Woocommerce_Brands\Admin;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Edit_Brands_Page {

  private static $current_user;

  function __construct(){
    add_filter( 'get_terms', array( $this, 'brand_list_admin_filter' ), 10, 3 );
    add_filter( 'manage_edit-pwb-brand_columns', array( $this, 'brand_taxonomy_columns_head' ) );
    add_filter( 'manage_pwb-brand_custom_column', array( $this, 'brand_taxonomy_columns' ), 10, 3 );
    add_action( 'wp_ajax_pwb_admin_set_featured_brand', array( $this, 'set_featured_brand' ) );
    add_filter( 'screen_settings', array( $this, 'add_screen_options' ), 10, 2 );
    add_action( 'wp_ajax_pwb_admin_save_screen_settings', array( $this, 'save_screen_options' ) );
    add_action( 'init', function(){ self::$current_user = wp_get_current_user(); } );
  }

  public function brand_list_admin_filter( $brands, $taxonomies, $args ) {

    global $pagenow;
    if( $pagenow == 'edit-tags.php' && isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == 'pwb-brand' ){

      $featured = get_user_option( 'pwb-only-featured-brands', self::$current_user->ID );

      if( $featured ){
        $filtered_brands = array();
        foreach( $brands as $brand ) {
          if( get_term_meta( $brand->term_id, 'pwb_featured_brand', true ) ) $filtered_brands[] = $brand;
        }
        return $filtered_brands;
      }

    }
    return $brands;

  }

  public function brand_taxonomy_columns_head($defaults) {

      global $pagenow;

      if( $pagenow == 'edit-tags.php' ){
        $defaults['featured'] = '';

        $newColumns = array(
            'cb'   => $defaults['cb'],
            'logo' => __( 'Logo', 'perfect-woocommerce-brands' )
        );

        unset( $defaults['description'] );
        unset( $defaults['cb'] );

        return array_merge( $newColumns, $defaults );
      }
      return $defaults;

  }

  public function brand_taxonomy_columns($c, $column_name, $term_id){
      switch( $column_name ){
        case 'logo':
          $image = wp_get_attachment_image( get_term_meta( $term_id, 'pwb_brand_image', 1 ), array('60','60') );
          return ( $image ) ? $image : "-";
          break;
        case 'featured':
          $featured_class = ( $this->is_featured_brand( $term_id ) ) ? 'dashicons-star-filled' : 'dashicons-star-empty';
          printf(
            '<span class="dashicons %1$s" title="%2$s" data-brand-id="%3$s"></span>',
            $featured_class, __('Set as featured', 'perfect-woocommerce-brands'), $term_id
          );
          break;
      }
  }

  private function is_featured_brand( $brand_id ){
    return ( get_term_meta( $brand_id, 'pwb_featured_brand', true ) );
  }

  public function set_featured_brand(){
    if( isset( $_POST['brand'] ) ){
      $brand = intval( $_POST['brand'] );
      if( $this->is_featured_brand( $brand ) ){
        update_term_meta( $brand, 'pwb_featured_brand', false );
      }else{
        update_term_meta( $brand, 'pwb_featured_brand', true );
      }
      wp_send_json_success( array( 'success' => true ) );
    }else{
      wp_send_json_error( array( 'success' => false ) );
    }
    wp_die();
  }

  public function add_screen_options( $status, $args ){
    $featured = get_user_option( 'pwb-only-featured-brands', self::$current_user->ID );
    ob_start();
    ?>
    <legend><?php _e('Brands','perfect-woocommerce-brands');?></legend>
    <label>
      <input id="pwb-only-featured-brands" type="checkbox" <?php checked($featured,true);?>>
      <?php _e('Show only featured brands','perfect-woocommerce-brands');?>
    </label>
    <?php
    return ob_get_clean();
  }

  public function save_screen_options(){
    if( isset( $_POST['new_val'] ) ){
      $new_val = ( $_POST['new_val'] == 'true' ) ? true : false;
      update_user_option( self::$current_user->ID, 'pwb-only-featured-brands', $new_val );
    }
    wp_die();
  }

}
