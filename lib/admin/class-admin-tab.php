<?php

namespace QuadLayers\PWB\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Admin_Tab {

	protected $id;

	public function __construct() {

		$this->id = 'pwb_admin_tab';

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_tab' ), 50 );
		add_filter( 'woocommerce_sections_' . $this->id, array( $this, 'add_tabs' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'add_settings' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save_settings' ) );
		add_action( 'admin_footer', array( __CLASS__, 'add_premium_css' ) );

		add_filter(
			'pwb_admin_settings_tabs',
			function( $tabs ) {

				$tabs = array(
					''               => __( 'General', 'perfect-woocommerce-brands' ),
					'archives'       => __( 'Shop & Categories', 'perfect-woocommerce-brands' ),
					'taxonomy-brand' => __( 'Brands', 'perfect-woocommerce-brands' ),
					'products'       => __( 'Products', 'perfect-woocommerce-brands' ),
					'tools'          => __( 'Tools', 'perfect-woocommerce-brands' ),
					array(
						'title'  => __( 'Documentation', 'perfect-woocommerce-brands' ),
						'href'   => PWB_DOCUMENTATION_URL,
						'target' => '_blank',
					),
					array(
						'title'  => __( 'Premium', 'perfect-woocommerce-brands' ),
						'href'   => PWB_PREMIUM_SELL_URL,
						'target' => '_blank',
					),
					array(
						'title'  => __( 'Suggestions', 'perfect-woocommerce-brands' ),
						'href'   => admin_url( 'admin.php?page=wc-settings_suggestions' ),
						'target' => '_blank',
					),
				);

				return $tabs;
			}
		);

		add_filter(
			'pwb_admin_settings_fields',
			function( $fields ) {

				global $current_section;

				$available_image_sizes_adapted = array();
				$available_image_sizes         = get_intermediate_image_sizes();
				foreach ( $available_image_sizes as $image_size ) {
					$available_image_sizes_adapted[ $image_size ] = $image_size;
				}
				$available_image_sizes_adapted['full'] = 'full';

				$pages_select_adapted = array( '-' => '-' );
				$pages_select         = get_pages();
				foreach ( $pages_select as $page ) {
					$pages_select_adapted[ $page->ID ] = $page->post_title;
				}

				switch ( $current_section ) {
					case 'archives':
						$fields = apply_filters(
							'wc_pwb_admin_tab_archives_settings',
							array(
								'section_title' => array(
									'name' => __( 'Shop & Categories', 'perfect-woocommerce-brands' ),
									'type' => 'title',
									'desc' => '',
									'id'   => 'wc_pwb_admin_tab_section_title',
								),
								array(
									'name'    => __( 'Show brands in loop', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field',
									'desc'    => __( 'Show brand logo (or name) in product loop', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brands_in_loop',
									'options' => array(
										'no'          => __( 'No', 'perfect-woocommerce-brands' ),
										'brand_link'  => __( 'Show brand link', 'perfect-woocommerce-brands' ),
										'brand_image' => __( 'Show brand image (if is set)', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'  => __( 'Brands in loop separator', 'perfect-woocommerce-brands' ),
									'type'  => 'text',
									'class' => 'pwb-admin-tab-field pwb-premium-field',
									'desc'  => __( 'Show separator between brands', 'perfect-woocommerce-brands' ),
									'id'    => 'wc_pwb_admin_tab_brands_in_loop_separator',
								),
								array(
									'name'    => __( 'Show brands in loop hook', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'desc'    => __( 'Show brand logo (or name) in product loop hook', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brands_in_loop_hook',
									'options' => array(
										'before_shop_loop_item'       => __( 'Before shop loop item', 'perfect-woocommerce-brands' ),
										'before_shop_loop_item_title' => __( 'Before shop loop item title', 'perfect-woocommerce-brands' ),
										'shop_loop_item_title'        => __( 'Shop loop item title', 'perfect-woocommerce-brands' ),
										'after_shop_loop_item_title'  => __( 'After shop loop item title', 'perfect-woocommerce-brands' ),
										'after_shop_loop_item'        => __( 'After shop loop item', 'perfect-woocommerce-brands' ),
									),
									'default' => 'after_shop_loop_item_title',
								),
								array(
									'name'    => __( 'Show brands in loop hook order', 'perfect-woocommerce-brands' ),
									'type'    => 'number',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'desc'    => __( 'Show brand logo (or name) in product loop hook order', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brands_in_loop_hook_order',
									'default' => 9,
								),
								'section_end'   => array(
									'type' => 'sectionend',
									'id'   => 'wc_pwb_admin_tab_section_end',
								),
							)
						);
						break;
					case 'taxonomy-brand':
						$fields = apply_filters(
							'wc_pwb_admin_tab_brand_pages_settings',
							array(
								'section_title' => array(
									'name' => __( 'Archives', 'perfect-woocommerce-brands' ),
									'type' => 'title',
									'desc' => '',
									'id'   => 'wc_pwb_admin_tab_section_title',
								),
								array(
									'name'    => __( 'Show brand title', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'default' => 'yes',
									'desc'    => __( 'Show brand title (if is set) on brand archive page', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brand_title',
									'options' => array(
										'yes' => __( 'Yes, before product loop', 'perfect-woocommerce-brands' ),
										'no'  => __( 'No, hide title', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'    => __( 'Show brand description', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field',
									'default' => 'yes',
									'desc'    => __( 'Show brand description (if is set) on brand archive page', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brand_desc',
									'options' => array(
										'yes'            => __( 'Yes, before product loop', 'perfect-woocommerce-brands' ),
										'yes_after_loop' => __( 'Yes, after product loop', 'perfect-woocommerce-brands' ),
										'no'             => __( 'No, hide description', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'    => __( 'Show long brand description', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'default' => 'no',
									'desc'    => __( 'Show long brand description (if is set) on brand archive page', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_long_brand_desc',
									'options' => array(
										'yes'            => __( 'Yes, before product loop', 'perfect-woocommerce-brands' ),
										'yes_after_loop' => __( 'Yes, after product loop', 'perfect-woocommerce-brands' ),
										'no'             => __( 'No, hide description', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'    => __( 'Show brand banner', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field',
									'default' => 'yes',
									'desc'    => __( 'Show brand banner (if is set) on brand archive page', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brand_banner',
									'options' => array(
										'yes'            => __( 'Yes, before product loop', 'perfect-woocommerce-brands' ),
										'yes_after_loop' => __( 'Yes, after product loop', 'perfect-woocommerce-brands' ),
										'no'             => __( 'No, hide banner', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'        => __( 'Columns', 'perfect-woocommerce-brands' ),
									'type'        => 'number',
									'class'       => 'pwb-admin-tab-field pwb-premium-field',
									'placeholder' => get_option( 'woocommerce_catalog_columns', 4 ),
									'desc'        => __( 'Number of columns in the brand page', 'perfect-woocommerce-brands' ),
									'id'          => 'wc_pwb_admin_tab_brand_columns',
								),
								array(
									'name'        => __( 'Show brands in loop', 'perfect-woocommerce-brands' ),
									'type'        => 'select',
									'class'       => 'pwb-admin-tab-field pwb-premium-field',
									'desc'        => __( 'Show brand logo (or name) in product loop', 'perfect-woocommerce-brands' ),
									'id'          => 'wc_pwb_admin_tab_archives_brand_in_loop',
									'options'     => array(
										'no'          => __( 'No', 'perfect-woocommerce-brands' ),
										'brand_link'  => __( 'Show brand link', 'perfect-woocommerce-brands' ),
										'brand_image' => __( 'Show brand image (if is set)', 'perfect-woocommerce-brands' ),
									),
									'placeholder' => get_option( 'wc_pwb_admin_tab_brands_in_loop', 'no' ),
								),
								array(
									'name'        => __( 'Brands in loop separator', 'perfect-woocommerce-brands' ),
									'type'        => 'text',
									'class'       => 'pwb-admin-tab-field pwb-premium-field',
									'desc'        => __( 'Show separator between brands', 'perfect-woocommerce-brands' ),
									'id'          => 'wc_pwb_admin_tab_archives_brand_in_loop_separator',
									'placeholder' => get_option( 'wc_pwb_admin_tab_brands_in_loop_separator', '' ),
								),
								array(
									'name'    => __( 'Show brands in loop hook', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'desc'    => __( 'Show brand logo (or name) in product loop hook', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_archives_brand_in_loop_hook',
									'options' => array(
										'before_shop_loop_item' => __( 'Before shop loop item', 'perfect-woocommerce-brands' ),
										'before_shop_loop_item_title' => __( 'Before shop loop item title', 'perfect-woocommerce-brands' ),
										'after_shop_loop_item'  => __( 'After shop loop item', 'perfect-woocommerce-brands' ),
										'after_shop_loop_item_title' => __( 'After shop loop item title', 'perfect-woocommerce-brands' ),
									),
									'default' => 'after_shop_loop_item_title',
								),
								array(
									'name'    => __( 'Show brands in loop hook order', 'perfect-woocommerce-brands' ),
									'type'    => 'number',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'desc'    => __( 'Show brand logo (or name) in product loop hook order', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_archives_brand_in_loop_hook_order',
									'default' => 9,
								),
								'section_end'   => array(
									'type' => 'sectionend',
									'id'   => 'wc_pwb_admin_tab_section_end',
								),
							)
						);
						break;
					case 'products':
						$fields = apply_filters(
							'wc_pwb_admin_tab_settings',
							array(
								'section_title' => array(
									'name' => __( 'Products', 'perfect-woocommerce-brands' ),
									'type' => 'title',
									'desc' => '',
									'id'   => 'wc_pwb_admin_tab_section_title',
								),
								array(
									'name'    => __( 'Products tab', 'perfect-woocommerce-brands' ),
									'type'    => 'checkbox',
									'default' => 'yes',
									'desc'    => __( 'Show brand tab in single product page', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brand_single_product_tab',
								),
								array(
									'name'    => __( 'Show brands in single product', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field',
									'desc'    => __( 'Show brand logo (or name) in single product', 'perfect-woocommerce-brands' ),
									'default' => 'brand_image',
									'id'      => 'wc_pwb_admin_tab_brands_in_single',
									'options' => array(
										'no'          => __( 'No', 'perfect-woocommerce-brands' ),
										'brand_link'  => __( 'Show brand link', 'perfect-woocommerce-brands' ),
										'brand_image' => __( 'Show brand image (if is set)', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'    => __( 'Brand position', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field',
									'desc'    => __( 'For single product', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brand_single_position',
									'options' => array(
										'before_title'  => __( 'Before title', 'perfect-woocommerce-brands' ),
										'after_title'   => __( 'After title', 'perfect-woocommerce-brands' ),
										'after_price'   => __( 'After price', 'perfect-woocommerce-brands' ),
										'after_excerpt' => __( 'After excerpt', 'perfect-woocommerce-brands' ),
										'after_add_to_cart' => __( 'After add to cart', 'perfect-woocommerce-brands' ),
										'meta'          => __( 'In meta', 'perfect-woocommerce-brands' ),
										'after_meta'    => __( 'After meta', 'perfect-woocommerce-brands' ),
										'after_sharing' => __( 'After sharing', 'perfect-woocommerce-brands' ),
									),
								),
								array(
									'name'  => __( 'Brands in single product separator', 'perfect-woocommerce-brands' ),
									'type'  => 'text',
									'class' => 'pwb-admin-tab-field pwb-premium-field',
									'desc'  => __( 'Show separator between brands', 'perfect-woocommerce-brands' ),
									'id'    => 'wc_pwb_admin_tab_brands_in_single_separator',
								),
								array(
									'name'        => __( 'Brands label', 'perfect-woocommerce-brands' ),
									'type'        => 'text',
									'class'       => 'pwb-admin-tab-field pwb-premium-field',
									'placeholder' => esc_html__( 'Brands', 'perfect-woocommerce-brands' ),
									'desc'        => __( 'Change or disable the brands label in the single products page.', 'perfect-woocommerce-brands' ),
									'desc_tip'    => __( 'Display label for brand links.', 'perfect-woocommerce-brands' ),
									'id'          => 'wc_pwb_admin_tab_brand_single_label',
								),
								array(
									'name'    => __( 'Brands breadcrumb', 'perfect-woocommerce-brands' ),
									'type'    => 'select',
									'class'   => 'pwb-admin-tab-field pwb-premium-field',
									'desc'    => __( 'Include brand in product breadcrumb', 'perfect-woocommerce-brands' ),
									'id'      => 'wc_pwb_admin_tab_brand_single_breadcrumbs',
									'options' => array(
										'no'      => __( 'No', 'perfect-woocommerce-brands' ),
										'yes'     => __( 'Yes', 'perfect-woocommerce-brands' ),
										'replace' => __( 'Replace category', 'perfect-woocommerce-brands' ),
									),
								),
								'section_end'   => array(
									'type' => 'sectionend',
									'id'   => 'wc_pwb_admin_tab_section_end',
								),
							)
						);
						break;
					case 'tools':
							$fields = apply_filters(
								'wc_pwb_admin_tab_tools_settings',
								array(
									'section_title' => array(
										'name' => __( 'Tools', 'perfect-woocommerce-brands' ),
										'type' => 'title',
										'desc' => '',
										'id'   => 'wc_pwb_admin_tab_section_tools_title',
									),
									array(
										'name'    => __( 'Import brands', 'perfect-woocommerce-brands' ),
										'type'    => 'select',
										'class'   => 'pwb-admin-tab-field',
										'desc'    => sprintf(
											__( 'Import brands from other brand plugin. <a href="%s" target="_blank">Click here for more details</a>', 'perfect-woocommerce-brands' ),
											str_replace( '/?', '/brands/?', PWB_DOCUMENTATION_URL )
										),
										'id'      => 'wc_pwb_admin_tab_tools_migrate',
										'options' => array(
											'-'         => __( '-', 'perfect-woocommerce-brands' ),
											'yith'      => __( 'YITH WooCommerce Brands Add-On', 'perfect-woocommerce-brands' ),
											'ultimate'  => __( 'Ultimate WooCommerce Brands', 'perfect-woocommerce-brands' ),
											'woobrands' => __( 'Offical WooCommerce Brands', 'perfect-woocommerce-brands' ),
										),
									),
									array(
										'name'    => __( 'Dummy data', 'perfect-woocommerce-brands' ),
										'type'    => 'select',
										'class'   => 'pwb-admin-tab-field',
										'desc'    => __( 'Import generic brands and assign it to products randomly', 'perfect-woocommerce-brands' ),
										'id'      => 'wc_pwb_admin_tab_tools_dummy_data',
										'options' => array(
											'-'            => __( '-', 'perfect-woocommerce-brands' ),
											'start_import' => __( 'Start import', 'perfect-woocommerce-brands' ),
										),
									),
									array(
										'name' => __( 'System status', 'perfect-woocommerce-brands' ),
										'type' => 'textarea',
										'desc' => __( 'Show system status', 'perfect-woocommerce-brands' ),
										'id'   => 'wc_pwb_admin_tab_tools_system_status',
									),
									'section_end'   => array(
										'type' => 'sectionend',
										'id'   => 'wc_pwb_admin_tab_section_tools_end',
									),
								)
							);
						break;
					default:
						$brands_url = get_option( 'wc_pwb_admin_tab_slug', __( 'brands', 'perfect-woocommerce-brands' ) ) . '/' . __( 'brand-name', 'perfect-woocommerce-brands' ) . '/';

						$fields = apply_filters(
							'wc_pwb_admin_tab_product_settings',
							array(
								'section_title' => array(
									'name' => __( 'General', 'perfect-woocommerce-brands' ),
									'type' => 'title',
									'desc' => '',
									'id'   => 'wc_pwb_admin_tab_section_title',
								),
								array(
									'name'        => __( 'Slug', 'perfect-woocommerce-brands' ),
									'type'        => 'text',
									'class'       => 'pwb-admin-tab-field',
									'desc'        => __( 'Brands taxonomy slug', 'perfect-woocommerce-brands' ),
									'desc_tip'    => sprintf(
										__( 'Your brands URLs will look like "%s"', 'perfect-woocommerce-brands' ),
										'https://site.com/' . $brands_url
									),
									'id'          => 'wc_pwb_admin_tab_slug',
									'placeholder' => get_taxonomy( 'pwb-brand' )->rewrite['slug'],
								),
								array(
									'name'     => __( 'Brand logo size', 'perfect-woocommerce-brands' ),
									'type'     => 'select',
									'class'    => 'pwb-admin-tab-field',
									'desc'     => __( 'Select the size for the brand logo image around the site', 'perfect-woocommerce-brands' ),
									'desc_tip' => __( 'The default image sizes can be configured under "Settings > Media". You can also define your own image sizes', 'perfect-woocommerce-brands' ),
									'id'       => 'wc_pwb_admin_tab_brand_logo_size',
									'options'  => $available_image_sizes_adapted,
								),
								array(
									'name'     => __( 'Brands page', 'perfect-woocommerce-brands' ),
									'type'     => 'select',
									'class'    => 'pwb-admin-tab-field pwb-admin-selectwoo',
									'desc'     => __( 'For linking breadcrumbs', 'perfect-woocommerce-brands' ),
									'desc_tip' => __( 'Select your "Brands" page (if you have one), it will be linked in the breadcrumbs.', 'perfect-woocommerce-brands' ),
									'id'       => 'wc_pwb_admin_tab_brands_page_id',
									'options'  => $pages_select_adapted,
								),
								array(
									'name'     => __( 'Brands search', 'perfect-woocommerce-brands' ),
									'type'     => 'select',
									'class'    => 'pwb-admin-tab-field pwb-premium-field',
									'desc'     => __( 'Better search experience', 'perfect-woocommerce-brands' ),
									'desc_tip' => __( 'Redirect if the search matchs with a brands name.', 'perfect-woocommerce-brands' ),
									'id'       => 'wc_pwb_admin_tab_brands_search',
									'options'  => array(
										'no'  => __( 'No', 'perfect-woocommerce-brands' ),
										'yes' => __( 'Yes', 'perfect-woocommerce-brands' ),
									),
								),
								'section_end'   => array(
									'type' => 'sectionend',
									'id'   => 'wc_pwb_admin_tab_section_end',
								),
							)
						);
						break;
				}

				return $fields;
			}
		);
	}

	public function add_tab( $settings_tabs ) {
		$settings_tabs[ $this->id ] = __( 'Brands', 'perfect-woocommerce-brands' );
		return $settings_tabs;
	}

	public function add_tabs( $tabs = array() ) {

		global $current_section;

		$tabs = apply_filters( 'pwb_admin_settings_tabs', array() );

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $tabs );

		foreach ( $tabs as $id => $tab ) {

			$target = '_self';
			if ( is_string( $tab ) ) {
				$title = $tab;
				$href  = admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) );
			} else {
				$title = $tab['title'];
				$href  = $tab['href'];
				if ( isset( $tab['target'] ) ) {
					$target = $tab['target'];
				}
			}

			echo '<li><a target="' . esc_attr( $target ) . '" href="' . esc_url( $href ) . '" class="' . ( $current_section === $id ? 'current' : '' ) . '">' . esc_attr( $title ) . '</a> ' . ( end( $array_keys ) === $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}

	public function add_settings() {

		global $current_section;
		//phpcs:ignore:WordPress.Security.NonceVerification
		woocommerce_admin_fields( $this->get_settings() );

		if ( 'taxonomy-brand' == $current_section ) {
			?>
				<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=pwb-brand&post_type=product' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Edit brands', 'perfect-woocommerce-brands' ); ?></a>
			<?php
		}
	}

	public function save_settings() {
		update_option( 'old_wc_pwb_admin_tab_slug', get_taxonomy( 'pwb-brand' )->rewrite['slug'] );

		//phpcs:ignore:WordPress.Security.NonceVerification
		if ( isset( $_POST['wc_pwb_admin_tab_slug'] ) ) {
			$_POST['wc_pwb_admin_tab_slug'] = sanitize_title( wp_unslash( $_POST['wc_pwb_admin_tab_slug'] ) );//phpcs:ignore:WordPress.Security.NonceVerification		
		}
		woocommerce_update_options( $this->get_settings() );
	}

	public function get_settings() {
		$fields = apply_filters( 'pwb_admin_settings_fields', array() );
		return $fields;
	}

	public static function add_premium_css() {
		?>
		<style>
			.pwb-premium-field {
				opacity: 0.5; 
				pointer-events: none;
			}
			.pwb-premium-field .description {
				display: block!important;
			}
		</style>
		<script>
			const fields = document.querySelectorAll('.pwb-premium-field')
			Array.from(fields).forEach((field)=> {
				field.closest('tr')?.classList.add('pwb-premium-field');
			})
		</script>
		<?php
	}
}
