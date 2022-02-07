<?php

/**
 * The template for displaying the list widget
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
?>

<ul class="pwb-row">
	<?php foreach ( $data['brands'] as $brand ) : ?>
	<li>
		<a href="<?php echo esc_html( $brand->get( 'link' ) ); ?>" title="<?php echo esc_html( $data['title_prefix'] . ' ' . $brand->get( 'name' ) ); ?>">
			<?php echo esc_html( $brand->get( 'name' ) ); ?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>
