<?php

namespace MovieList;

defined( 'ABSPATH' ) || die();

class Taxonomy {
	public function __construct() {
		add_action( 'init', [ $this, 'registerArtistsTaxonomy' ], 0 );
		add_action( 'init', [ $this, 'registerGenreTaxonomy' ], 0 );
	}

	/**
	 * Register Artist taxonomy
	 */
	function registerArtistsTaxonomy(): void {
		$labels = array(
			'name'                       => 'Artists',
			'singular_name'              => 'Artist',
			'menu_name'                  => 'Artists',
			'all_items'                  => 'All Items',
			'parent_item'                => 'Parent Item',
			'parent_item_colon'          => 'Parent Item:',
			'new_item_name'              => 'New Item Name',
			'add_new_item'               => 'Add New Item',
			'edit_item'                  => 'Edit Item',
			'update_item'                => 'Update Item',
			'view_item'                  => 'View Item',
			'separate_items_with_commas' => 'Separate items with commas',
			'add_or_remove_items'        => 'Add or remove items',
			'choose_from_most_used'      => 'Choose from the most used',
			'popular_items'              => 'Popular Items',
			'search_items'               => 'Search Items',
			'not_found'                  => 'Not Found',
			'no_terms'                   => 'No items',
			'items_list'                 => 'Items list',
			'items_list_navigation'      => 'Items list navigation',
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'artist', array( 'movie' ), $args );
	}

	/**
	 * Register Genre taxonomy
	 */
	function registerGenreTaxonomy(): void {
		$labels = array(
			'name'                       => 'Genres',
			'singular_name'              => 'Genre',
			'menu_name'                  => 'Genres',
			'all_items'                  => 'All Items',
			'parent_item'                => 'Parent Item',
			'parent_item_colon'          => 'Parent Item:',
			'new_item_name'              => 'New Item Name',
			'add_new_item'               => 'Add New Item',
			'edit_item'                  => 'Edit Item',
			'update_item'                => 'Update Item',
			'view_item'                  => 'View Item',
			'separate_items_with_commas' => 'Separate items with commas',
			'add_or_remove_items'        => 'Add or remove items',
			'choose_from_most_used'      => 'Choose from the most used',
			'popular_items'              => 'Popular Items',
			'search_items'               => 'Search Items',
			'not_found'                  => 'Not Found',
			'no_terms'                   => 'No items',
			'items_list'                 => 'Items list',
			'items_list_navigation'      => 'Items list navigation',
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'genre', array( 'movie' ), $args );
	}
}