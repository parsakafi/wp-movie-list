<?php
/*!
 * Plugin Name: Movie List
 * Description: The Movie List plugin displays movies along with their genres and artists.
 * Version: 1.0
 * Plugin URI: https://parsa.ws
 * Author: Parsa Kafi
 * Author URI: https://parsa.ws
 */

namespace MovieList;

defined( 'ABSPATH' ) || die();

define( 'MOVIELIST_PLUGIN_KEY', 'movie-list' );
define( 'MOVIELIST_PLUGIN_FILE_PATH', __FILE__ );

class MovieList {
	public function __construct() {
		$this->includes();

		$version = '1.0';
		if ( function_exists( 'get_plugin_data' ) ) {
			$pluginData = get_plugin_data( MOVIELIST_PLUGIN_FILE_PATH );
			$version    = $pluginData['Version'];
		}
		define( 'MOVIELIST_PLUGIN_VERSION', $version );

		new PostType();
		new Taxonomy();
		new Content();
		new Hooks();
	}

	/**
	 * Include the required files.
	 */
	private function includes(): void {
		include dirname( __FILE__ ) . '/vendor/autoload.php';
	}
}

new MovieList();
register_activation_hook( __FILE__, array( 'MovieList\Install', 'install' ) );