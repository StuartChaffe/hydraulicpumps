<?php
/**
 * Breadcrumb trail for the current page, as a list of items, each
 * array( 'title' => ..., 'url' => ... ). The last item is the current page
 * (no url). Empty on the home page.
 *
 * Pages: Home > parent pages > page
 * Posts: Home > blog page (if set) > post
 * Other post types: Home > archive (if it has one) > post
 * Archives, search and 404: Home > title
 */
function origin_breadcrumbs() {
	if ( is_front_page() ) {
		return array();
	}

	$items = array( array( 'title' => __( 'Home', 'origin' ), 'url' => home_url( '/' ) ) );

	if ( is_singular() ) {
		$post = get_queried_object();

		if ( is_page() ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
				$items[] = array( 'title' => get_the_title( $ancestor ), 'url' => get_permalink( $ancestor ) );
			}
		} elseif ( $post->post_type === 'post' ) {
			$blog = (int) get_option( 'page_for_posts' );
			if ( $blog ) {
				$items[] = array( 'title' => get_the_title( $blog ), 'url' => get_permalink( $blog ) );
			}
		} else {
			$type = get_post_type_object( $post->post_type );
			$link = get_post_type_archive_link( $post->post_type );
			if ( $type && $link ) {
				$items[] = array( 'title' => $type->labels->name, 'url' => $link );
			}
		}

		$items[] = array( 'title' => get_the_title( $post ), 'url' => '' );
	} elseif ( is_home() ) {
		$items[] = array( 'title' => get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'News', 'origin' ), 'url' => '' );
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$items[] = array( 'title' => sprintf( __( 'Search results for “%s”', 'origin' ), get_search_query() ), 'url' => '' );
	} elseif ( is_404() ) {
		$items[] = array( 'title' => __( 'Page not found', 'origin' ), 'url' => '' );
	} elseif ( is_archive() ) {
		$items[] = array( 'title' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
	}

	return $items;
}
