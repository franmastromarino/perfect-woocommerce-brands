<?php

namespace Perfect_Woocommerce_Brands\Admin;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class PWB_Suggestions {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'add_redirect' ) );
		add_action( 'admin_head', array( $this, 'remove_menu' ) );
		add_filter( 'network_admin_url', array( $this, 'network_admin_url' ), 10, 2 );
	}

	// Admin
	// -------------------------------------------------------------------------

	public function add_page() {
		include_once PWB_PLUGIN_DIR . 'classes/class-pwb-suggestions-list-table.php';
		?>
			<style>
				@media screen and (max-width: 2299px) and (min-width: 1600px) {
					#the-list {
						display: flex;
						flex-wrap: wrap;
					}

					.plugin-card {
						margin: 8px !important;
						width: calc(50% - 4px - 16px) !important;
					}
				}
			</style>
			<div class="wrap about-wrap full-width-layout">

			<h1><?php esc_html_e( 'Suggestions', 'perfect-woocommerce-brands' ); ?></h1>

			<p class="about-text"><?php printf( esc_html__( 'Thanks for using our product! We recommend these extensions that will add new features to stand out your business and improve your sales.', 'perfect-woocommerce-brands' ), esc_html( PWB_PLUGIN_NAME ) ); ?></p>

			<p class="about-text">
					<?php printf( '<a href="%s" target="_blank">%s</a>', esc_html( PWB_PURCHASE_URL ), esc_html__( 'Purchase', 'perfect-woocommerce-brands' ) ); ?></a> |
					<?php printf( '<a href="%s" target="_blank">%s</a>', esc_html( PWB_DOCUMENTATION_URL ), esc_html__( 'Documentation', 'perfect-woocommerce-brands' ) ); ?></a>
			</p>
					<?php
						printf(
							'<a href="%s" target="_blank"><div style="
								background: #006bff url(%s) no-repeat;
								background-position: top center;
								background-size: 130px 130px;
								color: #fff;
								font-size: 14px;
								text-align: center;
								font-weight: 600;
								margin: 5px 0 0;
								padding-top: 120px;
								height: 40px;
								display: inline-block;
								width: 140px;
								" class="wp-badge">%s</div></a>',
							'https://quadlayers.com/?utm_source=pwb_admin',
							esc_url( plugins_url( '/assets/img/quadlayers.jpg', PWB_PLUGIN_FILE ) ),
							esc_html__( 'QuadLayers', 'perfect-woocommerce-brands' )
						);
					?>

			</div>
			<div class="wrap" style="
			position: relative;
			margin: 25px 40px 0 20px;
			max-width: 1200px;">
					<?php
					$wp_list_table = new \Perfect_Woocommerce_Brands\PWB_Suggestions_List_Table();
					$wp_list_table->prepare_items();
					?>
			<form id="plugin-filter" method="post" class="importer-item">
					<?php $wp_list_table->display(); ?>
			</form>
			</div>
		<?php
	}

	public function add_menu() {
		add_menu_page( PWB_PLUGIN_NAME, PWB_PLUGIN_NAME, 'manage_woocommerce', PWB_PREFIX, array( $this, 'add_page' ) );
		add_submenu_page( PWB_PREFIX, esc_html__( 'Suggestions', 'perfect-woocommerce-brands' ), esc_html__( 'Suggestions', 'perfect-woocommerce-brands' ), 'manage_woocommerce', PWB_PREFIX . '_suggestions', array( $this, 'add_page' ) );
	}

	// fix for activateUrl on install now button
	public function network_admin_url( $url, $path ) {
		if ( wp_doing_ajax() && ! is_network_admin() ) {
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'install-plugin' ) {
				if ( strpos( $url, 'plugins.php' ) !== false ) {
					$url = self_admin_url( $path );
				}
			}
		}

		return $url;
	}

	public function add_redirect() {
		if ( isset( $_REQUEST['activate'] ) && $_REQUEST['activate'] == 'true' ) {
			if ( wp_get_referer() == admin_url( 'admin.php?page=' . PWB_PREFIX . '_suggestions' ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=' . PWB_PREFIX . '_suggestions' ) );
				exit();
			}
		}
	}

	public function remove_menu() {
		?>
			<style>
				li.toplevel_page_<?php echo esc_attr( PWB_PREFIX ); ?> {
					display: none;
				}
			</style>
		<?php
	}
}
