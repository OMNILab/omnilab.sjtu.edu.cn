<?php
/**
 * The default template for displaying all pages.
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
		<main id="main" class="site-main" role="main">

			<?php
			global $page;
			if(of_get_option('enable_parallax') == "0" || is_singular()): ?>

				<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

			<?php endwhile; ?>
			<?php else:

 			echo wpautop($page->post_content);

			endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>