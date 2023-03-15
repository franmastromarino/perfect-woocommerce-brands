<?php

namespace QuadLayers\PWB;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class WooCommerce {

	public function __construct() {
		$this->register_brands_taxonomy();
		add_action( 'init', array( $this, 'add_brands_metafields' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		$this->brand_logo_position();
		add_action( 'wp', array( $this, 'brand_desc_position' ) );
		/**
		 * Add brands to product loop before price and after sale flash
		 */
		add_action( 'woocommerce_after_shop_loop_item_title', array( self::class, 'show_brands_in_loop' ), 9 );
		$this->add_shortcodes();
		if ( is_plugin_active( 'js_composer/js_composer.php' ) || is_plugin_active( 'visual_composer/js_composer.php' ) ) {
			add_action( 'vc_before_init', array( $this, 'vc_map_shortcodes' ) );
		}
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_filter( 'woocommerce_structured_data_product', array( $this, 'product_microdata' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'pwb_brand_filter' ) );

		add_action(
			'wp',
			function () {
				if ( is_tax( 'pwb-brand' ) ) {
					remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
				}
			}
		);
		add_action( 'woocommerce_product_duplicate', array( $this, 'product_duplicate_save' ), 10, 2 );

		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'breadcrumbs' ) );

		add_filter( 'shortcode_atts_products', array( $this, 'extend_products_shortcode_atts' ), 10, 4 );
		add_filter( 'woocommerce_shortcode_products_query', array( $this, 'extend_products_shortcode' ), 10, 2 );

		add_filter( 'manage_edit-product_sortable_columns', array( $this, 'brands_column_sortable' ), 90 );
		add_action( 'posts_clauses', array( $this, 'brands_column_sortable_posts' ), 10, 2 );
		add_filter( 'post_type_link', array( $this, 'brand_name_in_url' ), 10, 2 );

		/**
		 * Clean cache after edit brand
		 */
		add_action( 'edited_terms', array( $this, 'clean_caches' ), 10, 2 );
		add_action( 'created_term', array( $this, 'clean_caches_after_edit_brand' ), 10, 3 );
		add_action( 'delete_term', array( $this, 'clean_caches_after_edit_brand' ), 10, 3 );
	}

	public function clean_caches( $term_id, $taxonomy ) {
		if ( 'pwb-brand' != $taxonomy ) {
			return;
		}
		delete_transient( 'pwb_az_listing_cache_' . get_locale() );
	}

	public function clean_caches_after_edit_brand( $term_id, $tt_id, $taxonomy ) {
		if ( 'pwb-brand' != $taxonomy ) {
			return;
		}
		delete_transient( 'pwb_az_listing_cache_' . get_locale() );
	}

