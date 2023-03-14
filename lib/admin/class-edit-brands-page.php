<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Edit_Brands_Page {

	public function __construct() {
		add_filter( 'get_terms', array( $this, 'brand_list_admin_filter' ), 10, 3 );
		add_filter( 'manage_edit-pwb-brand_columns', array( $this, 'brand_taxonomy_columns_head' ) );
		add_filter( 'manage_pwb-brand_custom_column', array( $this, 'brand_taxonomy_columns' ), 10, 3 );
		add_action( 'wp_ajax_pwb_admin_set_featured_brand', array( $this, 'admin_set_featured_brand' ) );
		add_filter( 'screen_settings', array( $this, 'add_screen_options' ), 10, 2 );
		add_action( 'wp_ajax_pwb_admin_save_screen_settings', array( $this, 'admin_save_screen_settings' ) );
		add_action( 'after-pwb-brand-table', array( $this, 'add_brands_count' ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	private static function is_edit_brands_page() {
		global $pagenow;
		return ( 'edit-tags.php' == $pagenow && isset( $_GET['taxonomy'] ) && 'pwb-brand' == $_GET['taxonomy'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	public function add_brands_count( $tax_name ) {
		$brands          = get_terms(
			$tax_name,
			array( 'hide_empty' => false )
		);
		$brands_featured = get_terms(
			$tax_name,
			array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key'   => 'pwb_featured_brand',
						'value' => true,
					),
				),
			)
		);

		echo \QuadLayers\PWB\WooCommerce::render_template(// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'edit-brands-bottom',
			'admin',
			array(
				'featured_count' => count( $brands_featured ),
				'text_featured'  => esc_html__( 'featured', 'perfect-woocommerce-brands' ),
			)
		);
	}

	public function brand_list_admin_filter( $brands, $taxonomies, $args ) {

		$current_user = wp_get_current_user();

		if ( self::is_edit_brands_page() && ! empty( $current_user->ID ) ) {
			$featured = get_user_option( 'pwb-first-featured-brands', $current_user->ID );
			if ( $featured ) {
				$featured_brands = array();
				$other_brands    = array();
				foreach ( $brands as $brand ) {
					if ( isset( $brand->term_id ) && get_term_meta( $brand->term_id, 'pwb_featured_brand', true ) ) {
						$featured_brands[] = $brand;
					} else {
						$other_brands[] = $brand;
					}
				}
				return array_merge( $featured_brands, $other_brands );
			}
		}
		return $brands;
	}

	public function brand_taxonomy_columns_head( $columns ) {
		$new_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		if ( isset( $columns['description'] ) ) {
			unset( $columns['description'] );
		}

		$new_columns['logo'] = __( 'Logo', 'perfect-woocommerce-brands' );
		$columns['featured'] = '<span class="pwb-featured-col-title">' . __( 'Featured', 'perfect-woocommerce-brands' ) . '</span>';

		return array_merge( $new_columns, $columns );
	}

	public function brand_taxonomy_columns( $c, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'logo':
				$image = wp_get_attachment_image( get_term_meta( $term_id, 'pwb_brand_image', 1 ), array( '40', '40' ) );
				return ( $image ) ? $image : wc_placeholder_img( array( '40', '40' ) );
			break;
			case 'featured':
				$featured_class = $this->is_featured_brand( $term_id ) ? 'dashicons-star-filled' : 'dashicons-star-empty';
				printf(
					'<span class="dashicons %1$s" title="%2$s" data-brand-id="%3$s"></span>',
					esc_attr( $featured_class ),
					esc_html__( 'Set as featured', 'perfect-woocommerce-brands' ),
					esc_attr( $term_id )
				);
				break;
		}
	}

	private function is_featured_brand( $brand_id ) {
		return ( get_term_meta( $brand_id, 'pwb_featured_brand', true ) );
	}

	public function admin_set_featured_brand() {

		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['nonce'] ) ), 'pwb_admin_set_featured_brand' ) && current_user_can( 'manage_options' ) ) {

			if ( isset( $_POST['brand'] ) ) {
				$direction = 'up';
				$brand     = intval( $_POST['brand'] );
				if ( $this->is_featured_brand( $brand ) ) {
					delete_term_meta( $brand, 'pwb_featured_brand', true );
					$direction = 'down';
				} else {
					update_term_meta( $brand, 'pwb_featured_brand', true );
				}
				wp_send_json_success(
					array(
						'success'   => true,
						'direction' => $direction,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'success'   => false,
						'error_msg' => __(
							'Error!',
							'perfect-woocommerce-brands'
						),
					)
				);
			}
		}
		wp_die();
	}

	public function add_screen_options( $status, $args ) {
		if ( self::is_edit_brands_page() ) {

			$current_user = wp_get_current_user();

			$featured = get_user_option( 'pwb-first-featured-brands', $current_user->ID );
			ob_start();
			?>
				<legend><?php esc_html_e( 'Brands', 'perfect-woocommerce-brands' ); ?></legend>
				<label>
					<input id="pwb-first-featured-brands" type="checkbox" <?php checked( $featured, true ); ?>>
						<?php esc_html_e( 'Show featured brands first', 'perfect-woocommerce-brands' ); ?>
				</label>
			<?php
			return ob_get_clean();
		}
	}

	public function admin_save_screen_settings() {
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['nonce'] ) ), 'pwb_admin_save_screen_settings' ) && current_user_can( 'manage_options' ) ) {

			$current_user = wp_get_current_user();

			if ( isset( $_POST['new_val'] ) ) {
				$new_val = ( 'true' == $_POST['new_val'] ) ? true : false;
				update_user_option( $current_user->ID, 'pwb-first-featured-brands', $new_val );
			}
		}
		wp_die();
	}
}
