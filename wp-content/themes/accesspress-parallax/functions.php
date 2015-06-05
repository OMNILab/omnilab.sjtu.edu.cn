<?php
/**
 * accesspress_parallax functions and definitions
 *
 * @package accesspress_parallax
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'accesspress_parallax_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function accesspress_parallax_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on accesspress_parallax, use a find and replace
	 * to change 'accesspress_parallax' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'accesspress_parallax', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/**
	 * Add callback for custom TinyMCE editor stylesheets. (editor-style.css)
	 * @see http://codex.wordpress.org/Function_Reference/add_editor_style
	 */
	add_editor_style('css/editor-style.css');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'blog-header', 900, 300, array('center','center')); //blog Image
	add_image_size( 'portfolio-thumbnail', 560, 450, array('center','center')); //Portfolio Image
    add_image_size( 'blog-thumbnail', 480, 300, array('center','center')); //Blog Image	
	add_image_size( 'team-thumbnail', 380, 380, array('top','center')); //Portfolio Image

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'accesspress_parallax' ),
	) );
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	//add_theme_support( 'post-formats', array(
	//	'aside', 'image', 'video', 'quote', 'link'
	//) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'accesspress_parallax_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // accesspress_parallax_setup
add_action( 'after_setup_theme', 'accesspress_parallax_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function accesspress_parallax_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'accesspress_parallax' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer One', 'accesspress_parallax' ),
		'id'            => 'footer-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Two', 'accesspress_parallax' ),
		'id'            => 'footer-2',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Three', 'accesspress_parallax' ),
		'id'            => 'footer-3',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Four', 'accesspress_parallax' ),
		'id'            => 'footer-4',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'accesspress_parallax_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function accesspress_parallax_scripts() {
	$query_args = array(
		'family' => 'Roboto:400,300,500,700|Oxygen:400,300,700',
	);
	wp_enqueue_style( 'accesspress_parallax-google-fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ) );
	wp_enqueue_style( 'accesspress_parallax-font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'accesspress_parallax-bx-slider', get_template_directory_uri() . '/css/jquery.bxslider.css' );
	wp_enqueue_style( 'accesspress_parallax-nivo-lightbox', get_template_directory_uri() . '/css/nivo-lightbox.css' );
	wp_enqueue_style( 'accesspress_parallax-animate', get_template_directory_uri() . '/css/animate.css' );
	wp_enqueue_style( 'accesspress_parallax-style', get_stylesheet_uri() );
	if(of_get_option('enable_responsive') == 1) :
		wp_enqueue_style( 'accesspress_parallax-responsive', get_template_directory_uri() . '/css/responsive.css' );
	endif;
	
	if (of_get_option('enable_animation') == '1' && is_front_page()) :
        wp_enqueue_script('accesspress_parallax-wow', get_template_directory_uri() . '/js/wow.js', array('jquery'), '1.0', true);
    endif;

	wp_enqueue_script( 'accesspress_parallax-smoothscroll', get_template_directory_uri() . '/js/SmoothScroll.js', array('jquery'), '1.2.1', true );
    wp_enqueue_script( 'accesspress_parallax-parallax', get_template_directory_uri() . '/js/parallax.js', array('jquery'), '1.1.3', true );
	wp_enqueue_script( 'accesspress_parallax-ScrollTo', get_template_directory_uri() . '/js/jquery.scrollTo.min.js', array('jquery'), '1.4.14', true );
	wp_enqueue_script( 'accesspress_parallax-local-scroll', get_template_directory_uri() . '/js/jquery.localScroll.min.js', array('jquery'), '1.3.5', true );
	wp_enqueue_script( 'accesspress_parallax-parallax-nav', get_template_directory_uri() . '/js/jquery.nav.js', array('jquery'), '2.2.0', true );
	wp_enqueue_script( 'accesspress_parallax-bx_slider', get_template_directory_uri() . '/js/jquery.bxslider.min.js', array('jquery'), '4.2.1', true );
	wp_enqueue_script( 'accesspress_parallax-easing', get_template_directory_uri() . '/js/jquery.easing.min.js', array('jquery'), '1.3', true );
	wp_enqueue_script( 'accesspress_parallax-fit-vid', get_template_directory_uri() . '/js/jquery.fitvids.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'accesspress_parallax-actual', get_template_directory_uri() . '/js/jquery.actual.min.js', array('jquery'), '1.0.16', true );
	wp_enqueue_script( 'accesspress_nivo-lightbox', get_template_directory_uri() . '/js/nivo-lightbox.min.js', array('jquery'), '1.2.0', true );
	wp_enqueue_script( 'accesspress_parallax-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	wp_enqueue_script( 'accesspress_parallax-custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '1.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'accesspress_parallax_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/accesspress-header.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/accesspress-functions.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Theme Option Frame work files
 */
require get_template_directory() . '/inc/options-framework/options-framework.php';

define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/options-framework/' );

function accesspress_ajax_script()
{
	 wp_localize_script( 'ajax_script_function', 'ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )) );
     wp_enqueue_script( 'ajax_script_function', get_template_directory_uri().'/inc/options-framework/js/ajax.js', 'jquery', true);

}
add_action('admin_enqueue_scripts', 'accesspress_ajax_script');

function accesspress_parallax_get_my_option()
{
	require get_template_directory() . '/inc/ajax.php';
	die();
}

add_action("wp_ajax_get_my_option", "accesspress_parallax_get_my_option");

/* add by honlan */
function honlan_features(){
	return '<div class="service-listing clearfix">
            <div class="clearfix service-list odd wow fadeInLeft" data-wow-delay="0.25s">
                <div class="service-image">
                    <img src="'.get_stylesheet_directory_uri().'/honlan/omnilab/icon-big-data.png" alt="MASSIVE DATA">
                </div>
                <div class="service-detail">
                    <h3>MASSIVE DATA</h3>
                    <div class="service-content"><p>Process the overwhelming data with Hadoop and Spark. Batch and streaming jobs are supported in a unified system.</p>
                    </div>
                </div>
            </div>
            <div class="clearfix service-list even wow fadeInRight" data-wow-delay="0.5s">
                <div class="service-image">
                    <img src="'.get_stylesheet_directory_uri().'/honlan/omnilab/icon-ml.png" alt="MING TECHNIQUES">
                </div>
                <div class="service-detail">
                    <h3>MINING TECHNIQUES</h3>
                    <div class="service-content"><p>Employ visualization and Machine Learning technologies to uncover gold from realistic data mine.</p>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="clearfix service-list odd wow fadeInLeft" data-wow-delay="0.75s">
                <div class="service-image">
                    <img src="'.get_stylesheet_directory_uri().'/honlan/omnilab/icon-monitor.png" alt="MEASUREMENT">
                </div>
                <div class="service-detail">
                    <h3>MEASUREMENT</h3>
                    <div class="service-content"><p>Ever wanted to know how the computer network works? Use data-drive analysis to inspect the dark side of our cyber space.</p>
                    </div>
                </div>
            </div>
            <div class="clearfix service-list even wow fadeInRight" data-wow-delay="1s">
                <div class="service-image">
                    <img src="'.get_stylesheet_directory_uri().'/honlan/omnilab/icon-mobile.png" alt="MOBILITY INSPECTION">
                </div>
                <div class="service-detail">
                    <h3>MOBILITY OF HUMAN</h3>
                    <div class="service-content"><p>Inspect the interesting patterns and universal laws of human behaviour in large-scale wireless networks.</p>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="clearfix service-list odd wow fadeInLeft" data-wow-delay="1.25s">
                <div class="service-image">
                    <img src="'.get_stylesheet_directory_uri().'/honlan/omnilab/icon-iot.png" alt="MACHINE NETWORKS">
                </div>
                <div class="service-detail">
                    <h3>MACHINE NETWORKS</h3>
                    <div class="service-content"><p>Heard about Internet of Things? The next opp comes from the connect of every machines and generated data mountains.</p>
                    </div>
                </div>
            </div>
            <div class="clearfix service-list even wow fadeInRight" data-wow-delay="1.5s">
                <div class="service-image">
                    <img src="'.get_stylesheet_directory_uri().'/honlan/omnilab/icon-team.png"
                         alt="MOTIVATED MAKERS">
                </div>
                <div class="service-detail">
                    <h3>MOTIVATED MAKERS</h3>
                    <div class="service-content"><p>Come to join a team of aspiring and enthusiastic makers to understand, to create, and to better our world.</p>
                    </div>
                </div>
            </div>
        </div><!-- #primary -->';
}
add_shortcode('honlan_features', 'honlan_features');

function computeOrder(){
	if (get_post_type()=="team") {
		$memberName = get_the_title();
		$memberDegree = get_post_meta(get_the_ID(), "wpcf-degree", true);
		$memberEntry = get_post_meta(get_the_ID(), "wpcf-entry-date", true);
		update_post_meta(get_the_ID(), "wpcf-sort", $memberDegree.$memberEntry.$memberName);
	}
}
add_action('the_post', 'computeOrder');
add_action('save_post', 'computeOrder');
add_action('draft_to_publish', 'computeOrder');
add_action('new_to_publish', 'computeOrder');
add_action('pending_to_publish', 'computeOrder');
add_action('future_to_publish', 'computeOrder');
