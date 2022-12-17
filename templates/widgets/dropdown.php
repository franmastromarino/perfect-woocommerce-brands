<?php

/**
 * The template for displaying the dropdown widget
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
?>

<select class="pwb-dropdown-widget">
	<option selected="true" disabled="disabled">
		<?php echo esc_html( apply_filters( 'pwb_dropdown_placeholder', __( 'Brands', 'perfect-woocommerce-brands' ) ) ); ?>
	</option>
	<?php foreach ( $brands as $brand ) : ?>
		<option value="<?php echo esc_url( $brand->get( 'link' ) ); ?>" <?php selected( $data['selected'], $brand->get( 'id' ) ); ?>>
			<?php echo esc_html( $brand->get( 'name' ) ); ?>
		</option>
	<?php endforeach; ?>
</select>
