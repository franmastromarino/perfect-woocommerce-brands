<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Coupon {

	public function __construct() {
		add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'coupon_restriction' ) );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'coupon_save' ) );
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_valid_for_brand' ), 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_coupon_for_brand' ), 10, 4 );
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_valid_for_exclude_brand' ), 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_coupon_for_exclude_brand' ), 10, 4 );
	}

	public function coupon_restriction() {
		global $thepostid, $post;

		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

		$product_brands_ids = (array) get_post_meta( $thepostid, '_pwb_coupon_restriction', true );
		$exclude_brands_ids = (array) get_post_meta( $thepostid, '_pwb_coupon_exclude_brands', true );

		ob_start();
		?>		
		<p class="form-field">
			<label for="_pwb_coupon_restriction"><?php esc_html_e( 'Product brands', 'perfect-woocommerce-brands' ); ?></label>
			<select id="_pwb_coupon_restriction" name="_pwb_coupon_restriction[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any brand', 'perfect-woocommerce-brands' ); ?>">
				<?php
				$categories = get_terms( 'pwb-brand', 'orderby=name&hide_empty=0' );
				if ( $categories ) {
					foreach ( $categories as $cat ) {
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $product_brands_ids ) . '>' . esc_html( $cat->name ) . '</option>';
					}
				}
				?>
			</select>
			<?php echo wc_help_tip( esc_html__( 'Product brands that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'perfect-woocommerce-brands' ) ); ?>
		</p>		
		<p class="form-field">
			<label for="_pwb_coupon_exclude_brands"><?php esc_html_e( 'Exclude brands', 'perfect-woocommerce-brands' ); ?></label>
			<select id="_pwb_coupon_exclude_brands" name="_pwb_coupon_exclude_brands[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any brand', 'perfect-woocommerce-brands' ); ?>">
				<?php
				$categories = get_terms( 'pwb-brand', 'orderby=name&hide_empty=0' );
				if ( $categories ) {
					foreach ( $categories as $cat ) {
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $exclude_brands_ids ) . '>' . esc_html( $cat->name ) . '</option>';
					}
				}
				?>
			</select>
			<?php echo wc_help_tip( esc_html__( 'Product brands that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'perfect-woocommerce-brands' ) ); ?>
		</p>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();

	}

	public function coupon_save( $post_id ) {

		if ( ! array_key_exists( '_pwb_coupon_restriction', $_POST ) ) {// phpcs:ignore WordPress.Security
			delete_post_meta( $post_id, '_pwb_coupon_restriction' );
		} else {
			update_post_meta( $post_id, '_pwb_coupon_restriction', array_filter( array_map( 'intval', $_POST['_pwb_coupon_restriction'] ) ) );// phpcs:ignore WordPress.Security
		}

		if ( ! array_key_exists( '_pwb_coupon_exclude_brands', $_POST ) ) {// phpcs:ignore WordPress.Security
			delete_post_meta( $post_id, '_pwb_coupon_exclude_brands' );
		} else {
			update_post_meta( $post_id, '_pwb_coupon_exclude_brands', array_filter( array_map( 'intval', $_POST['_pwb_coupon_exclude_brands'] ) ) );// phpcs:ignore WordPress.Security
		}

	}

	public function is_valid_for_brand( $availability, $coupon ) {

		$valid = true;

		$product_brands_ids = get_post_meta( $coupon->get_ID(), '_pwb_coupon_restriction', true );

		if ( empty( $product_brands_ids ) ) {
			return $valid;
		}

		global $woocommerce;

		$products = $woocommerce->cart->get_cart();

		$valid = false;

		foreach ( $products as $product ) {

			$product_brands = wp_get_post_terms( $product['product_id'], 'pwb-brand', array( 'fields' => 'ids' ) );
			// If we find an item with a brand in our allowed cat list, the coupon is valid.
			if ( count( array_intersect( $product_brands_ids, $product_brands ) ) ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}

	public function is_valid_coupon_for_brand( $valid, $product, $coupon, $values ) {

		if ( ! $valid ) {
			return $valid;
		}

		$coupon_id = is_callable( array( $coupon, 'get_id' ) ) ? $coupon->get_id() : $coupon->id;

		$product_brands_ids = get_post_meta( $coupon_id, '_pwb_coupon_restriction', true );

		if ( empty( $product_brands_ids ) ) {
			return $valid;
		}

		if ( $product->is_type( 'variation' ) ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;
		}

		$product_brands = wp_get_post_terms( $product_id, 'pwb-brand', array( 'fields' => 'ids' ) );

		$valid = false;

		if ( count( array_intersect( $product_brands_ids, $product_brands ) ) ) {
			$valid = true;
		}

		return $valid;
	}

	public function is_valid_for_exclude_brand( $availability, $coupon ) {

		$valid = true;

		$exclude_brands_ids = get_post_meta( $coupon->get_ID(), '_pwb_coupon_exclude_brands', true );

		if ( empty( $exclude_brands_ids ) ) {
			return $valid;
		}

		global $woocommerce;

		$products = $woocommerce->cart->get_cart();

		$valid = false;

		foreach ( $products as $product ) {

			$product_brands = wp_get_post_terms( $product['product_id'], 'pwb-brand', array( 'fields' => 'ids' ) );
			// If we find an item with a brand in our allowed cat list, the coupon is invalid.
			if ( ! count( array_intersect( $exclude_brands_ids, $product_brands ) ) ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}

	public function is_valid_coupon_for_exclude_brand( $valid, $product, $coupon, $values ) {

		if ( ! $valid ) {
			return $valid;
		}

		$coupon_id = is_callable( array( $coupon, 'get_id' ) ) ? $coupon->get_id() : $coupon->id;

		$exclude_brands_ids = get_post_meta( $coupon_id, '_pwb_coupon_exclude_brands', true );

		if ( empty( $exclude_brands_ids ) ) {
			return $valid;
		}

		if ( $product->is_type( 'variation' ) ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;
		}

		$product_brands = wp_get_post_terms( $product_id, 'pwb-brand', array( 'fields' => 'ids' ) );

		$valid = false;

		if ( ! count( array_intersect( $exclude_brands_ids, $product_brands ) ) ) {
			$valid = true;
		}

		return $valid;
	}

}
