<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="call-to-action">

		<h1><?php echo $page->post_title; ?></h1>

		<div class="parallax-content">
			<?php if($page->post_content != "") : ?>
			<div class="page-content">
				<?php echo wpautop($page->post_content); ?>
			</div>
			<?php endif; ?>
		</div>

	</div><!-- #primary -->