	public function brand_name_in_url( $permalink, $post ) {
		if ( 'product' == $post->post_type && strpos( $permalink, '%pwb-brand%' ) !== false ) {
			$term   = 'product';
			$brands = wp_get_post_terms( $post->ID, 'pwb-brand' );
			if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
				$term = current( $brands )->slug;
			}
			$permalink = str_replace( '%pwb-brand%', $term, $permalink );
		}
		return $permalink;
	}

	public function brands_column_sortable_posts( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'taxonomy-pwb-brand' == $wp_query->query['orderby'] ) {

			$clauses['join'] .= "
      LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
      LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
      LEFT OUTER JOIN {$wpdb->terms} USING (term_id)";

			$clauses['where']   .= " AND (taxonomy = 'pwb-brand' OR taxonomy IS NULL)";
			$clauses['groupby']  = 'object_id';
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}

	public function brands_column_sortable( $columns ) {
		$columns['taxonomy-pwb-brand'] = 'taxonomy-pwb-brand';
		return $columns;
	}

	public function extend_products_shortcode_atts( $out, $pairs, $atts, $shortcode ) {
		if ( ! empty( $atts['brands'] ) ) {
			$out['brands'] = explode( ',', $atts['brands'] );
		}
		return $out;
	}

	public function extend_products_shortcode( $query_args, $atts ) {

		if ( ! empty( $atts['brands'] ) ) {
			global $wpdb;

			$terms_in = implode( "', '", $atts['brands'] );
			$in       = sprintf( "IN('%s')", $terms_in );
			$from     = sprintf( 'FROM %1$sterm_relationships as tr INNER JOIN %1$sterm_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN %1$sterms as t ON tt.term_id = t.term_id', $wpdb->prefix );

			$where = sprintf( "WHERE tt.taxonomy LIKE 'pwb_brand' AND t.slug %s", $in );

			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$ids = $wpdb->get_col( sprintf( 'SELECT DISTINCT tr.object_id %s %s', $from, $where ) );

			if ( ! empty( $ids ) ) {
				if ( 1 === count( $ids ) ) {
					$query_args['p'] = $ids[0];
				} else {
					$query_args['post__in'] = $ids;
				}
			}
		}

		return $query_args;
	}

	public function pwb_brand_filter( $query ) {

		if ( ! empty( $_GET['pwb-brand-filter'] ) ) {

			$terms_array = explode( ',', sanitize_text_field( wp_unslash( $_GET['pwb-brand-filter'] ) ) );
			/**
			 * Remove invalid terms (security)
			 */
			$terms_array_count = count( $terms_array );
			for ( $i = 0; $i < $terms_array_count; $i++ ) {
				if ( ! term_exists( $terms_array[ $i ], 'pwb-brand' ) ) {
					unset( $terms_array[ $i ] );
				}
			}

			$filterable_product = false;
			if ( is_product_taxonomy() || is_post_type_archive( 'product' ) ) {
				$filterable_product = true;
			}

			if ( $filterable_product && $query->is_main_query() ) {

				$query->set(
					'tax_query',
					array(
						array(
							'taxonomy' => 'pwb-brand',
							'field'    => 'slug',
							'terms'    => $terms_array,
						),
					)
				);
			}
		}
	}

	/**
	 * Adds microdata (brands) to single products
	 */
	public function product_microdata( $markup, $product ) {
		$new_markup = array();
		$brands     = wp_get_post_terms( $product->get_id(), 'pwb-brand' );
		foreach ( $brands as $brand ) {
			$new_markup['brand'][] = array(
				'@type' => 'Brand',
				'name'  => $brand->name,
			);
		}

		return array_merge( (array) $markup, $new_markup );
	}

	public function add_shortcodes() {
		add_shortcode(
			'pwb-carousel',
			array(
				'\QuadLayers\PWB\Shortcodes\Carousel',
				'carousel_shortcode',
			)
		);
		add_shortcode(
			'pwb-product-carousel',
			array(
				'\QuadLayers\PWB\Shortcodes\Product_Carousel',
				'product_carousel_shortcode',
			)
		);
		add_shortcode(
			'pwb-all-brands',
			array(
				'\QuadLayers\PWB\Shortcodes\All_Brands',
				'all_brands_shortcode',
			)
		);
		add_shortcode(
			'pwb-az-listing',
			array(
				'\QuadLayers\PWB\Shortcodes\AZ_Listing',
				'shortcode',
			)
		);
		add_shortcode(
			'pwb-brand',
			array(
				'\QuadLayers\PWB\Shortcodes\Brand',
				'brand_shortcode',
			)
		);
	}

	public function register_widgets() {
		register_widget( '\QuadLayers\PWB\Widgets\Brands_List' );
		register_widget( '\QuadLayers\PWB\Widgets\Brands_Dropdown' );
		register_widget( '\QuadLayers\PWB\Widgets\Filter_By_Brand' );
	}

	public static function show_brands_in_loop() {
		$brands_in_loop      = get_option( 'wc_pwb_admin_tab_brands_in_loop' );
		$image_size_selected = get_option( 'wc_pwb_admin_tab_brand_logo_size', 'thumbnail' );

		if ( 'brand_link' == $brands_in_loop || 'brand_image' == $brands_in_loop ) {

			global $product;
			$product_id     = $product->get_id();
			$product_brands = wp_get_post_terms( $product_id, 'pwb-brand' );
			if ( ! empty( $product_brands ) ) {
				echo '<div class="pwb-brands-in-loop">';
				foreach ( $product_brands as $brand ) {

					echo '<span>';
					$brand_link    = get_term_link( $brand->term_id, 'pwb-brand' );
					$attachment_id = get_term_meta( $brand->term_id, 'pwb_brand_image', 1 );

					$attachment_html = wp_get_attachment_image( $attachment_id, $image_size_selected );
					if ( ! empty( $attachment_html ) && 'brand_image' == $brands_in_loop ) {
						echo '<a href="' . esc_url( $brand_link ) . '">' . wp_kses_post( $attachment_html ) . '</a>';
					} else {
						if ( $brand !== $product_brands[0] ) {
							echo wp_kses_post( get_option( 'wc_pwb_admin_tab_brands_in_loop_separator', '' ) );
						}
						echo '<a href="' . esc_url( $brand_link ) . '">' . esc_html( $brand->name ) . '</a>';
					}
					echo '</span>';
				}
				echo '</div>';
			}
		}
	}

	/**
	 * Add brand logo to single product page.
	 *
	 * Hook woocommerce_single_product_summary.
	 *
	 * @hooked woocommerce_template_single_title - 5
	 * @hooked woocommerce_template_single_rating - 10
	 * @hooked woocommerce_template_single_price - 10
	 * @hooked woocommerce_template_single_excerpt - 20
	 * @hooked woocommerce_template_single_add_to_cart - 30
	 * @hooked woocommerce_template_single_meta - 40
	 * @hooked woocommerce_template_single_sharing - 50
	 */
	private function brand_logo_position() {
		$position          = 41;
		$position_selected = get_option( 'wc_pwb_admin_tab_brand_single_position' );
		if ( ! $position_selected ) {
			update_option( 'wc_pwb_admin_tab_brand_single_position', 'after_meta' );
		}

		switch ( $position_selected ) {
			case 'before_title':
				$position = 4;
				break;
			case 'after_title':
				$position = 6;
				break;
			case 'after_price':
				$position = 11;
				break;
			case 'after_excerpt':
				$position = 21;
				break;
			case 'after_add_to_cart':
				$position = 31;
				break;
			case 'after_meta':
				$position = 41;
				break;
			case 'after_sharing':
				$position = 51;
				break;
		}

		if ( 'meta' == $position_selected ) {
			add_action( 'woocommerce_product_meta_end', array( $this, 'action_woocommerce_single_product_summary' ) );
		} else {
			add_action( 'woocommerce_single_product_summary', array( $this, 'action_woocommerce_single_product_summary' ), $position );
		}
	}

	public function brand_desc_position() {
		if ( is_tax( 'pwb-brand' ) && ! is_paged() ) {

			$show_banner = get_option( 'wc_pwb_admin_tab_brand_banner' );
			$show_desc   = get_option( 'wc_pwb_admin_tab_brand_desc' );

			if ( ( ! $show_banner || 'yes' == $show_banner ) && ( ! $show_desc || 'yes' == $show_desc ) ) {
				/**
				 * Show brand banner and description before loop
				 */
				add_action( 'woocommerce_archive_description', array( $this, 'print_brand_banner_and_desc' ), 15 );
			} elseif ( 'yes_after_loop' == $show_banner && 'yes_after_loop' == $show_desc ) {
				/**
				 * Show brand banner and description after loop
				 */
				add_action( 'woocommerce_after_main_content', array( $this, 'print_brand_banner_and_desc' ), 9 );
			} else {
				/**
				 * Show banner and description independently
				 */
				if ( ! $show_banner || 'yes' == $show_banner ) {
					add_action( 'woocommerce_archive_description', array( $this, 'print_brand_banner' ), 15 );
				} elseif ( 'yes_after_loop' == $show_banner ) {
					add_action( 'woocommerce_after_main_content', array( $this, 'print_brand_banner' ), 9 );
				}

				if ( ! $show_desc || 'yes' == $show_desc ) {
					add_action( 'woocommerce_archive_description', array( $this, 'print_brand_desc' ), 15 );
				} elseif ( 'yes_after_loop' == $show_desc ) {
					add_action( 'woocommerce_after_main_content', array( $this, 'print_brand_desc' ), 9 );
				}
			}
		}
	}

	/**
	 * Maps shortcode (for visual composer plugin)
	 *
	 * @since 1.0
	 * @link https://vc.wpbakery.com/
	 * @return mixed
	 */
	public function vc_map_shortcodes() {
		$available_image_sizes_adapted = array();
		$available_image_sizes         = get_intermediate_image_sizes();

		foreach ( $available_image_sizes as $image_size ) {
			$available_image_sizes_adapted[ $image_size ] = $image_size;
		}

		vc_map(
			array(
				'name'        => __( 'PWB Product carousel', 'perfect-woocommerce-brands' ),
				'description' => __( 'Product carousel by brand or by category', 'perfect-woocommerce-brands' ),
				'base'        => 'pwb-product-carousel',
				'class'       => '',
				'icon'        => PWB_PLUGIN_URL . '/assets/backend/img/logo.jpg',
				'category'    => 'WooCommerce',
				'params'      => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Brand', 'perfect-woocommerce-brands' ),
						'param_name'  => 'brand',
						'admin_label' => true,
						'value'       => self::get_brands_array( true ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Products', 'perfect-woocommerce-brands' ),
						'param_name'  => 'products',
						'value'       => '10',
						'description' => __( 'Number of products to load', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Products to show', 'perfect-woocommerce-brands' ),
						'param_name'  => 'products_to_show',
						'value'       => '5',
						'description' => __( 'Number of products to show', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Products to scroll', 'perfect-woocommerce-brands' ),
						'param_name'  => 'products_to_scroll',
						'value'       => '1',
						'description' => __( 'Number of products to scroll', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'div',
						'heading'     => __( 'Autoplay', 'perfect-woocommerce-brands' ),
						'param_name'  => 'autoplay',
						'description' => __( 'Autoplay carousel', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'div',
						'heading'     => __( 'Arrows', 'perfect-woocommerce-brands' ),
						'param_name'  => 'arrows',
						'description' => __( 'Display prev and next arrows', 'perfect-woocommerce-brands' ),
					),
				),
			)
		);

		vc_map(
			array(
				'name'        => __( 'PWB Brands carousel', 'perfect-woocommerce-brands' ),
				'description' => __( 'Brands carousel', 'perfect-woocommerce-brands' ),
				'base'        => 'pwb-carousel',
				'class'       => '',
				'icon'        => PWB_PLUGIN_URL . '/assets/backend/img/logo.jpg',
				'category'    => 'WooCommerce',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Items', 'perfect-woocommerce-brands' ),
						'param_name'  => 'items',
						'value'       => '10',
						'description' => __( "Number of items to load (or 'featured')", 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Items to show', 'perfect-woocommerce-brands' ),
						'param_name'  => 'items_to_show',
						'value'       => '5',
						'description' => __( 'Number of items to show', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Items to scroll', 'perfect-woocommerce-brands' ),
						'param_name'  => 'items_to_scroll',
						'value'       => '1',
						'description' => __( 'Number of items to scroll', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'div',
						'heading'     => __( 'Autoplay', 'perfect-woocommerce-brands' ),
						'param_name'  => 'autoplay',
						'description' => __( 'Autoplay carousel', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'div',
						'heading'     => __( 'Arrows', 'perfect-woocommerce-brands' ),
						'param_name'  => 'arrows',
						'description' => __( 'Display prev and next arrows', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Brand logo size', 'perfect-woocommerce-brands' ),
						'param_name'  => 'image_size',
						'admin_label' => true,
						'value'       => $available_image_sizes_adapted,
					),
				),
			)
		);

		vc_map(
			array(
				'name'        => __( 'PWB All brands', 'perfect-woocommerce-brands' ),
				'description' => __( 'Show all brands', 'perfect-woocommerce-brands' ),
				'base'        => 'pwb-all-brands',
				'class'       => '',
				'icon'        => PWB_PLUGIN_URL . '/assets/backend/img/logo.jpg',
				'category'    => 'WooCommerce',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Brands per page', 'perfect-woocommerce-brands' ),
						'param_name'  => 'per_page',
						'value'       => '10',
						'description' => __( 'Show x brands per page', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Brand logo size', 'perfect-woocommerce-brands' ),
						'param_name'  => 'image_size',
						'admin_label' => true,
						'value'       => $available_image_sizes_adapted,
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order by', 'perfect-woocommerce-brands' ),
						'param_name'  => 'order_by',
						'admin_label' => true,
						'value'       => array(
							'name'        => 'name',
							'slug'        => 'slug',
							'term_id'     => 'term_id',
							'id'          => 'id',
							'description' => 'description',
							'rand'        => 'rand',
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Order', 'perfect-woocommerce-brands' ),
						'param_name'  => 'order',
						'admin_label' => true,
						'value'       => array(
							'ASC' => 'ASC',
							'DSC' => 'DSC',
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Title position', 'perfect-woocommerce-brands' ),
						'param_name'  => 'title_position',
						'admin_label' => true,
						'value'       => array(
							__( 'Before image', 'perfect-woocommerce-brands' ) => 'before',
							__( 'After image', 'perfect-woocommerce-brands' ) => 'after',
							__( 'Hide', 'perfect-woocommerce-brands' ) => 'none',
						),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'div',
						'heading'     => __( 'Hide empty', 'perfect-woocommerce-brands' ),
						'param_name'  => 'hide_empty',
						'description' => __( 'Hide brands that have not been assigned to any product', 'perfect-woocommerce-brands' ),
					),
				),
			)
		);

		vc_map(
			array(
				'name'        => __( 'PWB AZ Listing', 'perfect-woocommerce-brands' ),
				'description' => __( 'AZ Listing for brands', 'perfect-woocommerce-brands' ),
				'base'        => 'pwb-az-listing',
				'class'       => '',
				'icon'        => PWB_PLUGIN_URL . '/assets/backend/img/logo.jpg',
				'category'    => 'WooCommerce',
				'params'      => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Only parent brands', 'perfect-woocommerce-brands' ),
						'param_name'  => 'only_parents',
						'admin_label' => true,
						'value'       => array(
							esc_html__( 'No' )  => 'no',
							esc_html__( 'Yes' ) => 'yes',
						),
					),
				),
			)
		);

		vc_map(
			array(
				'name'        => __( 'PWB brand', 'perfect-woocommerce-brands' ),
				'description' => __( 'Show brand for a specific product', 'perfect-woocommerce-brands' ),
				'base'        => 'pwb-brand',
				'class'       => '',
				'icon'        => PWB_PLUGIN_URL . '/assets/backend/img/logo.jpg',
				'category'    => 'WooCommerce',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'heading'     => __( 'Product id', 'perfect-woocommerce-brands' ),
						'param_name'  => 'product_id',
						'value'       => null,
						'description' => __( 'Product id (post id)', 'perfect-woocommerce-brands' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Brand logo size', 'perfect-woocommerce-brands' ),
						'param_name'  => 'image_size',
						'admin_label' => true,
						'value'       => $available_image_sizes_adapted,
					),
				),
			)
		);
	}

	public function action_woocommerce_single_product_summary() {
		$brands = wp_get_post_terms( get_the_ID(), 'pwb-brand' );

		if ( ! is_wp_error( $brands ) ) {

			if ( count( $brands ) > 0 ) {

				$show_as = get_option( 'wc_pwb_admin_tab_brands_in_single' );

				if ( 'no' != $show_as ) {

					do_action( 'pwb_before_single_product_brands', $brands );

					echo '<div class="pwb-single-product-brands pwb-clearfix">';

					if ( 'brand_link' == $show_as ) {
						$label = apply_filters( 'pwb_text_before_brands_links', esc_html( _n( 'Brand', 'Brands', count( $brands ), 'perfect-woocommerce-brands' ) ), count( $brands ) );
						if ( $label ) {
							$before_brands_links  = '<span class="pwb-text-before-brands-links">';
							$before_brands_links .= $label;
							$before_brands_links .= ':</span>';
							echo wp_kses_post( apply_filters( 'pwb_html_before_brands_links', $before_brands_links ) );
						}
					}

					foreach ( $brands as $brand ) {
						$brand_link    = get_term_link( $brand->term_id, 'pwb-brand' );
						$attachment_id = get_term_meta( $brand->term_id, 'pwb_brand_image', 1 );

						$image_size          = 'thumbnail';
						$image_size_selected = get_option( 'wc_pwb_admin_tab_brand_logo_size', 'thumbnail' );
						if ( false != $image_size_selected ) {
							$image_size = $image_size_selected;
						}

						$attachment_html = wp_get_attachment_image( $attachment_id, $image_size );

						if ( ! empty( $attachment_html ) && 'brand_image' == $show_as || ! empty( $attachment_html ) && ! $show_as ) {
							echo '<a href="' . esc_url( $brand_link ) . '" title="' . esc_attr( $brand->name ) . '">' . $attachment_html . '</a>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							if ( $brand !== $brands[0] ) {
								echo wp_kses_post( get_option( 'wc_pwb_admin_tab_brands_in_loop_separator', '' ) );
							}
							echo '<a href="' . esc_url( $brand_link ) . '" title="' . esc_html__( 'View brand', 'perfect-woocommerce-brands' ) . '">' . esc_html( $brand->name ) . '</a>';
						}
					}
					echo '</div>';

					do_action( 'pwb_after_single_product_brands', $brands );
				}
			}
		}
	}

	public function enqueue_scripts() {

		wp_register_script(
			'pwb-lib-slick',
			PWB_PLUGIN_URL . '/assets/lib/slick/slick.min.js',
			array( 'jquery' ),
			'1.8.0',
			false
		);

		wp_register_style(
			'pwb-lib-slick',
			PWB_PLUGIN_URL . '/assets/lib/slick/slick.css',
			array(),
			'1.8.0',
			'all'
		);

		$frontend = include PWB_PLUGIN_DIR . 'build/frontend/js/index.asset.php';

		wp_enqueue_style(
			'pwb-styles-frontend',
			plugins_url( '/build/frontend/css/style.css', PWB_PLUGIN_FILE ),
			array(),
			PWB_PLUGIN_VERSION,
			'all'
		);

		wp_register_script(
			'pwb-functions-frontend',
			plugins_url( '/build/frontend/js/index.js', PWB_PLUGIN_FILE ),
			$frontend['dependencies'],
			$frontend['version'],
			true
		);

		wp_localize_script(
			'pwb-functions-frontend',
			'pwb_ajax_object',
			array(
				'carousel_prev' => apply_filters( 'pwb_carousel_prev', '&lt;' ),
				'carousel_next' => apply_filters( 'pwb_carousel_next', '&gt;' ),
			)
		);

		wp_enqueue_script( 'pwb-functions-frontend' );
	}

	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();

		if ( 'edit-tags.php' == $hook && 'pwb-brand' == $screen->taxonomy || 'term.php' == $hook && 'pwb-brand' == $screen->taxonomy ) {
			wp_enqueue_media();
		}

		$backend = include PWB_PLUGIN_DIR . 'build/backend/js/index.asset.php';

		wp_enqueue_style(
			'pwb-styles-admin',
			plugins_url( '/build/backend/css/style.css', PWB_PLUGIN_FILE ),
			array(),
			PWB_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'pwb-functions-admin',
			plugins_url( '/build/backend/js/index.js', PWB_PLUGIN_FILE ),
			$backend['dependencies'],
			$backend['version'],
			true
		);

		wp_localize_script(
			'pwb-functions-admin',
			'pwb_ajax_object_admin',
			array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'site_url'     => site_url(),
				'brands_url'   => admin_url( 'edit-tags.php?taxonomy=pwb-brand&post_type=product' ),
				'translations' => array(
					'migrate_notice'    => esc_html__( '¿Start migration?', 'perfect-woocommerce-brands' ),
					'migrating'         => esc_html__( 'We are migrating the product brands. ¡Don´t close this window until the process is finished!', 'perfect-woocommerce-brands' ),
					'dummy_data_notice' => esc_html__( '¿Start loading dummy data?', 'perfect-woocommerce-brands' ),
					'dummy_data'        => esc_html__( 'We are importing the dummy data. ¡Don´t close this window until the process is finished!', 'perfect-woocommerce-brands' ),
				),
				'nonce'        => array(
					'pwb_brands_export'              => wp_create_nonce( 'pwb_brands_export' ),
					'pwb_brands_import'              => wp_create_nonce( 'pwb_brands_import' ),
					'pwb_admin_set_featured_brand'   => wp_create_nonce( 'pwb_admin_set_featured_brand' ),
					'pwb_admin_save_screen_settings' => wp_create_nonce( 'pwb_admin_save_screen_settings' ),
					'pwb_admin_dummy_data'           => wp_create_nonce( 'pwb_admin_dummy_data' ),
					'pwb_admin_migrate_brands'       => wp_create_nonce( 'pwb_admin_migrate_brands' ),
					'pwb_system_status'              => wp_create_nonce( 'pwb_system_status' ),
				),
			)
		);
		wp_enqueue_script( 'pwb-functions-admin' );
	}

	public function register_brands_taxonomy() {
		$labels = array(
			'name'                       => esc_html__( 'Brands', 'perfect-woocommerce-brands' ),
			'singular_name'              => esc_html__( 'Brand', 'perfect-woocommerce-brands' ),
			'menu_name'                  => esc_html__( 'Brands', 'perfect-woocommerce-brands' ),
			'all_items'                  => esc_html__( 'All Brands', 'perfect-woocommerce-brands' ),
			'edit_item'                  => esc_html__( 'Edit Brand', 'perfect-woocommerce-brands' ),
			'view_item'                  => esc_html__( 'View Brand', 'perfect-woocommerce-brands' ),
			'update_item'                => esc_html__( 'Update Brand', 'perfect-woocommerce-brands' ),
			'add_new_item'               => esc_html__( 'Add New Brand', 'perfect-woocommerce-brands' ),
			'new_item_name'              => esc_html__( 'New Brand Name', 'perfect-woocommerce-brands' ),
			'parent_item'                => esc_html__( 'Parent Brand', 'perfect-woocommerce-brands' ),
			'parent_item_colon'          => esc_html__( 'Parent Brand:', 'perfect-woocommerce-brands' ),
			'search_items'               => esc_html__( 'Search Brands', 'perfect-woocommerce-brands' ),
			'popular_items'              => esc_html__( 'Popular Brands', 'perfect-woocommerce-brands' ),
			'separate_items_with_commas' => esc_html__( 'Separate brands with commas', 'perfect-woocommerce-brands' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove brands', 'perfect-woocommerce-brands' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used brands', 'perfect-woocommerce-brands' ),
			'not_found'                  => esc_html__( 'No brands found', 'perfect-woocommerce-brands' ),
		);

		$new_slug = get_option( 'wc_pwb_admin_tab_slug' );
		$old_slug = get_option( 'old_wc_pwb_admin_tab_slug' );

		$new_slug = ( false != $new_slug ) ? $new_slug : 'brand';
		$old_slug = ( false != $old_slug ) ? $old_slug : 'null';

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'query_var'         => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'         => apply_filters( 'pwb_taxonomy_rewrite', $new_slug ),
				'hierarchical' => true,
				'with_front'   => apply_filters( 'pwb_taxonomy_with_front', true ),
				'ep_mask'      => EP_PERMALINK,
			),
		);

		register_taxonomy( 'pwb-brand', array( 'product' ), $args );

		if ( false != $new_slug && false != $old_slug && $new_slug != $old_slug ) {
			flush_rewrite_rules();
			update_option( 'old_wc_pwb_admin_tab_slug', $new_slug );
		}
	}

	public function add_brands_metafields() {
		register_meta( 'term', 'pwb_brand_image', array( $this, 'add_brands_metafields_sanitize' ) );
	}

	public function add_brands_metafields_sanitize( $brand_img ) {
		return $brand_img;
	}

	public static function get_brands( $hide_empty = false, $order_by = 'name', $order = 'ASC', $only_featured = false, $pwb_term = false, $only_parents = false ) {
		$result = array();

		$brands_args = array(
			'hide_empty' => $hide_empty,
			'orderby'    => $order_by,
			'order'      => $order,
		);
		if ( $only_featured ) {
			$brands_args['meta_query'] = array(
				array(
					'key'   => 'pwb_featured_brand',
					'value' => true,
				),
			);
		}
		if ( $only_parents ) {
			$brands_args['parent'] = 0;
		}

		$brands = get_terms( 'pwb-brand', $brands_args );

		foreach ( $brands as $key => $brand ) {

			if ( $pwb_term ) {
				$brands[ $key ] = new Term( $brand );
			} else {
				$brand_image_id      = get_term_meta( $brand->term_id, 'pwb_brand_image', true );
				$brand_banner_id     = get_term_meta( $brand->term_id, 'pwb_brand_banner', true );
				$brand->brand_image  = wp_get_attachment_image_src( $brand_image_id );
				$brand->brand_banner = wp_get_attachment_image_src( $brand_banner_id );
			}
		}

		if ( is_array( $brands ) && count( $brands ) > 0 ) {
			$result = $brands;
		}

		return $result;
	}

	public static function get_brands_array( $is_select = false ) {
		$result = array();

		/**
		 * If is for select input adds default value
		 */
		if ( $is_select ) {
			$result[0] = esc_html__( 'All', 'perfect-woocommerce-brands' );
		}

		$brands = get_terms(
			'pwb-brand',
			array(
				'hide_empty' => false,
			)
		);

		foreach ( $brands as $brand ) {
			$result[ $brand->term_id ] = $brand->slug;
		}

		return $result;
	}

	public function print_brand_banner() {
		$queried_object    = get_queried_object();
		$brand_banner      = get_term_meta( $queried_object->term_id, 'pwb_brand_banner', true );
		$brand_banner_link = get_term_meta( $queried_object->term_id, 'pwb_brand_banner_link', true );
		$show_banner       = get_option( 'wc_pwb_admin_tab_brand_banner' );
		$show_banner       = get_option( 'wc_pwb_admin_tab_brand_banner' );
		$show_banner_class = ( ! $show_banner || 'yes' == $show_banner ) ? 'pwb-before-loop' : 'pwb-after-loop';

		if ( '' != $brand_banner ) {
			echo '<div class="pwb-brand-banner pwb-clearfix ' . esc_attr( $show_banner_class ) . '">';
			if ( '' != $brand_banner_link ) {
				echo '<a href="' . esc_url( site_url( $brand_banner_link ) ) . '">' . wp_get_attachment_image( $brand_banner, 'full', false ) . '</a>';
			} else {
				echo wp_get_attachment_image( $brand_banner, 'full', false );
			}
			echo '</div>';
		}
	}

	public function print_brand_desc() {
		$queried_object  = get_queried_object();
		$show_desc       = get_option( 'wc_pwb_admin_tab_brand_desc' );
		$show_desc       = get_option( 'wc_pwb_admin_tab_brand_desc' );
		$show_desc_class = ( ! $show_desc || 'yes' == $show_desc ) ? 'pwb-before-loop' : 'pwb-after-loop';

		if ( '' != $queried_object->description && 'no' !== $show_desc ) {
			echo '<div class="pwb-brand-description ' . esc_attr( $show_desc_class ) . '">';
			echo do_shortcode( apply_filters( 'the_content', $queried_object->description ) );
			echo '</div>';
		}
	}

	public function print_brand_banner_and_desc() {
		$queried_object = get_queried_object();

		$show_desc       = get_option( 'wc_pwb_admin_tab_brand_desc' );
		$show_desc_class = ( ! $show_desc || 'yes' == $show_desc ) ? 'pwb-before-loop' : 'pwb-after-loop';

		$brand_banner      = get_term_meta( $queried_object->term_id, 'pwb_brand_banner', true );
		$brand_banner_link = get_term_meta( $queried_object->term_id, 'pwb_brand_banner_link', true );

		if ( '' != $brand_banner || '' != $queried_object->description && 'no' !== $show_desc ) {
			echo '<div class="pwb-brand-banner-cont ' . esc_attr( $show_desc_class ) . '">';
			$this->print_brand_banner();
			$this->print_brand_desc();
			echo '</div>';
		}
	}

	public static function render_template( $name = '', $folder = '', $data = array(), $private = true ) {
		/**
		 * Default template
		 */
		if ( $folder ) {
			$folder = $folder . '/';
		}
		$template_file = dirname( __DIR__ ) . '/templates/' . $folder . $name . '.php';
		/**
		 * Theme overrides
		 */
		if ( ! $private ) {
			$theme_template_path = get_stylesheet_directory() . '/perfect-woocommerce-brands/';
			if ( file_exists( $theme_template_path . $folder . $name . '.php' ) ) {
				$template_file = $theme_template_path . $folder . $name . '.php';
			}
		}

		extract( $data );

		ob_start();
		include $template_file;
		return ob_get_clean();
	}

	public function product_duplicate_save( $duplicate, $product ) {
		$product_brands = wp_get_object_terms( $product->get_id(), 'pwb-brand', array( 'fields' => 'ids' ) );
		wp_set_object_terms( $duplicate->get_id(), $product_brands, 'pwb-brand' );
	}

	public function breadcrumbs( $crumbs ) {
		if ( is_tax( 'pwb-brand' ) ) {

			$brands_page_id = get_option( 'wc_pwb_admin_tab_brands_page_id' );

			if ( ! empty( $brands_page_id ) && '-' != $brands_page_id ) {

				$cur_brand       = get_queried_object();
				$brand_ancestors = get_ancestors( $cur_brand->term_id, 'pwb-brand', 'taxonomy' );

				$brand_page_pos = count( $crumbs ) - ( count( $brand_ancestors ) + 2 );
				if ( is_paged() ) {
					--$brand_page_pos;
				}

				if ( isset( $crumbs[ $brand_page_pos ][1] ) ) {
					$crumbs[ $brand_page_pos ][1] = get_page_link( $brands_page_id );
				}
			}
		}

		return $crumbs;
	}
}
