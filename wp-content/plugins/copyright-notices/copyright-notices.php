<?php
/*
Plugin Name: Copyright Notices
Plugin URI: http://github.com/caesar0301/copyright-notices/
Description: A plugin that allows the user to set Copyright text in the
theme and control it from WordPress Admin.
Author: Xiaming Chen
Version: 1.0
Author URI: http://hsiamin.com/
*/

function copyright_notices_admin()
{
    ?>
    <div class="wrap">
        <h2>Copyright Notices Configuration</h2>
        <p>On this page, you will configure all the aspects of this plugins.</p>
        <form action="" method="post" id="copyright-notices-conf-form">
            <h3><label for="copyright_text">Copyright Text to be inserted
                    in the footer of your theme:</label></h3>
            <p><input type="text" name="copyright_text" id="copyright_text"
                       value="<?php echo esc_attr(get_option('copyright_notices_text')) ?>"/>
            </p>
            <p class="submit"><input type="submit" name="submit" value="Update options &raquo;"/>
            </p>
            <?php wp_nonce_field('copyright_notices_admin_options-update'); ?>
        </form>
    </div>
<?php
}

function copyright_notices_admin_page()
{
    add_submenu_page('plugins.php', 'Copyright Notices Configuration',
        'Copyright Notices Configuration', 'manage_options', 'copyright-notices',
        'copyright_notices_admin');
}

function save_copyright_notices()
{
    if( check_admin_referer('copyright_notices_admin_options-update') ) {
        if (update_option('copyright_notices_text', stripslashes($_POST['copyright_text'])))
            wp_redirect(__FILE__ . '?updated=1');
    }
}

add_action('admin_menu', 'copyright_notices_admin_page');
add_action( 'load-plugins_page_copyright-notices.php', 'save_copyright_notices' );
//add_action( 'load-copyright-notices.php', 'save_copyright_notices' );