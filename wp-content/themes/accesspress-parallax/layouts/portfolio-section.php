<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="portfolio-listing clearfix">
	<?php 
		$args = array(
			'cat' => $category,
			'posts_per_page' => -1
			);
		$count_service = 0;
		$query = new WP_Query($args);
		if($query->have_posts()):
			$i = 0;
			while($query->have_posts()): $query->the_post();
			$i = $i + 0.25;
		?>

		<a href="<?php the_permalink(); ?>" class="portfolio-list wow fadeInUp" data-wow-delay="<?php echo $i; ?>s">
		<div class="portfolio-overlay"><span>+</span></div>
			<div class="portfolio-image">
			<?php if(has_post_thumbnail()) : 
			$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'portfolio-thumbnail'); ?>
				<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>">
			<?php else: ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpg" alt="<?php the_title(); ?>">
			<?php endif; ?>
			</div>
			<h3><?php the_title(); ?></h3>
		</a>

		<?php
			endwhile;
			wp_reset_postdata();
		endif;
	?>
	</div><!-- #primary -->



