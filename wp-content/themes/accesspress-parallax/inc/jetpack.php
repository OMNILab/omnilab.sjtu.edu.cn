<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package accesspress_parallax
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function accesspress_parallax_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'accesspress_parallax_jetpack_setup' );
