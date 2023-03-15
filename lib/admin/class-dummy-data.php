<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Dummy_Data {

	public function __construct() {
		add_action( 'wp_ajax_pwb_admin_dummy_data', array( $this, 'admin_dummy_data' ) );
	}

	private static function get_attachment_id_from_src( $image_src ) {
		global $wpdb;
		// $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
		// $id    = $wpdb->get_var( $query );
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%1s'", $image_src ) );
		return $id;
	}

	private static function retrieve_img_src( $img ) {
		if ( preg_match( '/<img(\s+?)([^>]*?)src=(\"|\')([^>\\3]*?)\\3([^>]*?)>/is', $img, $m ) && isset( $m[4] ) ) {
			return $m[4];
		}
		return false;
	}

	private static function upload_image( $post_id, $img_url ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		/**
		 *Solves media_sideload_image bug with spaces in filenames
		 */
		$parsed_file       = wp_parse_url( $img_url );
		$path              = $parsed_file['path'];
		$file_name         = basename( $path );
		$encoded_file_name = rawurlencode( $file_name );
		$path              = str_replace( $file_name, $encoded_file_name, $path );
		$img_url           = $parsed_file['scheme'] . '://' . $parsed_file['host'] . $path;
		$image             = '';

		preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $img_url, $file_matches );
		if ( isset( $file_matches[0] ) ) {
			$image = media_sideload_image( $img_url, $post_id );
		}

		/**
		 * Media_sideload_image returns a html image
		 * Extract the src value for get the attachment id
		 */
		$image_src = self::retrieve_img_src( $image );
		return self::get_attachment_id_from_src( $image_src );
	}

	private function build_description() {
		$desc          = 'lorem ipsum dolor <strong>sit</strong> amet consectetur adipiscing elit etiam mollis faucibus aliquet';
		$desc         .= 'sed risus turpis dapibus vel <strong>rhoncus</strong> a vestibulum sed lectus in hac habitasse platea dictumst';
		$desc         .= 'suspendisse non luctus felis <strong>morbi</strong> id volutpat ligula quisque rutrum arcu at erat lobortis';
		$exploded_desc = explode( ' ', $desc );
		shuffle( $exploded_desc );
		$desc = implode( ' ', $exploded_desc );
		return ucfirst( $desc );
	}

	public function admin_dummy_data() {
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['nonce'] ) ), 'pwb_admin_dummy_data' ) && current_user_can( 'manage_options' ) ) {

			for ( $i = 1; $i < 11; $i++ ) {
				$term_desc      = $this->build_description();
				$brand_name     = 'brand' . $i;
				$attachment_id  = self::upload_image( false, PWB_PLUGIN_URL . '/assets/frontend/img/dummy-data/' . $brand_name . '.png' );
				$inserted_brand = wp_insert_term( ucfirst( $brand_name ), 'pwb-brand', array( 'description' => $term_desc ) );
				if ( ! is_wp_error( $inserted_brand ) && isset( $inserted_brand['term_id'] ) ) {
					add_term_meta( $inserted_brand['term_id'], 'pwb_brand_image', $attachment_id );
				}
			}

			$this->set_brands_randomly();
		}

		wp_die();
	}

	public function set_brands_randomly() {
		$brands = \QuadLayers\PWB\WooCommerce::get_brands_array();

		$the_query = new \WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'product',
			)
		);

		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			wp_set_object_terms( get_the_ID(), array_rand( $brands ), 'pwb-brand' );
		}
		wp_reset_postdata();
	}
}
