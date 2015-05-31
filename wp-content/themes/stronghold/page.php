<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package stronghold
 */

get_header(); ?>
<div class="row">
		<div class="sixteen columns breadcrumb">
			<?php
			$breadcrumb = get_theme_mod( 'breadcrumb' );
				if( $breadcrumb ) : ?>
				<?php stronghold_breadcrumbs(); ?>
			<?php endif; ?>
		</div>
	</div>

	<div id="primary" class="content-area eleven columns">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
