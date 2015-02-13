<?php
/**
 * The main landing page of our site.
 *
 * @package omniparallax
 */

get_header(); ?>
<?php
    if( of_get_option('enable_parallax') == 1 || of_get_option('enable_parallax') == NULL):
        get_template_part('index', 'parallax');
    else:
        get_template_part('blogs');
    endif;
?>

<?php get_footer(); ?>