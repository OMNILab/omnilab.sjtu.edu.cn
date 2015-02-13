<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package omniparallax
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<?php if(is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3') || is_active_sidebar('footer-4')) :?>
		<div class="top-footer footer-column-<?php echo accesspress_footer_count(); ?>">
			<div class="mid-content">
            <div class="top-footer-wrap clearfix">
				<?php if(is_active_sidebar('footer-1')): ?>
				<div class="footer-block">
					<?php dynamic_sidebar('footer-1'); ?>
				</div>
				<?php endif; ?>

				<?php if(is_active_sidebar('footer-2')): ?>
				<div class="footer-block">
					<?php dynamic_sidebar('footer-2'); ?>
				</div>
				<?php endif; ?>

				<?php if(is_active_sidebar('footer-3')): ?>
				<div class="footer-block">
					<?php dynamic_sidebar('footer-3'); ?>
				</div>
				<?php endif; ?>

				<?php if(is_active_sidebar('footer-4')): ?>
				<div class="footer-block">
					<?php dynamic_sidebar('footer-4'); ?>
				</div>
				<?php endif; ?> 
            </div>
			</div>
		</div>
		<?php endif; ?>
		

		<div class="bottom-footer">
			<div class="mid-content clearfix">
				<div  class="copy-right">
					&copy; <?php echo date('Y').". OMNILab. All Right Reserved."; ?>
				</div><!-- .copy-right -->
				<div class="site-info">
					<?php _e('Powered by','accesspress_parallax'); ?> <a href="<?php echo esc_url('http://www.wordpress.com/','accesspress_parallax'); ?>" title="WordPress" target="_blank">WordPress</a>
				</div><!-- .site-info -->
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->
<div id="go-top"><a href="#page"><i class="fa fa-angle-up"></i></a></div>

<?php wp_footer(); ?>
</body>
</html>
