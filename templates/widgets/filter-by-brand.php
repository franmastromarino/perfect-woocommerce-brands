<?php

/**
 * The template for displaying filter by brand widget
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
?>

<div class="pwb-filter-products
<?php
if ( $hide_submit_btn ) {
	echo ' pwb-hide-submit-btn';}
?>
" data-cat-url="<?php echo esc_url( $cate_url ); ?>">
	<ul>
		<?php foreach ( $brands as $brand ) : ?>
		<li>
			<label>
				<input type="checkbox" data-brand="<?php echo esc_attr( $brand->term_id ); ?>" value="<?php echo esc_html( $brand->slug ); ?>">
				<span><?php echo esc_html( $brand->name ); ?></span>
			</label>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( ! $hide_submit_btn ) : ?>
		<button class="pwb-apply-filter"><?php esc_html_e( 'Apply filter', 'perfect-woocommerce-brands' ); ?></button>
	<?php endif; ?>
	<?php if ( ! $hide_submit_btn && ! empty( $_GET['pwb-brand-filter'] ) ) : ?>
		<button class="pwb-remove-filter"><?php esc_html_e( 'Remove filter', 'perfect-woocommerce-brands' ); ?></button>
	<?php endif; ?>
</div>
