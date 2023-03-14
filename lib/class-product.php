<?php
namespace QuadLayers\PWB;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Product {

	public function __construct() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'product_tab' ) );
	}

	public function product_tab( $tabs ) {
		global $product;

		if ( isset( $product ) ) {
			$brands = wp_get_object_terms( $product->get_id(), 'pwb-brand' );

			if ( ! empty( $brands ) ) {
				$show_brand_tab = get_option( 'wc_pwb_admin_tab_brand_single_product_tab' );
				if ( 'yes' == $show_brand_tab || ! $show_brand_tab ) {
					$tabs['pwb_tab'] = array(
						'title'    => __( 'Brand', 'perfect-woocommerce-brands' ),
						'priority' => 20,
						'callback' => array( $this, 'product_tab_content' ),
					);
				}
			}
		}

		return $tabs;
	}

	public function product_tab_content() {
		global $product;
		$brands = wp_get_object_terms( $product->get_id(), 'pwb-brand' );

		ob_start();
		?>

		<h2><?php echo esc_html( apply_filters( 'woocommerce_product_brand_heading', esc_html__( 'Brand', 'perfect-woocommerce-brands' ) ) ); ?></h2>
		<?php foreach ( $brands as $brand ) : ?>
			<?php
			$image_size = get_option( 'wc_pwb_admin_tab_brand_logo_size', 'thumbnail' );
			$brand_logo = get_term_meta( $brand->term_id, 'pwb_brand_image', true );
			$brand_logo = wp_get_attachment_image( $brand_logo, apply_filters( 'pwb_product_tab_brand_logo_size', $image_size ) );
			$brand_link = get_term_link( $brand->term_id, 'pwb-brand' );
			?>
				<div id="tab-pwb_tab-content">
					<h3><?php echo esc_html( $brand->name ); ?></h3>
						<?php if ( ! empty( $brand->description ) ) : ?>
							<div>
								<?php echo wp_kses_post( $brand->description ); ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $brand_logo ) ) : ?>
							<span>
								<a href="<?php echo esc_url( $brand_link ); ?>" title="<?php echo esc_html( $brand->name ); ?>" ><?php echo wp_kses_post( $brand_logo ); ?></a>
							</span>
					<?php endif; ?>
				</div>
		<?php endforeach; ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();
	}
}
