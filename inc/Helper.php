<?php

namespace MovieList;

defined( 'ABSPATH' ) || die();

class Helper {
	/**
	 * Numeric pagination via WP core function paginate_links().
	 * @link http://codex.wordpress.org/Function_Reference/paginate_links
	 *
	 * @param array $args
	 *
	 * @return string HTML for numneric pagination
	 */
	public static function pagination( $args = array() ) {
		global $wp_query;
		$output = '';

		$pagination_args = array(
//			'base'               => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'total'              => $wp_query->max_num_pages,
			'current'            => max( 1, get_query_var( 'paged' ) ),
			'format'             => '?paged=%#%',
			'show_all'           => false,
			'type'               => 'plain',
			'end_size'           => 2,
			'mid_size'           => 1,
			'prev_text'          => '&laquo; Prev',
			'next_text'          => 'Next &raquo;',
			'add_args'           => false,
			'add_fragment'       => '',

			// Custom arguments not part of WP core:
			'show_page_position' => false, // Optionally allows the "Page X of XX" HTML to be displayed.
		);

		$pagination_args = wp_parse_args( $args, $pagination_args );

		if ( $pagination_args['total'] <= 1 )
			return '';

		$output .= '<div class="movie-pagination">';
		$output .= paginate_links( $pagination_args );

		// Optionally, show Page X of XX.
		if ( true == $pagination_args['show_page_position'] && $wp_query->max_num_pages > 0 ) {
			$output .= '<span class="page-of-pages">' .
			           sprintf( __( 'Page %1s of %2s', 'text-domain' ), $pagination_args['current'], $wp_query->max_num_pages ) .
			           '</span>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Validate taxonomy terms
	 *
	 * @param array|string $terms Taxonomy terms
	 * @param string $taxonomy Taxonomy name
	 *
	 * @return array Taxonomy term IDs
	 */
	public static function validateTermList( $terms, $taxonomy ): array {
		if ( ! is_array( $terms ) )
			$terms = explode( ',', $terms );

		$termIDs = [];
		foreach ( $terms as $termID ) {
			if ( is_numeric( $termID ) )
				$termIDs[] = intval( $termID );

			elseif ( is_string( $termID ) ) {
				$term = get_term_by( 'slug', $termID, $taxonomy );

				if ( $term )
					$termIDs[] = $term->term_id;
			}
		}

		return $termIDs;
	}
}