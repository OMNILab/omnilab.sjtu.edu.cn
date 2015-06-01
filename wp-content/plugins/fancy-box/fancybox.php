<?php
/*
Plugin Name: Fancybox
Plugin URI: http://wordpress.org/extend/plugins/fancy-box/
Description: Enables <a href="http://fancy.klade.lv/">fancybox 1.2.6</a> on all image links including BMP, GIF, JPG, JPEG, and PNG links.
Version: 1.1.0
Author: Kevin Sylvestre
Author URI: http://ksylvest.com/
*/

if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

function fancybox() {
?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    var select = $('a[href$=".bmp"],a[href$=".gif"],a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".BMP"],a[href$=".GIF"],a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"]');
    select.attr('rel', 'fancybox');
    select.fancybox();
  });
</script>
<?php
}

if (!is_admin()) {
  function load_styles() {
    wp_enqueue_style('jquery.fancybox', WP_PLUGIN_URL.'/fancy-box/jquery.fancybox.css', false, '1.2.6');
  }
  function load_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery.fancybox', WP_PLUGIN_URL.'/fancy-box/jquery.fancybox.js', array('jquery'), '1.2.6');
    wp_enqueue_script('jquery.easing', WP_PLUGIN_URL.'/fancy-box/jquery.easing.js', array('jquery'), '1.3'); 
  }
  add_action('wp_enqueue_scripts', 'load_styles');
  add_action('wp_enqueue_scripts', 'load_scripts');
  add_action('wp_head', 'fancybox');
}
?>
