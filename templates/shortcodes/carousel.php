<?php

/**
 * The template for displaying the carousels
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
?>

<div class="pwb-carousel" data-slick="<?php echo $slick_settings; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
	<?php foreach ( $brands as $brand ) : ?>
		<div class="pwb-slick-slide">
			<a href="<?php echo esc_url( $brand['link'] ); ?>" title="<?php echo esc_html( $brand['name'] ); ?>">
				<?php echo wp_kses_post( $brand['attachment_html'] ); ?>
			</a>
		</div>
	<?php endforeach; ?>
	<div class="pwb-carousel-loader"><?php esc_html_e( 'Loading', 'perfect-woocommerce-brands' ); ?>...</div>
</div>
