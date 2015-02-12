<?php
/**
 * Template Name: Wide Page
 *
 * @package accesspress_parallax
 */

get_header(); ?>

<div class="mid-content">
	<main id="main" class="site-main" role="main">

		<?php global $page;
		if (of_get_option('enable_parallax') == "0" || is_singular()): ?>

			<?php while (have_posts()) : the_post(); ?>
				<?php get_template_part('content', 'page'); ?>
			<?php endwhile; // end of the loop. ?>

		<?php else:
			echo wpautop($page->post_content);
		endif; ?>

	</main><!-- #main -->
</div>

<?php get_footer(); ?>