<?php

namespace Perfect_Woocommerce_Brands;

require_once ABSPATH . 'wp-admin/includes/class-wp-plugin-install-list-table.php';

class PWB_Suggestions_List_Table extends \WP_Plugin_Install_List_Table {


	public $promote = array(
		'woocommerce-checkout-manager',
		'woocommerce-direct-checkout',
		'wp-whatsapp-chat',
		'quadlayers-telegram-chat',
		'wp-tiktok-feed',
		'insta-gallery',
		'quadmenu',
	);

	private function remove_plugins( $plugins ) {
		$promote = array();

		foreach ( $this->promote as $order => $slug ) {

			if ( $id = @max( array_keys( array_column( $plugins, 'slug' ), $slug ) ) ) {

				$promote[] = $plugins[ $id ];
			}
		}

		return $promote;
	}

	public function self_admin_url( $url, $path ) {
		if ( strpos( $url, 'tab=plugin-information' ) !== false ) {
			$url = network_admin_url( $path );
		}

		return $url;
	}

	public function network_admin_url( $url, $path ) {
		if ( strpos( $url, 'plugins.php' ) !== false ) {
			$url = self_admin_url( $path );
		}

		return $url;
	}

	public function display_rows() {
		add_filter( 'self_admin_url', array( $this, 'self_admin_url' ), 10, 2 );
		add_filter( 'network_admin_url', array( $this, 'network_admin_url' ), 10, 2 );
		parent::display_rows();
	}

	public function is_connected() {
		global $wp_version;

		$http_args = array(
			'timeout'    => 15,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);

		return is_wp_error( wp_remote_get( 'http://api.wordpress.org/plugins/info/1.2/', $http_args ) );
	}

	public function get_plugins() {
		$tk = PWB_PREFIX . '_suggestions_plugins';

		$plugins = get_transient( $tk );

		if ( $plugins === false ) {

			$args = array(
				'per_page' => 36,
				'author'   => 'quadlayers',
				'locale'   => get_user_locale(),
			);

			$api = plugins_api( 'query_plugins', $args );

			if ( ! is_wp_error( $api ) ) {

				$plugins = $this->remove_plugins( $api->plugins );

				set_transient( $tk, $plugins, 24 * HOUR_IN_SECONDS );
			}
		}

		return $plugins;
	}

	public function prepare_items() {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		global $tabs, $tab;

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'updates' );
		// wp_localize_script('updates', 'pagenow', array('plugin-install-network'))

		wp_reset_vars( array( 'tab' ) );

		$tabs = array();

		if ( 'search' === $tab ) {
			$tabs['search'] = esc_html__( 'Search Results' );
		}
		if ( $tab === 'beta' || false !== strpos( get_bloginfo( 'version' ), '-' ) ) {
			$tabs['beta'] = _x( 'Beta Testing', 'Plugin Installer' );
		}
		$tabs['featured']    = _x( 'Featured', 'Plugin Installer' );
		$tabs['popular']     = _x( 'Popular', 'Plugin Installer' );
		$tabs['recommended'] = _x( 'Recommended', 'Plugin Installer' );
		$tabs['favorites']   = _x( 'Favorites', 'Plugin Installer' );

		$nonmenu_tabs = array( 'plugin-information' ); // Valid actions to perform which do not have a Menu item.

		$tabs = apply_filters( 'install_plugins_tabs', $tabs );

		$nonmenu_tabs = apply_filters( 'install_plugins_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And it's not a non-menu action.
		if ( empty( $tab ) || ( ! isset( $tabs[ $tab ] ) && ! in_array( $tab, (array) $nonmenu_tabs ) ) ) {
			$tab = key( $tabs );
		}

		$this->items = $this->get_plugins();

		wp_localize_script(
			'updates',
			'_wpUpdatesItemCounts',
			array(
				'totals' => wp_get_update_data(),
			)
		);
	}
}
