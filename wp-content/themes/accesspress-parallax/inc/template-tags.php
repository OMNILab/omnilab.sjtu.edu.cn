<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package accesspress_parallax
 */

if ( ! function_exists( 'accesspress_parallax_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function accesspress_parallax_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'accesspress_parallax' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<i class="fa fa-hand-o-left"></i> Older Posts', 'accesspress_parallax' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer Posts <i class="fa fa-hand-o-right"></i>', 'accesspress_parallax' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'accesspress_parallax_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function accesspress_parallax_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'accesspress_parallax' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<i class="fa fa-hand-o-left"></i>%title', 'Previous post link', 'accesspress_parallax' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title<i class="fa fa-hand-o-right"></i>', 'Next post link',     'accesspress_parallax' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'accesspress_parallax_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function accesspress_parallax_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s"><span class="posted-day">%2$s</span><span class="posted-month">%3$s</span></time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '<time class="updated" datetime="%4$s">%5$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( 'd' ) ),
		esc_html( get_the_date( 'M' ) ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = $time_string ;

	$byline = sprintf(
		_x( 'By %s', 'post author', 'accesspress_parallax' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	$post_date = of_get_option('post_date');
	$post_author = of_get_option('post_author');
	if($post_date ==  1 || empty($post_date)) :
	echo '<span class="posted-on">' . $posted_on . '</span>';
	endif;

	if($post_author ==  1 || empty($post_author)) :
	echo '<span class="byline"> ' . $byline . '</span>';
	endif;

}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function accesspress_parallax_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'accesspress_parallax_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'accesspress_parallax_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so accesspress_parallax_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so accesspress_parallax_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in accesspress_parallax_categorized_blog.
 */
function accesspress_parallax_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'accesspress_parallax_categories' );
}
add_action( 'edit_category', 'accesspress_parallax_category_transient_flusher' );
add_action( 'save_post',     'accesspress_parallax_category_transient_flusher' );
