<?php
  namespace Perfect_Woocommerce_Brands\Admin;

  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  class PWB_Coupon{

    function __construct(){
      add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'coupon_restriction' ) );
      add_action( 'woocommerce_coupon_options_save',  array( $this, 'coupon_save' ) );
      add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_valid_coupon' ), 10, 2 );
      add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_for_product_brand' ), 10, 4 );
    }

    public function coupon_restriction() {
        global $thepostid, $post;
        $thepostid = empty( $thepostid ) ? $post->get_ID() : $thepostid;

        $selected_brands = get_post_meta( $thepostid, '_pwb_coupon_restriction', true );
        if( $selected_brands == '' ) $selected_brands = array();

        ob_start();
        ?>
        <p class="form-field"><label for="_pwb_coupon_restriction"><?php _e( 'Brands restriction', 'perfect-woocommerce-brands' ); ?></label>
				<select id="_pwb_coupon_restriction" name="_pwb_coupon_restriction[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any brand', 'perfect-woocommerce-brands' ); ?>">
					<?php
						$categories   = get_terms( 'pwb-brand', 'orderby=name&hide_empty=0' );
						if ( $categories ) {
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $selected_brands ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
							}
						}
					?>
				</select> <?php echo wc_help_tip( __( 'Coupon will be valid if there are at least one product of this brands in the cart', 'perfect-woocommerce-brands' ) ); ?></p>
        <?php
        echo ob_get_clean();

    }

    public function coupon_save( $post_id ){
      $_pwb_coupon_restriction = isset( $_POST['_pwb_coupon_restriction'] ) ? $_POST['_pwb_coupon_restriction'] : '';
      update_post_meta( $post_id, '_pwb_coupon_restriction', $_pwb_coupon_restriction );
    }

    public function is_valid_coupon( $availability, $coupon ){
      $selected_brands = get_post_meta( $coupon->get_ID(), '_pwb_coupon_restriction', true );
      if( !empty( $selected_brands ) ){
        global $woocommerce;
        $products = $woocommerce->cart->get_cart();
        foreach( $products as $product ) {
          $product_brands = wp_get_post_terms( $product['product_id'], 'pwb-brand', array( 'fields' => 'ids' ) );
          $valid_brands = array_intersect( $selected_brands, $product_brands );
          if( !empty( $valid_brands ) ) return true;
        }
        return false;
      }
      return true;
    }

    public function is_valid_for_product_brand( $valid, $product, $coupon, $values ){
      if ( !$valid ) return false;

      $coupon_id = is_callable( array( $coupon, 'get_id' ) ) ?  $coupon->get_id() : $coupon->id;
      $selected_brands = get_post_meta( $coupon_id, '_pwb_coupon_restriction', true );
      if ( empty( $selected_brands ) ) return $valid;

      $product_id = is_callable( array( $product, 'get_id' ) ) ?  $product->get_id() : $product->id;
      $product_brands = wp_get_post_terms( $product_id, 'pwb-brand', array( 'fields' => 'ids' ) );
      $valid_brands = array_intersect( $selected_brands, $product_brands );
      return !empty( $valid_brands );
    }

  }
