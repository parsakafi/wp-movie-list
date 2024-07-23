<?php

namespace MovieList;

use WP_Query;

defined( 'ABSPATH' ) || die();

class Content {
	public function __construct() {
		add_filter( 'the_content', [ $this, 'addTermsToContent' ], 9999999999 );
		add_shortcode( 'movies', [ $this, 'moviesShortcode' ] );
		add_action( 'wp_ajax_movie_list_search', [ $this, 'searchRequest' ] );
		add_action( 'wp_ajax_nopriv_movie_list_search', [ $this, 'searchRequest' ] );
	}

	/**
	 * Handle ajax search request
	 */
	function searchRequest(): void {
		$nonce   = sanitize_text_field( $_POST['nonce'] );
		$artists = sanitize_text_field( $_POST['artists'] );
		$genres  = sanitize_text_field( $_POST['genres'] );
		$paged   = intval( $_POST['paged'] );
		$perPage = intval( $_POST['per_page'] );

		if ( wp_verify_nonce( $nonce, MOVIELIST_PLUGIN_KEY . current_time( 'd' ) ) ) {
			$result = do_shortcode( '[movies artists="' . $artists . '" genres="' . $genres . '" paged="' . $paged . '" per_page="' . $perPage . '" output="result"]' );
			wp_send_json_success( [ 'result' => $result ] );
		}

		wp_send_json_error();
	}

	/**
	 * Movie archive shortcode
	 *
	 * @param array $atts Shortcode parameters
	 *
	 * @return string Movie archive shortcode html
	 */
	function moviesShortcode( $atts ): string {
		global $post;
		$temp = $post;

		$atts = shortcode_atts( array(
			'genres'   => '',
			'artists'  => '',
			'per_page' => 6,
			'paged'    => 1,
			'output'   => 'all'
		), $atts, 'movies' );

		$perPage = intval( $atts['per_page'] );
		if ( ! $perPage || $perPage < 1 )
			$perPage = 6;

		$paged = intval( $atts['paged'] );
		if ( ! $paged || $paged < 1 )
			$paged = 1;

		$movieArgs = array(
			'post_type'           => 'movie',
			'post_status'         => 'publish',
			'posts_per_page'      => $perPage,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
			'tax_query'           => [ 'relation' => 'AND' ]
		);

		$genreIDs = Helper::validateTermList( $atts['genres'], 'genre' );
		if ( ! empty( $genreIDs ) ) {
			$movieArgs['tax_query'][] = array(
				'taxonomy' => 'genre',
				'field'    => 'term_id',
				'terms'    => $genreIDs
			);
		}

		$artistIDs = Helper::validateTermList( $atts['artists'], 'artist' );
		if ( ! empty( $artistIDs ) ) {
			$movieArgs['tax_query'][] = array(
				'taxonomy' => 'artist',
				'field'    => 'term_id',
				'terms'    => $artistIDs
			);
		}

		$movieQuery = new WP_Query( $movieArgs );
		$content    = '';

		if ( $atts['output'] != 'result' ) {
			$content .= '<div class="movie-filter"><input type="hidden" id="movie_per_page" value="' . $perPage . '"><input type="hidden" id="movie_paged" value="' . $paged . '">';
			$content .= '<div><label for="movie_artist">Artist</label><div>';
			$content .= wp_dropdown_categories(
				[
					'taxonomy' => 'artist',
					'name'     => 'movie_artist',
					'class'    => 'movie-select2',
					'selected' => implode( ',', $artistIDs ),
					'multiple' => true,
					'echo'     => false,
				]
			);
			$content .= '</div></div>';

			$content .= '<div><label for="movie_genre">Genre</label><div>';
			$content .= wp_dropdown_categories(
				[
					'taxonomy' => 'genre',
					'name'     => 'movie_genre',
					'class'    => 'movie-select2',
					'selected' => implode( ',', $genreIDs ),
					'multiple' => true,
					'echo'     => false,
				]
			);
			$content .= '</div></div>';

			$content .= '</div>';
		}

		$content .= '<div class="movie-result"><div class="movie-loader"></div>';

		if ( $movieQuery->have_posts() ) {
			$content .= '<div class="movie-list">';
			while ( $movieQuery->have_posts() ) {
				$movieQuery->the_post();
				$title = get_the_title();

				if ( has_post_thumbnail() )
					$image = get_the_post_thumbnail( null, 'medium_large', [ 'alt' => $title ] );
				else
					$image = '<img src="' . plugins_url( '/assets/images/movie.png', MOVIELIST_PLUGIN_FILE_PATH ) . '" alt="' . $title . '">';

				$content .= '<a href="' . get_the_permalink() . '" class="movie-card">';
				$content .= '<div class="movie-image">' . $image . '</div>';
				$content .= '<strong class="movie-title">' . $title . '</strong>';
				$content .= '</a>';
			}

			$content .= '</div>';

			$content .= Helper::pagination( [
				'total'     => $movieQuery->max_num_pages,
				'current'   => $paged,
				'prev_text' => false,
				'next_text' => false
			] );
		} else {
			$content .= '<div class="movie-not-found">Your search returned no results.</div>';
		}
		$content .= '</div>';

		wp_reset_query();
		$post = $temp;

		return $atts['output'] == 'result' ? $content : '<div class="movie-wrapper">' . $content . '</div>';
	}


	/**
	 * Add genre and artist to movie single post content
	 *
	 * @param string $content Content string
	 *
	 * @return string Changed content
	 */
	function addTermsToContent( $content ) {
		if ( ! is_singular( 'movie' ) )
			return $content;

		$postID      = get_the_ID();
		$artists     = wp_get_post_terms( $postID, 'artist' );
		$genres      = wp_get_post_terms( $postID, 'genre' );
		$artistLinks = $genreLinks = [];

		if ( is_array( $artists ) && ! empty( $artists ) ) {
			foreach ( $artists as $artist ) {
				$artistLinks[] = '<a href="' . get_term_link( $artist, 'artist' ) . '">' . $artist->name . '</a>';
			}
		}

		if ( is_array( $genres ) && ! empty( $genres ) ) {
			foreach ( $genres as $genre ) {
				$genreLinks[] = '<a href="' . get_term_link( $genre, 'artist' ) . '">' . $genre->name . '</a>';
			}
		}

		if ( ! empty( $artistLinks ) || ! empty( $genreLinks ) ) {
			$content .= '<div class="movie-taxonomy-terms">';
			if ( ! empty( $artistLinks ) )
				$content .= '<strong>Artists:</strong> ' . implode( ', ', $artistLinks ) . '<br />';
			if ( ! empty( $genreLinks ) )
				$content .= '<strong>Genres:</strong> ' . implode( ', ', $genreLinks );
			$content .= '</div>';
		}

		return $content;
	}
}