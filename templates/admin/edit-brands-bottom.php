<?php
/**
 * The template for displaying the edit-tags.php table footer
 *
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
?>

<div class="pwb-edit-brands-bottom pwb-clearfix">
	<span class="dashicons dashicons-admin-collapse"></span>
	<p class="pwb-featured-count">
		<span><?php echo esc_html( $data['featured_count'] ); ?></span> <?php echo esc_html( $data['text_featured'] ); ?>
	</p>
</div>
