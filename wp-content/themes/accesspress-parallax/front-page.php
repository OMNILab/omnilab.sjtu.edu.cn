<?php
/**
 * The main landing page of our site.
 *
 * @package accesspress_parallax
 */

get_header(); ?>
<?php if( of_get_option('enable_parallax') == 1 || of_get_option('enable_parallax') == NULL):
    get_template_part('index','parallax');
else:
?>
<div class="mid-content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php if ( have_posts() ) : ?>

                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content' ); ?>
                <?php endwhile; ?>

                <?php accesspress_parallax_paging_nav(); ?>

            <?php else : ?>
                <?php get_template_part( 'content', 'none' ); ?>
            <?php endif; ?>

        </main><!-- #main -->
    </div><!-- #primary -->

    <?php
    get_sidebar();
    endif;
    ?>
</div>

<?php get_footer(); ?>
