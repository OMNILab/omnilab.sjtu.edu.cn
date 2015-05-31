<?php
/**
 * The template for displaying all single posts.
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

			<?php get_template_part( 'content', 'single' ); ?>

			

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
	