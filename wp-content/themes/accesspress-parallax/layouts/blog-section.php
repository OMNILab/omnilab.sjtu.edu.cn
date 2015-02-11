<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="blog-listing clearfix">
	<?php 
		$args = array(
			'cat' => $category,
			'posts_per_page' => 3
			);
		$count_service = 0;
		$query = new WP_Query($args);
		if($query->have_posts()):
			$i = 0;
			while($query->have_posts()): $query->the_post();
				$i = $i + 0.25;
		?>

		<a href="<?php the_permalink(); ?>" class="blog-list wow fadeInDown" data-wow-delay="<?php echo $i; ?>s">
			<div class="blog-image">
			<?php if(has_post_thumbnail()) : 
			$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'blog-thumbnail'); ?>
				<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>">
			<?php else: ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpg" alt="<?php the_title(); ?>">
			<?php endif; ?>
			</div>
			<div class="blog-excerpt">
			<h3><?php the_title(); ?></h3>
			<h4 class="posted-date"><i class="fa fa-calendar"></i><?php echo get_the_date(); ?></h4>
				<?php echo accesspress_letter_count( get_the_excerpt(), 200 ); ?> <br />
			<span>Read More&nbsp;&nbsp;<i class="fa fa-angle-right"></i></span>
			</div>
		</a>

		<?php
			endwhile;
			wp_reset_postdata();
		endif;
	?>
	</div>
	<div class="clearfix btn-wrap">
	<a class="btn" href="<?php echo get_category_link($category)?>">Read All</a>
	</div>



