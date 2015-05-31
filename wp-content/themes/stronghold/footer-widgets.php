<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package stronghold
 */

if ( ! is_dynamic_sidebar( ) ) {
	return;
}
?>

		<div class="four columns">
			<?php dynamic_sidebar('footer-1'); ?>
		</div>

		<div class="four columns">
			<?php dynamic_sidebar('footer-2'); ?>
		</div>

		<div class="four columns">
			<?php dynamic_sidebar('footer-3'); ?>
		</div>

		<div class="four columns">
			<?php dynamic_sidebar('footer-4'); ?>
		</div>
