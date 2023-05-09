<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Brands_Custom_Fields {

	public function __construct() {
		add_action( 'pwb-brand_add_form_fields', array( $this, 'add_brands_metafields_form' ) );
		add_action( 'pwb-brand_edit_form_fields', array( $this, 'add_brands_metafields_form_edit' ) );
		add_action( 'edit_pwb-brand', array( $this, 'add_brands_metafields_save' ) );
		add_action( 'create_pwb-brand', array( $this, 'add_brands_metafields_save' ) );
	}

	public function add_brands_metafields_form() {
		ob_start();
		?>

	<div class="form-field pwb_brand_cont">
	<label for="pwb_brand_desc"><?php esc_html_e( 'Description', 'perfect-woocommerce-brands' ); ?></label>
	<?php wp_editor( '', 'pwb_brand_description_field', array( 'textarea_rows' => 5 ) ); ?>
	<p id="brand-description-help-text"><?php esc_html_e( 'Brand description for the archive pages. You can include some html markup and shortcodes.', 'perfect-woocommerce-brands' ); ?></p>
	</div>

	<div class="form-field pwb_brand_cont">
	<label for="pwb_long_brand_desc"><?php esc_html_e( 'Second description', 'perfect-woocommerce-brands' ); ?></label>
	<?php wp_editor( '', 'pwb_long_brand_description_field', array( 'textarea_rows' => 5 ) ); ?>
	<p id="brand-description-help-text"><?php esc_html_e( 'Second brand description for the archive pages. You can include some html markup and shortcodes.', 'perfect-woocommerce-brands' ); ?></p>
	</div>

	<div class="form-field pwb_brand_cont">
	<label for="pwb_brand_image"><?php esc_html_e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></label>
	<input type="text" name="pwb_brand_image" id="pwb_brand_image" value="">
	<a href="#" id="pwb_brand_image_select" class="button"><?php esc_html_e( 'Select image', 'perfect-woocommerce-brands' ); ?></a>
	</div>

	<div class="form-field pwb_brand_cont">
	<label for="pwb_brand_banner"><?php esc_html_e( 'Brand banner', 'perfect-woocommerce-brands' ); ?></label>
	<input type="text" name="pwb_brand_banner" id="pwb_brand_banner" value="">
	<a href="#" id="pwb_brand_banner_select" class="button"><?php esc_html_e( 'Select image', 'perfect-woocommerce-brands' ); ?></a>
	<p><?php esc_html_e( 'This image will be shown on brand page', 'perfect-woocommerce-brands' ); ?></p>
	</div>

	<div class="form-field pwb_brand_cont">
	<label for="pwb_brand_banner_link"><?php esc_html_e( 'Brand banner link', 'perfect-woocommerce-brands' ); ?></label>
	<input type="text" name="pwb_brand_banner_link" id="pwb_brand_banner_link" value="">
	<p><?php esc_html_e( 'This link should be relative to site url. Example: "/product/product-name"', 'perfect-woocommerce-brands' ); ?></p>
	</div>

		<?php wp_nonce_field( basename( __FILE__ ), 'pwb_nonce' ); ?>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();
	}

	public function add_brands_metafields_form_edit( $term ) {
		$term_value_image       = get_term_meta( $term->term_id, 'pwb_brand_image', true );
		$term_value_banner      = get_term_meta( $term->term_id, 'pwb_brand_banner', true );
		$term_value_banner_link = get_term_meta( $term->term_id, 'pwb_brand_banner_link', true );
		$term_long_brand_desc   = get_term_meta( $term->term_id, 'pwb_long_brand_desc', true );
		ob_start();
		$image_size_selected = get_option( 'wc_pwb_admin_tab_brand_logo_size', 'thumbnail' );

		?>
	<table class="form-table pwb_brand_cont">
	<tr class="form-field">
		<th>
		<label for="pwb_brand_desc"><?php esc_html_e( 'Description' ); ?></label>
		</th>
		<td>
		<?php wp_editor( html_entity_decode( $term->description ), 'pwb_brand_description_field', array( 'editor_height' => 120 ) ); ?>
		<p id="brand-description-help-text"><?php esc_html_e( 'Brand description for the archive pages. You can include some html markup and shortcodes.', 'perfect-woocommerce-brands' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th>
		<label for="pwb_long_brand_desc"><?php esc_html_e( 'Second description' ); ?></label>
		</th>
		<td>
		<?php wp_editor( html_entity_decode( $term_long_brand_desc ), 'pwb_long_brand_description_field', array( 'editor_height' => 120 ) ); ?>
		<p id="brand-description-help-text"><?php esc_html_e( 'Second brand description for the archive pages. You can include some html markup and shortcodes.', 'perfect-woocommerce-brands' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th>
		<label for="pwb_brand_image"><?php esc_html_e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></label>
		</th>
		<td>
		<input type="text" name="pwb_brand_image" id="pwb_brand_image" value="<?php echo esc_attr( $term_value_image ); ?>">
		<a href="#" id="pwb_brand_image_select" class="button"><?php esc_html_e( 'Select image', 'perfect-woocommerce-brands' ); ?></a>

		<?php $current_image = wp_get_attachment_image( $term_value_image, $image_size_selected, false ); ?>
		<?php if ( ! empty( $current_image ) ) : ?>
			<div class="pwb_brand_image_selected">
			<span>
				<?php echo wp_kses_post( $current_image ); ?>
				<a href="#" class="pwb_brand_image_selected_remove">X</a>
			</span>
			</div>
		<?php endif; ?>

		</td>
	</tr>
	<tr class="form-field">
		<th>
		<label for="pwb_brand_banner"><?php esc_html_e( 'Brand banner', 'perfect-woocommerce-brands' ); ?></label>
		</th>
		<td>
		<input type="text" name="pwb_brand_banner" id="pwb_brand_banner" value="<?php echo esc_html( $term_value_banner ); ?>">
		<a href="#" id="pwb_brand_banner_select" class="button"><?php esc_html_e( 'Select image', 'perfect-woocommerce-brands' ); ?></a>

		<?php $current_image = wp_get_attachment_image( $term_value_banner, 'full', false ); ?>
		<?php if ( ! empty( $current_image ) ) : ?>
			<div class="pwb_brand_image_selected">
			<span>
				<?php echo wp_kses_post( $current_image ); ?>
				<a href="#" class="pwb_brand_image_selected_remove">X</a>
			</span>
			</div>
		<?php endif; ?>

		</td>
	</tr>
	<tr class="form-field">
		<th>
		<label for="pwb_brand_banner_link"><?php esc_html_e( 'Brand banner link', 'perfect-woocommerce-brands' ); ?></label>
		</th>
		<td>
		<input type="text" name="pwb_brand_banner_link" id="pwb_brand_banner_link" value="<?php echo esc_html( $term_value_banner_link ); ?>">
		<p class="description"><?php esc_html_e( 'This link should be relative to site url. Example: "/product/product-name"', 'perfect-woocommerce-brands' ); ?></p>
		<div id="pwb_brand_banner_link_result"><?php echo wp_get_attachment_image( $term_value_banner_link, array( '90', '90' ), false ); ?></div>
		</td>
	</tr>
	</table>

		<?php wp_nonce_field( basename( __FILE__ ), 'pwb_nonce' ); ?>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();
	}

	public function add_brands_metafields_save( $term_id ) {
		if ( ! isset( $_POST['pwb_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['pwb_nonce'] ) ), basename( __FILE__ ) ) ) {
			return;
		}

		/* ·············· Brand image ·············· */
		$old_img = get_term_meta( $term_id, 'pwb_brand_image', true );
		$new_img = isset( $_POST['pwb_brand_image'] ) ? sanitize_text_field( wp_unslash( $_POST['pwb_brand_image'] ) ) : '';

		if ( $old_img && '' === $new_img ) {
			delete_term_meta( $term_id, 'pwb_brand_image' );

		} elseif ( $old_img !== $new_img ) {
			update_term_meta( $term_id, 'pwb_brand_image', $new_img );
		}
		/* ·············· /Brand image ·············· */

		/* ·············· Brand banner ·············· */
		$old_img = get_term_meta( $term_id, 'pwb_brand_banner', true );
		$new_img = isset( $_POST['pwb_brand_banner'] ) ? sanitize_text_field( wp_unslash( $_POST['pwb_brand_banner'] ) ) : '';

		if ( $old_img && '' === $new_img ) {
			delete_term_meta( $term_id, 'pwb_brand_banner' );

		} elseif ( $old_img !== $new_img ) {
			update_term_meta( $term_id, 'pwb_brand_banner', $new_img );
		}
		/* ·············· /Brand banner ·············· */

		/* ·············· Brand banner link ·············· */
		$old_img = get_term_meta( $term_id, 'pwb_brand_banner_link', true );
		$new_img = isset( $_POST['pwb_brand_banner_link'] ) ? esc_url_raw( wp_unslash( $_POST['pwb_brand_banner_link'] ) ) : '';

		if ( $old_img && '' === $new_img ) {
			delete_term_meta( $term_id, 'pwb_brand_banner_link' );

		} elseif ( $old_img !== $new_img ) {
			update_term_meta( $term_id, 'pwb_brand_banner_link', $new_img );
		}
		/* ·············· /Brand banner link ·············· */

		/* ·············· Brand desc ·············· */
		if ( isset( $_POST['pwb_brand_description_field'] ) ) {
			$desc = wp_kses_post( wp_unslash( $_POST['pwb_brand_description_field'] ) );
			global $wpdb;
			$wpdb->update( $wpdb->term_taxonomy, array( 'description' => $desc ), array( 'term_id' => $term_id ) );
		}
		/* ·············· /Brand desc ·············· */

		/* ·············· Brand second desc ·············· */
		$old_long_description = get_term_meta( $term_id, 'pwb_long_brand_desc', true );
		$new_long_description = isset( $_POST['pwb_long_brand_description_field'] ) ? wp_kses_post( wp_unslash( $_POST['pwb_long_brand_description_field'] ) ) : '';

		if ( $old_long_description && '' === $new_long_description ) {
			delete_term_meta( $term_id, 'pwb_long_brand_desc' );
		} elseif ( $old_long_description !== $new_long_description ) {
			update_term_meta( $term_id, 'pwb_long_brand_desc', $new_long_description );
		}
		/* ·············· /Brand second desc ·············· */
	}
}
