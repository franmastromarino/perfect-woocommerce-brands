<?php

namespace QuadLayers\PWB\Widgets;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Brands_Dropdown extends \WP_Widget {

	public function __construct() {
		$params = array(
			'description' => esc_html__( 'Adds a brands dropdown to your site', 'perfect-woocommerce-brands' ),
			'name'        => esc_html__( 'Brands dropdown', 'perfect-woocommerce-brands' ),
		);
		parent::__construct( 'Brands_Dropdown', '', $params );
	}

	public function form( $instance ) {
		extract( $instance );

		$title         = ( isset( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Brands', 'perfect-woocommerce-brands' );
		$hide_empty    = ( isset( $hide_empty ) && 'on' == $hide_empty ) ? true : false;
		$only_featured = ( isset( $only_featured ) && 'on' == $only_featured ) ? true : false;
		?>

	<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
		<?php esc_html_e( 'Title', 'perfect-woocommerce-brands' ); ?>
	</label>
	<input
	class="widefat"
	type="text"
	id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
	name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
	value="
		<?php
		if ( isset( $title ) ) {
			echo esc_attr( $title );}
		?>
		">
	</p>

	<p>
	<input
	type="checkbox"
	id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"
	name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>"
		<?php checked( $hide_empty ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>">
		<?php esc_html_e( 'Hide empty', 'perfect-woocommerce-brands' ); ?>
	</label>
	</p>

	<p>
	<input
	type="checkbox"
	id="<?php echo esc_attr( $this->get_field_id( 'only_featured' ) ); ?>"
	name="<?php echo esc_attr( $this->get_field_name( 'only_featured' ) ); ?>"
		<?php checked( $only_featured ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'only_featured' ) ); ?>">
		<?php esc_html_e( 'Only favorite brands', 'perfect-woocommerce-brands' ); ?>
	</label>
	</p>

		<?php
	}

	public function widget( $args, $instance ) {

		extract( $args );
		extract( $instance );

		$queried_obj      = get_queried_object();
		$queried_brand_id = ( isset( $queried_obj->term_id ) ) ? $queried_obj->term_id : false;

		$hide_empty    = ( isset( $hide_empty ) && 'on' == $hide_empty ) ? true : false;
		$only_featured = ( isset( $only_featured ) && 'on' == $only_featured ) ? true : false;
		$brands        = \QuadLayers\PWB\WooCommerce::get_brands(
			$hide_empty,
			'name',
			'ASC',
			$only_featured,
			true
		);

		if ( is_array( $brands ) && count( $brands ) > 0 ) {

			echo $before_widget;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo \QuadLayers\PWB\WooCommerce::render_template(// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'dropdown',
				'widgets',
				array(
					'brands'   => $brands,
					'selected' => $queried_brand_id,
				),
				false
			);

			echo $after_widget;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		}

	}

}
