<?php
/**
 * The front page template file.
 *
 *
 * @package stronghold
 */
	if ( 'posts' == get_option( 'show_on_front' ) ) { 
	    include( get_home_template() );
	} else {
get_header(); 
?>

	<?php
		$slider_cat = get_theme_mod( 'slider_cat', '' );
		$slider_count = get_theme_mod( 'slider_count', 5 );
		$slider_posts = array(
			'cat' => $slider_cat,
			'posts_per_page' => $slider_count
		);

		$query = new WP_Query($slider_posts);
		if( $query->have_posts()) : ?>
			<div class="flexslider">
				<ul class="slides">
		<?php while($query->have_posts()) :
				$query->the_post();
				if( has_post_thumbnail() ) : ?>
				    <li>
				    	<div class="flex-image">
				    		<?php the_post_thumbnail('full'); ?>
				    	</div>
				    	<div class="flex-caption">
				    		<?php the_content(); ?>
				    	</div>
				    </li>
				<?php endif; ?>
		<?php endwhile; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php  
		$query = null;
		wp_reset_postdata();
	?>
	<div id="content" class="site-content">
		<div class="container">		
	<div id="primary" class="content-area sixteen columns">
		<main id="main" class="site-main" role="main">

		<?php
			$service_page1 = get_theme_mod('service_1');
			$service_page2 = get_theme_mod('service_2');
			$service_page3 = get_theme_mod('service_3');

			if( $service_page1 && $service_page2 && $service_page3 ) {
				$service_pages = array($service_page1,$service_page2,$service_page3);
				$args = array(
					'post_type' => 'page',
					'post__in' => $service_pages,
					'posts_per_page' => -1 
				);
			} 	else {
			$args = array(
				'post_type' => 'page',
				'posts_per_page' => 3
			);			
		}


		$query = new WP_Query($args);
		if( $query->have_posts()) : ?>
			<div class="services-wrapper row">
		<?php while($query->have_posts()) :
				$query->the_post(); ?>
				    <div class="one-third column">
				    	<?php if( has_post_thumbnail() ) : ?>
				    		<?php the_post_thumbnail('full'); ?>
				    	<?php endif; ?>
				    	<?php the_content(); ?>
				    </div>
		<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php  
			$query = null;
			wp_reset_postdata();
		?>
		
		<?php stronghold_recent_posts(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php 
}
get_footer(); ?>
