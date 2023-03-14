<?php

namespace QuadLayers\PWB\Widgets;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Brands_List extends \WP_Widget {

	public function __construct() {
		$params = array(
			'description' => __( 'Adds a brands list to your site', 'perfect-woocommerce-brands' ),
			'name'        => __( 'Brands list', 'perfect-woocommerce-brands' ),
		);
		parent::__construct( 'Brands_List', '', $params );
	}

	public function form( $instance ) {
		extract( $instance );

		$title = ( isset( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Brands', 'perfect-woocommerce-brands' );
		if ( ! isset( $display_as ) ) {
			$display_as = 'brand_logo';
		}
		if ( ! isset( $columns ) ) {
			$columns = '2';
		}
		$hide_empty    = ( isset( $hide_empty ) && 'on' == $hide_empty ) ? true : false;
		$only_featured = ( isset( $only_featured ) && 'on' == $only_featured ) ? true : false;
		$randomize     = ( isset( $randomize ) && 'on' == $randomize ) ? true : false;
		?>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'perfect-woocommerce-brands' ); ?></label>
		<input
			class="widefat"
			type="text"
			id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			value="
			<?php
			if ( isset( $title ) ) {
				echo esc_attr( $title );
			}
			?>
		">
	</p>
	<p>
	  <label for="<?php echo esc_attr( $this->get_field_id( 'display_as' ) ); ?>"><?php esc_html_e( 'Display as:', 'perfect-woocommerce-brands' ); ?></label>
	  <select
		class="widefat pwb-select-display-as"
		id="<?php echo esc_attr( $this->get_field_id( 'display_as' ) ); ?>"
		name="<?php echo esc_attr( $this->get_field_name( 'display_as' ) ); ?>">
		<option value="brand_name" <?php selected( $display_as, 'brand_name' ); ?>><?php esc_html_e( 'Brand name', 'perfect-woocommerce-brands' ); ?></option>
		<option value="brand_logo" <?php selected( $display_as, 'brand_logo' ); ?>><?php esc_html_e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></option>
	  </select>
	</p>
	<p class="pwb-display-as-logo<?php echo ( 'brand_logo' == $display_as ) ? ' show' : ''; ?>">
	  <label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Columns:', 'perfect-woocommerce-brands' ); ?></label>
	  <select
		class="widefat"
		id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"
		name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>">
		<option value="1" <?php selected( $columns, '1' ); ?>>1</option>
		<option value="2" <?php selected( $columns, '2' ); ?>>2</option>
		<option value="3" <?php selected( $columns, '3' ); ?>>3</option>
		<option value="4" <?php selected( $columns, '4' ); ?>>4</option>
		<option value="5" <?php selected( $columns, '5' ); ?>>5</option>
		<option value="6" <?php selected( $columns, '6' ); ?>>6</option>
	  </select>
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
	<p class="pwb-display-as-logo<?php echo ( 'brand_logo' == $display_as ) ? ' show' : ''; ?>">
	  <input
	  type="checkbox"
	  id="<?php echo esc_attr( $this->get_field_id( 'randomize' ) ); ?>"
	  name="<?php echo esc_attr( $this->get_field_name( 'randomize' ) ); ?>"
		<?php checked( $randomize ); ?>>
	  <label for="<?php echo esc_attr( $this->get_field_id( 'randomize' ) ); ?>">
		<?php esc_html_e( 'Randomize', 'perfect-woocommerce-brands' ); ?>
	  </label>
	</p>

		<?php
	}

	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		$hide_empty    = ( isset( $hide_empty ) && 'on' == $hide_empty ) ? true : false;
		$only_featured = ( isset( $only_featured ) && 'on' == $only_featured ) ? true : false;
		$randomize     = ( isset( $randomize ) && 'on' == $randomize ) ? true : false;
		$brands        = \QuadLayers\PWB\WooCommerce::get_brands(
			$hide_empty,
			'name',
			'ASC',
			$only_featured,
			true
		);
		if ( isset( $randomize ) && 'on' == $randomize && 'brand_logo' == $display_as ) {
			shuffle( $brands );
		}

		if ( is_array( $brands ) && count( $brands ) > 0 ) {

			echo $before_widget; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			}

			if ( ! isset( $display_as ) ) {
				$display_as = 'brand_logo';
			}
			if ( ! isset( $columns ) ) {
				$columns = '2';
			}
			$li_class = ( 'brand_logo' == $display_as ) ? 'pwb-columns pwb-columns-' . $columns : '';

			echo \QuadLayers\PWB\WooCommerce::render_template( // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
				( 'brand_logo' == $display_as ) ? 'list-logo' : 'list',
				'widgets',
				array(
					'brands'       => $brands,
					'li_class'     => $li_class,
					'title_prefix' => __( 'Go to', 'perfect-woocommerce-brands' ),
				),
				false
			);

			echo $after_widget; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		}

	}

}
