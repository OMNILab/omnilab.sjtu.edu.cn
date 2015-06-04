<?php
/**
 * @package accesspress_parallax
 */
?>
<?php 
$post_date = of_get_option('post_date');
$post_footer = of_get_option('post_footer');
$post_date_class = ($post_date != 1 || has_post_thumbnail()) ? " no-date" : "";
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title<?php echo $post_date_class; ?>"><a href="<?php echo home_url(); ?>/researches/#post-<?php the_ID(); ?>"><?php the_title(); ?></a></h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'accesspress_parallax' ) ); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'accesspress_parallax' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<?php if(has_post_thumbnail()) : ?>
	<div class="entry-thumb portfolio-image wow fadeInUp" data-wow-delay="0.25s">
		<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'medium' ); ?>
		<a href="<?php echo home_url(); ?>/researches/#post-<?php the_ID(); ?>"><div class="overlay"></div><img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(); ?>"></a> 
	</div>
	<?php endif; ?>

	<footer class="entry-footer">
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'accesspress_parallax' ) );
				if ( $categories_list && accesspress_parallax_categorized_blog() ) :
			?>
			<span class="cat-links">
				<?php printf( __( '<i class="fa fa-folder-open"></i> %1$s', 'accesspress_parallax' ), $categories_list ); ?>
			</span>
			<?php endif; // End if categories ?>

			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'accesspress_parallax' ) );
				if ( $tags_list ) :
			?>
			<span class="tags-links">
				<?php printf( __( '<i class="fa fa-tags"></i> %1$s', 'accesspress_parallax' ), $tags_list ); ?>
			</span>
			<?php endif; // End if $tags_list ?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link"><?php comments_popup_link( __( '<i class="fa fa-comments"></i>Leave a comment', 'accesspress_parallax' ), __( '<i class="fa fa-comments"></i>1 Comment', 'accesspress_parallax' ), __( '<i class="fa fa-comments"></i>% Comments', 'accesspress_parallax' ) ); ?></span>
		<?php endif; ?>
	</footer><!-- .entry-footer -->
	<?php edit_post_link( __( '<i class="fa fa-pencil-square-o"></i>Edit', 'accesspress_parallax' ), '<span class="edit-link">', '</span>' ); ?>
</article><!-- #post-## -->