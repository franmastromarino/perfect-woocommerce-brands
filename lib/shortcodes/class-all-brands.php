<?php

namespace QuadLayers\PWB\Shortcodes;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class All_Brands {

	protected static function replace_curly_quotes( $attrs ) {
		if ( is_array( $attrs ) ) {
			foreach ( $attrs as $key => $value ) {
				$new_key           = str_replace( [ '“', '”', '″' ], '', $key );
				$new_value         = str_replace( [ '“', '”', '″' ], '', $value );
				$attrs[ $new_key ] = $new_value;

				if ( $new_key != $key ) {
					unset( $attrs[ $key ] );
				}
			}
		}
		return $attrs;
	}

	public static function all_brands_shortcode( $atts ) {
		$atts = self::replace_curly_quotes( $atts );
		$atts = shortcode_atts(
			array(
				'per_page'       => '10',
				'image_size'     => 'thumbnail',
				'hide_empty'     => false,
				'order_by'       => 'name',
				'order'          => 'ASC',
				'title_position' => 'before',
			),
			$atts,
			'pwb-all-brands'
		);

		$hide_empty = ( 'true' != $atts['hide_empty'] ) ? false : true;

		ob_start();

		$brands = array();
		if ( 'rand' == $atts['order_by'] ) {
			$brands = \QuadLayers\PWB\WooCommerce::get_brands( $hide_empty );
			shuffle( $brands );
		} else {
			$brands = \QuadLayers\PWB\WooCommerce::get_brands( $hide_empty, $atts['order_by'], $atts['order'] );
		}

		/**
		 * Remove residual empty brands
		 */
		foreach ( $brands as $key => $brand ) {

			$count = self::count_visible_products( $brand->term_id );

			if ( ! $count && $hide_empty ) {
				unset( $brands[ $key ] );
			} else {
				$brands[ $key ]->count_pwb = $count;
			}
		}

		?>
	<div class="pwb-all-brands">
		<?php static::pagination( $brands, $atts['per_page'], $atts['image_size'], $atts['title_position'] ); ?>
	</div>
		<?php

		return ob_get_clean();
	}

	/**
	 *  WP_Term->count property don´t care about hidden products
	 *  Counts the products in a specific brand
	 */
	public static function count_visible_products( $brand_id ) {
		$args     = array(
			'posts_per_page' => -1,
			'post_type'      => 'product',
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'pwb-brand',
					'field'    => 'term_id',
					'terms'    => $brand_id,
				),
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'exclude-from-catalog',
					'operator' => 'NOT IN',
				),
			),
		);
		$wc_query = new \WP_Query( $args );

		return $wc_query->found_posts;
	}

	public static function pagination( $display_array, $show_per_page, $image_size, $title_position ) {

		$page = 1;

		if ( isset( $_GET['pwb-page'] ) && true == filter_var( wp_unslash( $_GET['pwb-page'] ), FILTER_VALIDATE_INT ) ) {
			$page = intval( $_GET['pwb-page'] );
		}

		$page = $page < 1 ? 1 : $page;

		/**
		 * Start position in the $display_array
		 * +1 is to account for total values.
		 */
		$start  = ( $page - 1 ) * ( $show_per_page );
		$offset = $show_per_page;

		$out_array = array_slice( $display_array, $start, $offset );
		/**
		 * Pagination links
		 */
		$total_elements = count( $display_array );
		$pages          = ( (int) $total_elements / (int) $show_per_page );
		$pages          = ceil( $pages );

		if ( $pages >= 1 && $page <= $pages ) {
			?>
				<div class="pwb-brands-cols-outer">
					<?php
					foreach ( $out_array as $brand ) :

						$brand_id   = $brand->term_id;
						$brand_name = $brand->name;
						$brand_link = get_term_link( $brand_id );

						$attachment_id   = get_term_meta( $brand_id, 'pwb_brand_image', 1 );
						$attachment_html = $brand_name;
						if ( '' != $attachment_id ) {
							$attachment_html = wp_get_attachment_image( $attachment_id, $image_size );
						}

						?>
							<div class="pwb-brands-col3">
								<?php if ( 'none' != $title_position && 'after' != $title_position ) : ?>
									<p>
											<a href="<?php echo esc_url( $brand_link ); ?>">
												<?php echo esc_html( $brand_name ); ?>
											</a>
											<small>(<?php echo esc_html( $brand->count_pwb ); ?>)</small>
									</p>
								<?php endif; ?>
								<div>
									<a href="<?php echo esc_url( $brand_link ); ?>" title="<?php echo esc_html( $brand_name ); ?>">
										<?php echo wp_kses_post( $attachment_html ); ?>
									</a>
								</div>
								<?php if ( 'none' != $title_position && 'after' == $title_position ) : ?>
									<p>
										<a href="<?php echo esc_html( $brand_link ); ?>">
											<?php echo wp_kses_post( $brand_name ); ?>
										</a>
										<small>(<?php echo esc_html( $brand->count_pwb ); ?>)</small>
									</p>
								<?php endif; ?>
							</div>
					<?php endforeach; ?>
				</div>
			<?php
			$next = $page + 1;
			$prev = $page - 1;
			echo '<div class="pwb-pagination-wrapper">';
			if ( $prev > 1 ) {
				echo '<a href="' . esc_url( get_the_permalink() ) . '" class="pwb-pagination prev" title="' . esc_html__( 'First page', 'perfect-woocommerce-brands' ) . '">&laquo;</a>';
			}
			if ( $prev > 0 ) {
				echo '<a href="' . esc_url( get_the_permalink() ) . '?pwb-page=' . esc_attr( $prev ) . '" class="pwb-pagination last" title="' . esc_html__( 'Previous page', 'perfect-woocommerce-brands' ) . '">&lsaquo;</a>';
			}

			if ( $next <= $pages ) {
				echo '<a href="' . esc_url( get_the_permalink() ) . '?pwb-page=' . esc_attr( $next ) . '" class="pwb-pagination first" title="' . esc_html__( 'Next page', 'perfect-woocommerce-brands' ) . '">&rsaquo;</a>';
			}
			if ( $next < $pages ) {
				echo '<a href="' . esc_url( get_the_permalink() ) . '?pwb-page=' . esc_attr( $pages ) . '" class="pwb-pagination next" title="' . esc_html__( 'Last page', 'perfect-woocommerce-brands' ) . '">&raquo;</a>';
			}
			echo '</div>';
		} else {
			echo esc_html__( 'No results', 'perfect-woocommerce-brands' );
		}
	}
}
