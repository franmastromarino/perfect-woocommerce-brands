<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Brands_Exporter {

	public function __construct() {
		add_action( 'after-pwb-brand-table', array( $this, 'exporter_button' ) );
		add_action( 'wp_ajax_pwb_brands_export', array( $this, 'brands_export' ) );
		add_action( 'wp_ajax_pwb_brands_import', array( $this, 'brands_import' ) );
	}

	public function exporter_button() {
		echo \QuadLayers\PWB\WooCommerce::render_template(// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'brands-exporter',
			'admin',
			array( 'ok' => 'va' )
		);
	}

	public function brands_export() {
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['nonce'] ) ), 'pwb_brands_export' ) && current_user_can( 'manage_options' ) ) {
			$this->get_brands();
		} else {
			wp_send_json_error();
		}
		wp_die();
	}

	private function get_brands() {
		$brands_data = array();

		$brands = get_terms( 'pwb-brand', array( 'hide_empty' => false ) );
		foreach ( $brands as $brand ) {

			$current_brand = array(
				'slug'        => $brand->slug,
				'name'        => $brand->name,
				'banner_link' => get_term_meta( $brand->term_id, 'pwb_brand_banner_link', true ),
				'desc'        => htmlentities( $brand->description ),
			);

			$image = get_term_meta( $brand->term_id, 'pwb_brand_image', true );
			$image = wp_get_attachment_image_src( $image, 'full' );
			if ( $image ) {
				$current_brand['image'] = $image[0];
			}

			$banner = get_term_meta( $brand->term_id, 'pwb_brand_banner', true );
			$banner = wp_get_attachment_image_src( $banner, 'full' );
			if ( $banner ) {
				$current_brand['banner'] = $banner[0];
			}

			$brands_data[] = $current_brand;
		}

		$export_file = fopen( WP_CONTENT_DIR . '/uploads/pwb-export.json', 'w' );
		fwrite( $export_file, json_encode( $brands_data ) );
		fclose( $export_file );

		$result = array( 'export_file_url' => WP_CONTENT_URL . '/uploads/pwb-export.json' );

		wp_send_json_success( $result );
	}

	public function brands_import() {
		if ( isset( $_FILES['file'] ) && isset( $_REQUEST['nonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['nonce'] ) ), 'pwb_brands_import' ) && current_user_can( 'manage_options' ) ) {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$file = $_FILES['file'];

			$file_content = json_decode( file_get_contents( $file['tmp_name'] ), true );

			if ( is_array( $file_content ) ) {

				foreach ( $file_content as $brand ) {

					$new_brand = wp_insert_term(
						$brand['name'],
						'pwb-brand',
						array(
							'slug'        => $brand['slug'],
							'description' => html_entity_decode( $brand['desc'] ),
						)
					);

					if ( ! is_wp_error( $new_brand ) ) {

						if ( ! empty( $brand['image'] ) ) {
							$this->upload_remote_image_and_attach( $brand['image'], $new_brand['term_id'], 'pwb_brand_image' );
						}
						if ( ! empty( $brand['banner'] ) ) {
							$this->upload_remote_image_and_attach( $brand['banner'], $new_brand['term_id'], 'pwb_brand_banner' );
						}
						if ( ! empty( $brand['banner_link'] ) ) {
								update_term_meta( $new_brand['term_id'], 'pwb_brand_banner_link', $brand['banner_link'], true );
						}
					}
				}

				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} else {
			wp_send_json_error();
		}

		wp_die();
	}

	private function upload_remote_image_and_attach( $image_url, $term_id, $meta_key ) {
		$get  = wp_remote_get( $image_url );
		$type = wp_remote_retrieve_header( $get, 'content-type' );

		if ( ! $type ) {
			return false;
		}

		$mirror = wp_upload_bits( basename( $image_url ), null, wp_remote_retrieve_body( $get ) );

		$attachment = array(
			'post_title'     => basename( $image_url ),
			'post_mime_type' => $type,
		);

		$attach_id = wp_insert_attachment( $attachment, $mirror['file'] );
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		update_term_meta( $term_id, $meta_key, $attach_id, true );
	}
}
