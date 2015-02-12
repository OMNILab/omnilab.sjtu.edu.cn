<?php
/**
 * The template for displaying all blogs.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package accesspress_parallax
 */

get_header(); ?>

<div class="mid-content">
	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part('content'); ?>
				<?php endwhile; ?>

				<?php accesspress_parallax_paging_nav(); ?>

			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>