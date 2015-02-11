<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="service-listing clearfix">
	<?php 
		$args = array(
			'cat' => $category,
			'posts_per_page' => -1
			);
		$count_service = 0;
		$query = new WP_Query($args);
		if($query->have_posts()):
			$i = 0;
            while ($query->have_posts()): $query->the_post();
            $i = $i + 0.25;
			$count_service++;
			$service_class = ($count_service % 2 == 0) ? "even wow fadeInRight" : "odd wow fadeInLeft";
		?>

		<div class="clearfix service-list <?php echo $service_class; ?>" data-wow-delay="<?php echo $i; ?>s">
			<div class="service-image">
				<?php if(has_post_thumbnail()) : 
				$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'thumbnail'); ?>
					<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>">
				<?php else: ?>
					<img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpg" alt="<?php the_title(); ?>">
				<?php endif; ?>
			</div>

			<div class="service-detail">
				<h3><?php the_title(); ?></h3>
				<div class="service-content"><?php the_content(); ?></div>
			</div>
		</div>

		<?php 
		if($count_service % 2 == 0): ?>
			<div class="clearfix"></div>
		<?php endif;
		?>

		<?php
			endwhile;
			wp_reset_postdata();
		endif;
	?>
	</div><!-- #primary -->



