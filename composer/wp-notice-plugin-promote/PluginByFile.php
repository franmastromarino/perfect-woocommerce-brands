<?php

namespace QuadLayers\WP_License_Client\Models;

/**
 * Model_Plugin Class
 * This class handles plugin data based on plugin file and implements PluginBySlug
 *
 * @since 1.0.0
 */
class PluginByFile {

	/**
	 * Plugin file path
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Plugin author URL
	 *
	 * @var string
	 */
	private $plugin_url;

	/* TODO: crear las propiedades estaticas para cada setting del plugin y evitar que se llame mas de una vez a la funcion get_wp_plugin_data */

	/**
	 * Setup class
	 *
	 * @param string $plugin_file Plugin data.
	 */
	public function __construct( string $plugin_file ) {

		$this->plugin_file = $plugin_file;

		$this->try_get_plugin_file();
	}

	private function try_get_plugin_file() {
		
		/**
		 * If plugin file is set return it.
		 */
		if ( $this->plugin_file && is_file( $this->plugin_file ) ) {
			return $this->plugin_file;
		}

		/**
		 * If plugin file is not set, try to get it from current folder
		 */
		/* TODO: no estoy seguro si esto va a funcionar. Porque la libreria estaria adentro de vendor/quadlayers/xxxx */
		$plugin_basefolders = plugin_basename( __DIR__ );

		$plugin_filename = basename( __DIR__ ) . '.php';

		$plugin_basefolder = explode( '/', $plugin_basefolders );

		if ( ! isset( $plugin_basefolder[0] ) ) {
			return false;
		}

		$plugin_folder = $plugin_basefolder[0];

		$plugin_file = wp_normalize_path( WP_PLUGIN_DIR . '/' . $plugin_folder . '/' . $plugin_filename );

		if ( ! is_file( $plugin_file ) ) {
			return false;
		}

		$this->plugin_file = $plugin_file;

		return $plugin_file;
	}

	public function get_plugin_slug() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		$plugin_slug = basename( $this->get_plugin_file(), '.php' );
		return $plugin_slug;
	}

	public function is_valid() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		if ( ! is_file( $this->get_plugin_file() ) ) {
			return false;
		}
		return true;
	}

	public function get_plugin_base() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		$plugin_base = plugin_basename( $this->get_plugin_file() );
		return $plugin_base;
	}

	public function get_plugin_version() {
		$plugin_data = $this->get_wp_plugin_data( $this->get_plugin_file() );
		if ( empty( $plugin_data['Version'] ) ) {
			return false;
		}
		return $plugin_data['Version'];
	}

	public function get_plugin_name() {
		$plugin_data = $this->get_wp_plugin_data( $this->get_plugin_file() );
		if ( empty( $plugin_data['Name'] ) ) {
			return false;
		}
		return $plugin_data['Name'];
	}

	public function get_plugin_url() {

		if ( $this->plugin_url ) {
			return $this->plugin_url;
		}

		$plugin_data = $this->get_wp_plugin_data( $this->get_plugin_file() );

		if ( empty( $plugin_data['PluginURI'] ) ) {
			return false;
		}

		return $plugin_data['PluginURI'];
	}

	private function get_wp_plugin_data() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		return get_plugin_data( $this->get_plugin_file() );
	}

	public function get_plugin_file() {
		return $this->plugin_file;
	}
}
