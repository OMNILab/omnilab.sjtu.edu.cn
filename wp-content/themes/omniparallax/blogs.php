<?php
/**
 * The template file for displaying all blog entries.
 *
 * @package accesspress_parallax
 */
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

        </main>
    </div>

    <?php get_sidebar() ?>
</div>

