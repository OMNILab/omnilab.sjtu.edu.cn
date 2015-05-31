<?php 

/**
 * Enqueue scripts and styles.
 */
function stronghold_scripts() {
	wp_enqueue_style( 'stronghold-exo', stronghold_theme_font_url('Exo:700'), array(), 20141212 );
	wp_enqueue_style( 'stronghold-lora', stronghold_theme_font_url('Lora:400,400italic,700,700italic'), array(), 20141212 );
	wp_enqueue_style( 'stronghold-fa', get_template_directory_uri() . '/css/font-awesome.css', array(), 20150224 );
	wp_enqueue_style( 'stronghold-flex', get_template_directory_uri() . '/css/flexslider.css', array(), 20150224 );
	wp_enqueue_style( 'stronghold-style', get_stylesheet_uri() );

	wp_enqueue_script( 'stronghold-flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array('jquery'), '2.4.0', true );
	wp_enqueue_script( 'stronghold-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'stronghold-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	wp_enqueue_script( 'stronghold-custom', get_template_directory_uri() . '/js/custom.js', array(), '1.0.0', true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'stronghold_scripts' );

/**
 * Register Google fonts.
 *
 * @return string
 */
function stronghold_theme_font_url($font) {
	$font_url = '';
	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Font, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Font: on or off', 'stronghold' ) ) {
		$font_url = esc_url( add_query_arg( 'family', urlencode($font), "//fonts.googleapis.com/css" ) );
	}

	return $font_url;
}

function stronghold_admin_enqueue_scripts( $hook ) {
	if( strpos($hook, 'wbls_upgrade') ) {
		wp_enqueue_style( 
			'stronghold-fa', 
			get_template_directory_uri() . '/css/font-awesome.min.css', 
			array(), 
			'4.3.0', 
			'all' 
		);
		wp_enqueue_style( 
			'stronghold-admin-css', 
			get_template_directory_uri() . '/admin/css/admin.css', 
			array(), 
			'1.0.0', 
			'all' 
		);
	}
}
add_action( 'admin_enqueue_scripts', 'stronghold_admin_enqueue_scripts' );