<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="team-listing clearfix">
	<?php 
		$args = array(
			'cat' => $category,
			'posts_per_page' => -1
			);
		$query = new WP_Query($args);
		if($query->have_posts()): ?>
		<div class="team-tab">
			<div class="team-slider">
			<?php 
				$i = 0;
                    while ($query->have_posts()): $query->the_post();
                        $i = $i + 0.25;
                        ?>

			<a id="team-<?php echo get_the_ID(); ?>" href="#" class="clearfix team-image wow fadeInLeft" data-wow-delay="<?php echo $i; ?>s">
				<?php if(has_post_thumbnail()) : 
				$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'thumbnail');
				?>
				<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>">
				<?php else: ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/dummy.png" alt="<?php the_title(); ?>">
				<?php endif; ?>
			</a>

			<?php
				endwhile;
				wp_reset_postdata(); ?>
			</div>
		</div>
		<?php
		endif;
		?>

		<?php 
		$args = array(
			'cat' => $category,
			'posts_per_page' => -1
			);
		$query = new WP_Query($args);
		if($query->have_posts()): ?>
		<div class="team-content wow fadeIn" data-wow-delay="1.5s">
		<?php 
			while($query->have_posts()): $query->the_post();
		?>

		<div class="clearfix team-list team-<?php echo get_the_ID(); ?>">
			<?php if(has_post_thumbnail()) : 
			$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'team-thumbnail');
			?>
			<div class="team-big-image">
			<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>">
			</div>
			<?php endif; ?>

			<div class="team-detail">
				<h3><?php the_title(); ?></h3>
				<?php the_content(); ?>
			</div>
		</div>

		<?php
			endwhile;
			wp_reset_postdata(); ?>
		</div>
		<?php
		endif;
		?>
	</div><!-- #primary -->



