<?php
/**
 * Autoload classes, interfaces and traits for your namespaces.
 *
 * @package   wppunk/autoload
 * @author    WPPunk
 * @link      https://github.com/wppunk/wpautoload/
 * @copyright Copyright (c) 2020
 * @license   GPL-2.0+
 */

namespace QuadLayers\Autoload;

use Composer\Composer;
/**
 * @codeCoverageIgnore
 */
if ( class_exists( '\WPPunk\Autoload\Autoload' ) ) {
	return;
}
/**
 * @codingStandardsIgnoreEnd
 */

/**
 * Class Autoload
 *
 * @package wppunk/wpautoload
 */
class Autoload {

	/**
	 * Prefix for your namespace
	 *
	 * @var string
	 */
	private $prefix;
	/**
	 * Path to folder
	 *
	 * @var string
	 */
	private $folder;
	/**
	 * Cache
	 *
	 * @var Cache
	 */
	private $cache;

	/**
	 * Autoload constructor.
	 *
	 * @param string $prefix Prefix for your namespace.
	 * @param string $folder Path to folder.
	 * @param Cache  $cache  Cache.
	 */
	public function __construct( $prefix, $folder ) {

		$composer = new Composer();

		$test = $composer->getPackage()->getExtra();

		error_log('test: ' . json_encode($test,JSON_PRETTY_PRINT));

		$this->prefix = ltrim( $prefix, '\\' );
		$this->folder = $folder;
		spl_autoload_register( [ $this, 'autoload' ] );
	}

	/**
	 * Autoload files for custom plugins
	 *
	 * @param string $class Full class name.
	 *
	 * @throws Exception Class not found.
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, $this->prefix ) ) {
			return;
		}

		$path = $this->file_path( $class );

		require_once $path;
	}

	/**
	 * Find file path by namespace
	 *
	 * @param string $class Full class name.
	 *
	 * @return string
	 *
	 * @throws Exception Class not found.
	 */
	private function file_path( $class ) {
		$class        = str_replace( $this->prefix, '', $class );
		$plugin_parts = explode( '\\', $class );
		$name         = array_pop( $plugin_parts );
		$name         = preg_match( '/^(Interface|Trait)/', $name )
			? $name . '.php'
			: 'class-' . $name . '.php';
		$local_path   = implode( '/', $plugin_parts ) . '/' . $name;
		$local_path   = strtolower( str_replace( [ '\\', '_' ], [ '/', '-' ], $local_path ) );

		$path = $this->folder . '/' . $local_path;
		if ( file_exists( $path ) ) {
			return $path;
		}
		throw new \Exception( $class, $path );
	}

}