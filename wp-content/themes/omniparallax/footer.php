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
        <div class="bottom-footer">
            <div class="mid-content clearfix">
                <div class="copy-right">
                    &copy; <?php echo date('Y') . ". OMNILab. All Right Reserved."; ?>
                </div>
                <!-- .copy-right -->
                <div class="site-info">
                    <?php echo 'Powered by'; ?> <a
                        href="<?php echo esc_url('http://www.wordpress.com/', 'accesspress_parallax'); ?>" title="WordPress"
                        target="_blank">WordPress</a>
                </div>
                <!-- .site-info -->
            </div>
        </div>
    </footer><!-- #colophon -->

</div><!-- #page -->

<div id="go-top"><a href="#page"><i class="fa fa-angle-up"></i></a></div>

<?php wp_footer(); ?>
</body>
</html>
