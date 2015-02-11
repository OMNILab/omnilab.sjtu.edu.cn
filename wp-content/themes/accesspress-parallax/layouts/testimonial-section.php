<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="testimonial-listing clearfix wow fadeInUp">
	<?php 
		$args = array(
			'cat' => $category,
			'posts_per_page' => -1
			);
		$query = new WP_Query($args);
		if($query->have_posts()): ?>
		<div class="testimonial-slider">
		<?php
			while($query->have_posts()): $query->the_post();
		?>

		<div class="testimonial-list">
			
			<div class="testimonial-content"><?php the_content(); ?></div>
			<h3><?php the_title(); ?></h3>
			<?php if(has_post_thumbnail()) : 
			$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'thumbnail');
			?>
			<div class="testimonial-image">
				<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>">
			</div>
			<?php endif; ?>

		</div>

		<?php
			endwhile;
			wp_reset_postdata();
		?>
		</div>
		<?php
		endif;
	?>
	</div><!-- #primary -->


