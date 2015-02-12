<?php
/**
 * Template Name: Home Page
 *
 * @package accesspress_parallax
 */
 

get_header();

	$sections = of_get_option('parallax_section');

	if(!empty($sections)):
	foreach ($sections as $section) :
		$page = get_post( $section['page'] ); 
		$overlay = $section['overlay'];
		$image = $section['image'];
		$layout = $section['layout'];
		$category = $section['category']; 
		$googlemapclass = $layout == "googlemap_template" ? " google-map" : "";
	?>

	<?php if(!empty($section['page'])): ?>
		<section class="parallax-section clearfix<?php echo $googlemapclass." ".$layout;  ?>" id="<?php echo "section-".$page->ID; ?>">
		<?php if(!empty($image) && $overlay!="overlay0") : ?>
			<div class="overlay"></div>
		<?php endif; ?>

		<?php if($layout != "googlemap_template") :?>
			<div class="mid-content">
		<?php endif; ?>

				<?php 
				if($layout != "action_template" && $layout != "blank_template" && $layout != "googlemap_template"): ?>
					<h1><span><?php echo $page->post_title; ?></span></h1>

					<div class="parallax-content">
					<?php if($page->post_content != "") : ?>
						<div class="page-content">
						<?php echo wpautop(do_shortcode($page->post_content)); ?>
						</div>
					<?php endif; ?>
					</div> 
				<?php endif; ?>

					<?php 
						switch ($layout) {
							case 'default_template':
								$template = "layouts/default";
								break;

							case 'service_template':
								$template = "layouts/service";
								break;

							case 'team_template':
								$template = "layouts/team";
								break;

							case 'portfolio_template':
								$template = "layouts/portfolio";
								break;

							case 'testimonial_template':
								$template = "layouts/testimonial";
								break;

							case 'action_template':
								$template = "layouts/action";
								break;

							case 'blank_template':
								$template = "layouts/blank";
								break;

							case 'googlemap_template':
								$template = "layouts/googlemap";
								break;

							case 'blog_template':
								$template = "layouts/blog";
								break;
							
							default:
								$template = "layouts/default";
								break;
						}
					?>

					<?php include($template."-section.php");?>
        		
			<?php if($layout != "googlemap_template") :?>
			</div>
			<?php endif; ?>
		</section>
	<?php
	endif; 
	endforeach;
	endif;

?>

<?php get_footer(); ?>
