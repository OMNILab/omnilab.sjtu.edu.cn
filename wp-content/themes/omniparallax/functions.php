<?php
/**
 * accesspress_parallax_omnilab functions and definitions
 *
 * @package accesspress_parallax_omnilab
 */

/**
 * Enqueue the parent and child theme stylesheets.
 */
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}