<?php

namespace MovieList;

// Check run from WP
defined( 'ABSPATH' ) || die();

class Install {
	/**
	 * Install plugin needed
	 * To Create plugin tables
	 * Add default options
	 *
	 * @return void
	 */
	public static function install(): void {
		flush_rewrite_rules();
	}
}