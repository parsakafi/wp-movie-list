<?php

namespace MovieList;

class Hooks {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueueAssets' ] );
		add_filter( 'wp_dropdown_cats', [ $this, 'dropdownCatsMultiple' ], 10, 2 );
	}

	/**
	 * Add multiple feature to wp_dropdown_cats
	 *
	 * Source: https://wordpress.stackexchange.com/a/261094
	 */
	function dropdownCatsMultiple( $output, $r ) {
		if ( isset( $r['multiple'] ) && $r['multiple'] ) {
			$output = preg_replace( '/^<select/i', '<select multiple="multiple"', $output );
			$output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
			foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value ) {
				$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
			}
		}

		return $output;
	}

	/**
	 * Enqueue assets (script and style)
	 */
	function enqueueAssets(): void {
		$pluginVersion = MOVIELIST_PLUGIN_VERSION . ( defined( 'DEVELOPMENT_MODE' ) && DEVELOPMENT_MODE ? time() : '' );

		// Style
		wp_enqueue_style( MOVIELIST_PLUGIN_KEY . '-select2-style',
			plugins_url( '/assets/css/select2.min.css', MOVIELIST_PLUGIN_FILE_PATH ),
			false, '4.1.0' );

		wp_enqueue_style( MOVIELIST_PLUGIN_KEY . '-front-style',
			plugins_url( '/assets/css/style.css', MOVIELIST_PLUGIN_FILE_PATH ),
			false, $pluginVersion );

		// Scripts
		wp_enqueue_script( MOVIELIST_PLUGIN_KEY . '-select2-script',
			plugins_url( '/assets/js/select2.min.js', MOVIELIST_PLUGIN_FILE_PATH ),
			false, '4.1.0', [ 'in_footer' => true ] );

		wp_enqueue_script( MOVIELIST_PLUGIN_KEY . '-front-script',
			plugins_url( '/assets/js/script.js', MOVIELIST_PLUGIN_FILE_PATH ),
			[ MOVIELIST_PLUGIN_KEY . '-select2-script', 'jquery' ], $pluginVersion, [ 'in_footer' => true ] );

		wp_localize_script( MOVIELIST_PLUGIN_KEY . '-front-script', 'movieListVars', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( MOVIELIST_PLUGIN_KEY . current_time( 'd' ) )
		) );
	}
}