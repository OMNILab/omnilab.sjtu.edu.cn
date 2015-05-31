<?php
/**
 * @package stronghold
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		$stronghold = get_theme_mods(); 
	if( ! isset($stronghold['single_featured_image']) && has_post_thumbnail() ) : ?>
		<div class="post-thumb">
			<?php the_post_thumbnail(); ?>
		</div>
	<?php else : 
			$single_featured_image = get_theme_mod( 'single_featured_image' );
			if( $single_featured_image && has_post_thumbnail() ) : ?>
		<div class="post-thumb">
			<?php the_post_thumbnail(); ?>
		</div>			
		<?php 
			endif;  
		endif; ?>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php stronghold_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages: ', 'stronghold' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php stronghold_entry_footer(); ?>
	</footer><!-- .entry-footer -->
	<?php stronghold_post_nav(); ?>
</article><!-- #post-## -->
