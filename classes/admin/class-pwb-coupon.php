<?php
  namespace Perfect_Woocommerce_Brands\Admin;

  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  class PWB_Coupon{

    function __construct(){
      add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'coupon_restriction' ) );
      add_action( 'woocommerce_coupon_options_save',  array( $this, 'coupon_save' ) );
      add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_coupon' ), 10, 4 );
    }

    //since 2.5.0 moved to the 'Usage restriction' tab
    public function coupon_restriction() {
        global $thepostid, $post;
        $thepostid = empty( $thepostid ) ? $post->get_ID() : $thepostid;

        $selected_brands = get_post_meta( $thepostid, '_pwb_coupon_restriction', true );

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
				</select> <?php echo wc_help_tip( __( 'Restrict coupon usage to specific brands', 'perfect-woocommerce-brands' ) ); ?></p>
        <?php
        echo ob_get_clean();

    }

    public function coupon_save( $post_id ){
      $_pwb_coupon_restriction = isset( $_POST['_pwb_coupon_restriction'] ) ? $_POST['_pwb_coupon_restriction'] : '';
      update_post_meta( $post_id, '_pwb_coupon_restriction', $_pwb_coupon_restriction );
    }

    public function is_valid_coupon( $valid, $product, $coupon, $values ){
      $selected_brands = get_post_meta( $coupon->get_ID(), '_pwb_coupon_restriction', true );
      foreach( $selected_brands as $brand ) {
        if( has_term( $brand, 'pwb-brand', $product->get_ID() ) ) $valid = true;
      }
      return $valid;
    }

  }
