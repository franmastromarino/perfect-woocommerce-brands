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
    add_action( 'plugins_loaded', function(){ \Perfect_Woocommerce_Brands\Admin\Edit_Brands_Page::$current_user = wp_get_current_user(); } );
    add_action( 'after-pwb-brand-table', array( $this, 'add_brands_count' ) );
  }

  private static function is_edit_brands_page(){
    global $pagenow;
    return ( $pagenow == 'edit-tags.php' && isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == 'pwb-brand' ) ? true : false;
  }

  public function add_brands_count( $tax_name ){
    $brands = get_terms(
      $tax_name,
      array( 'hide_empty' => false )
    );
    $brands_featured = get_terms(
      $tax_name,
      array( 'hide_empty' => false, 'meta_query' => array( array( 'key' => 'pwb_featured_brand', 'value' => true ) ) )
    );

    echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
      'edit-brands-bottom',
      'admin',
      array( 'featured_count' => count( $brands_featured ), 'text_featured'  => __('featured', 'perfect-woocommerce-brands') )
    );

  }

  public function brand_list_admin_filter( $brands, $taxonomies, $args ) {

    if( self::is_edit_brands_page() ){

      $featured = get_user_option( 'pwb-first-featured-brands', self::$current_user->ID );
      if( $featured ){
        $featured_brands = array();
        $other_brands    = array();
        foreach( $brands as $brand ) {
          if( get_term_meta( $brand->term_id, 'pwb_featured_brand', true ) ){
            $featured_brands[] = $brand;
          }else{
            $other_brands[] = $brand;
          }
        }
        return array_merge( $featured_brands, $other_brands );
      }

    }
    return $brands;

  }

  public function brand_taxonomy_columns_head($defaults) {

      if( self::is_edit_brands_page() ){
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
      $direction = 'up';
      $brand = intval( $_POST['brand'] );
      if( $this->is_featured_brand( $brand ) ){
        update_term_meta( $brand, 'pwb_featured_brand', false );
        $direction = 'down';
      }else{
        update_term_meta( $brand, 'pwb_featured_brand', true );
      }
      wp_send_json_success( array( 'success' => true, 'direction' => $direction ) );
    }else{
      wp_send_json_error( array( 'success' => false, 'error_msg' => __( 'Error!','perfect-woocommerce-brands' ) ) );
    }
    wp_die();
  }

  public function add_screen_options( $status, $args ){
    if( self::is_edit_brands_page() ){
      $featured = get_user_option( 'pwb-first-featured-brands', self::$current_user->ID );
      ob_start();
      ?>
      <legend><?php _e('Brands','perfect-woocommerce-brands');?></legend>
      <label>
        <input id="pwb-first-featured-brands" type="checkbox" <?php checked($featured,true);?>>
        <?php _e('Show featured brands first','perfect-woocommerce-brands');?>
      </label>
      <?php
      return ob_get_clean();
    }
  }

  public function save_screen_options(){
    if( isset( $_POST['new_val'] ) ){
      $new_val = ( $_POST['new_val'] == 'true' ) ? true : false;
      update_user_option( self::$current_user->ID, 'pwb-first-featured-brands', $new_val );
    }
    wp_die();
  }

}
