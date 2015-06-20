<?php
/**
* Init admin actions : loads the meta boxes,
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_admin_init' ) ) :
  class TC_admin_init {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        //enqueue additional styling for admin screens
        add_action( 'admin_init'                          , array( $this , 'tc_admin_style' ) );

        //Load the editor-style specific (post formats and RTL), the active skin, the user style.css
        //add user defined fonts in the editor style (@see the query args add_editor_style below)
        add_action( 'after_setup_theme'                   , array( $this, 'tc_add_editor_style') );

        add_filter( 'tiny_mce_before_init'                , array( $this, 'tc_user_defined_tinymce_css') );
        //refresh the post / CPT / page thumbnail on save. Since v3.3.2.
        add_action ( 'save_post'                          , array( $this , 'tc_refresh_thumbnail') );
      }



      /*
      * @return void
      * updates the tc-thumb-fld post meta with the relevant thumb id and type
      * @package Customizr
      * @since Customizr 3.3.2
      */
      function tc_refresh_thumbnail( $post_id ) {
        // If this is just a revision, don't send the email.
        if ( wp_is_post_revision( $post_id ) )
          return;

        if ( ! class_exists( 'TC_post_thumbnails' ) )
          TC___::$instance -> tc__( array('content' => array( array('inc/parts', 'post_thumbnails') ) ), true );

        TC_post_thumbnails::$instance -> tc_set_thumb_info( $post_id );
      }


      /*
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.10
      */
      function tc_maybe_add_gfonts_to_editor() {
        $_font_pair         = esc_attr( TC_utils::$inst->tc_opt('tc_fonts') );
        $_all_font_pairs    = TC_init::$instance -> font_pairs;
        if ( false === strpos($_font_pair,'_g_') )
          return;
        //Commas in a URL need to be encoded before the string can be passed to add_editor_style.
        return array(
          str_replace(
            ',',
            '%2C',
            sprintf( '//fonts.useso.com/css?family=%s', TC_utils::$inst -> tc_get_font( 'single' , $_font_pair ) )
          )
        );
      }



     /**
     * Add fallback admin page.
     * @package Customizr
     * @since Customizr 1.1
     */
      function tc_add_welcome_page() {
          $theme_page = add_theme_page(
              __( 'About Customizr' , 'customizr' ),   // Name of page
              __( 'About Customizr' , 'customizr' ),   // Label in menu
              'edit_theme_options' ,          // Capability required
              'welcome.php' ,             // Menu slug, used to uniquely identify the page
              array( $this , 'tc_welcome_panel' )         //function to be called to output the content of this page
          );
      }



      /**
     * Render welcome admin page.
     * @package Customizr
     * @since Customizr 3.0.4
     */
      function tc_welcome_panel() {

        $is_help = isset($_GET['help'])  ?  true : false;

        ?>
        <div class="wrap about-wrap">

            <?php if ($is_help) : ?>
              <h1 class="need-help-title"><?php _e( 'Need help with Customizr ?','customizr' ) ?></h1>
            <?php else : ?>
              <h1><?php printf( __( 'Welcome to Customizr %s','customizr' ), CUSTOMIZR_VER ); ?></h1>
            <?php endif; ?>


          <?php if ($is_help) : ?>
            <div class="changelog">
              <div class="about-text tc-welcome">
              <?php printf( __( 'You can start by watching the <a href="%1$s" target="_blank">introduction video</a> or by reading <a href="%2$s" target="_blank">the documentation</a>.<br/> If you don\'t find an answer to your issue, don\'t panic! Since Customizr is used by a growing community of webmasters reporting bugs and making continuous improvements, you will probably find a solution to your problem either in the FAQ or in the user forum.','customizr' ),
               TC_WEBSITE,
               TC_WEBSITE.'customizr'
               ); ?>
               </div>
              <div class="feature-section col two-col">
                <div>
                   <br/>
                    <a class="button-secondary customizr-help" title="documentation" href="<?php echo TC_WEBSITE ?>customizr" target="_blank"><?php _e( 'Read the documentation','customizr' ); ?></a>
                </div>
                <div class="last-feature">
                  <br/>
                    <a class="button-secondary customizr-help" title="faq" href="<?php echo TC_WEBSITE ?>customizr/faq" target="_blank"><?php _e( 'Check the FAQ','customizr' ); ?></a>
                 </div>
              </div><!-- .two-col -->
              <div class="feature-section col two-col">
                 <div>
                    <a class="button-secondary customizr-help" title="code snippets" href="<?php echo TC_WEBSITE ?>code-snippets/" target="_blank"><?php _e( 'Code snippets','customizr' ); ?></a>
                </div>
                 <div class="last-feature">
                    <a class="button-secondary customizr-help" title="forum" href="http://wordpress.org/support/theme/customizr" target="_blank"><?php _e( 'Discuss in the user forum','customizr' ); ?></a>
                 </div>
              </div><!-- .two-col -->
            </div><!-- .changelog -->

          <?php else: ?>

            <div class="about-text tc-welcome">
              <?php printf( __( 'Thank you for using Customizr! Customizr %1$s has more features, is safer and more stable than ever <a href="#customizr-changelog">(see the changelog)</a> to help you build an awesome website. Watch the <a href="%2$s" target="_blank">introduction video</a> and find inspiration in the <a href="#showcase">showcase</a>.<br/>Enjoy it! ','customizr' ),
               CUSTOMIZR_VER,
               TC_WEBSITE
               ); ?>
            </div>

          <?php endif; ?>

          <?php if ( TC___::$instance -> tc_is_child() ) : ?>
            <div class="changelog point-releases"></div>

            <div class="tc-upgrade-notice">
              <p>
              <?php
                printf( __('You are using a child theme of Customizr %1$s : always check the %2$s after upgrading to see if a function or a template has been deprecated.' , 'customizr'),
                  'v'.CUSTOMIZR_VER,
                  '<strong><a href="#customizr-changelog">changelog</a></strong>'
                  );
                ?>
              </p>
            </div>
          <?php endif; ?>

          <div class="changelog point-releases"></div>

         <div class="changelog">
           <div class="feature-section col three-col">

              <div>
                <h3><?php _e( 'We need sponsors!','customizr' ); ?></h3>
                <p><?php  _e( '<strong>We do our best do make Customizr the perfect free theme for you!</strong><br/> Please help support it\'s continued development with a donation of $20, $50, or even $100.','customizr' ) ?></br>

                  <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8CTH6YFDBQYGU" target="_blank" rel="nofollow"><img class="tc-donate" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="Make a donation for Customizr" /></a>
                </p>
              </div>

              <div>
                <h3><?php _e( 'Happy user of Customizr?','customizr' ); ?></h3>
                <p><?php _e( 'If you are happy with the theme, say it on wordpress.org and give Customizr a nice review! <br />(We are addicted to your feedbacks...)','customizr' ) ?></br>
                <a class="button-primary review-customizr" title="Customizr WordPress Theme" href="http://wordpress.org/support/view/theme-reviews/customizr" target="_blank">Review Customizr &raquo;</a></p>
              </div>

              <div class="last-feature">
                <h3><?php _e( 'Follow us','customizr' ); ?></h3>
                <p class="tc-follow"><a href="<?php echo TC_WEBSITE.'blog' ?>" target="_blank"><img src="<?php echo TC_BASE_URL.'inc/admin/img/tc.png' ?>" alt="Press Customizr" /></a></p>
                <!-- Place this tag where you want the widget to render. -->

          </div><!-- .feature-section -->
        </div><!-- .changelog -->

        <div id="extend" class="changelog">
          <h3 style="text-align:left"><?php _e("Customizr's extensions" ,'customizr') ?></h3>

          <div class="feature-section images-stagger-right">
             <a class="" title="<?php _e("Visit the extension's page",'customizr') ?>" href="<?php echo TC_WEBSITE ?>extend/" target="_blank"><img alt="Customizr'extensions" src="<?php echo TC_BASE_URL.'inc/admin/img/extend.png' ?>" class=""></a>
            <h4 style="text-align: left"><?php _e('Easily take your web design one step further' ,'customizr') ?></h4></br>
            <p style="text-align: left"><?php _e("The Customizr's extensions are plugins developed to extend the Customizr theme with great features. Nicely integrated with the theme's built-in options, they can be enabled/disabled safely with no side effects on your existing settings or customizations." , 'customizr') ?>
            </p>
            <p style="text-align: left"><?php _e("These modules are designed to be simple to use for everyone. They are a good solution to add some creative customizations whitout needing to dive into the code." , 'customizr') ?>
            </p>
            <p style="text-align: left"><?php _e("Customizr's extensions are installed and upgraded from your WordPress admin, like any other WordPress plugins. Well documented and easily extendable with hooks, they come with a dedicated support forum on presscustomizr.com." , 'customizr') ?>
            </p>
            <p style="text-align:left">
                <a class="button-primary review-customizr" title="<?php _e("Visit the extension's page",'customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/extend/" target="_blank"><?php _e("Visit the extension's page",'customizr') ?> &raquo;</a>
            </p>
          </div>
        </div>

        <div id="showcase" class="changelog">
          <h3 style="text-align:right"><?php _e('Customizr Showcase' ,'customizr') ?></h3>

          <div class="feature-section images-stagger-left">
             <a class="" title="<?php _e('Visit the showcase','customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/showcase/" target="_blank"><img alt="Customizr Showcase" src="<?php echo TC_BASE_URL.'inc/admin/img/mu2.png' ?>" class=""></a>
            <h4 style="text-align: right"><?php _e('Find inspiration for your next Customizr based website!' ,'customizr') ?></h4>
            <p style="text-align: right"><?php _e('This showcase aims to show what can be done with Customizr and helping other users to find inspiration for their web design.' , 'customizr') ?>
            </p>
            <p style="text-align: right"><?php _e('Do you think you made an awesome website that can inspire people? Submitting a site for review is quick and easy to do.' , 'customizr') ?></br>
            </p>
            <p style="text-align:right">
                <a class="button-primary review-customizr" title="<?php _e('Visit the showcase','customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/showcase/" target="_blank"><?php _e('Visit the showcase','customizr') ?> &raquo;</a>
            </p>
          </div>
        </div>

        <div id="customizr-changelog" class="changelog">

          <h3><?php printf( __( 'Changelog in version %1$s' , 'customizr' ) , CUSTOMIZR_VER ); ?></h3>

            <p><?php do_action( '__changelog' ); ?></p>

        </div>


        <div class="return-to-dashboard">
          <a href="<?php echo esc_url( self_admin_url() ); ?>"><?php
            is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home','customizr' ) : _e( 'Go to Dashboard','customizr' ); ?></a>
        </div>

      </div>
      <?php
      }


      /**
     * enqueue additional styling for admin screens
     * @package Customizr
     * @since Customizr 3.0.4
     */
      function tc_admin_style() {
        wp_enqueue_style(
          'tc-admincss',
          sprintf('%1$sinc/admin/css/tc_admin%2$s.css' , TC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
          array(),
          CUSTOMIZR_VER
        );
      }



      /**
     * Extract changelog of latest version from readme.txt file
     * @package Customizr
     * @since Customizr 3.0.5
     */
      function tc_extract_changelog() {

        if( ! file_exists(TC_BASE."readme.txt") ) {
          return;
        }
        if( ! is_readable(TC_BASE."readme.txt") ) {
          echo '<p>The changelog in readme.txt is not readable.</p>';
          return;
        }

        $stylelines = explode("\n", implode('', file(TC_BASE."readme.txt")));
        $read = false;
        $i = 0;

        foreach ($stylelines as $line) {
          //echo 'i = '.$i.'|read = '.$read.'pos = '.strpos($line, '= ').'|line :'.$line.'<br/>';
          //we stop reading if we reach the next version change
          if ($i == 1 && strpos($line, '= ') === 0 ) {
            $read = false;
            $i = 0;
          }
          //we write the line if between current and previous version
          if ($read) {
            echo $line.'<br/>';
          }
          //we skip all lines before the current version changelog
          if ($line != strpos($line, '= '.CUSTOMIZR_VER)) {
            if ($i == 0) {
              $read = false;
            }
          }
          //we begin to read after current version title
          else {
            $read = true;
            $i = 1;
          }
        }
      }


      /**
      * Customizr styles the visual editor to resemble the theme style,
      * Loads the editor-style specific (post formats and RTL), the active skin, the user style.css, the user_defined fonts
      * @package Customizr
      * @since Customizr 3.2.11
      *
      */
      function tc_add_editor_style() {
        $_stylesheets = array(
            TC_BASE_URL.'inc/admin/css/editor-style.css',
            TC_init::$instance -> tc_get_style_src() , get_stylesheet_uri()
        );

        if ( apply_filters( 'tc_add_custom_fonts_to_editor' , false != $this -> tc_maybe_add_gfonts_to_editor() ) )
          $_stylesheets = array_merge( $_stylesheets , $this -> tc_maybe_add_gfonts_to_editor() );

        add_editor_style( $_stylesheets );
      }




      /**
      * Extend TinyMCE config with a setup function.
      * See http://www.tinymce.com/wiki.php/API3:event.tinymce.Editor.onInit
      * http://wordpress.stackexchange.com/questions/120831/how-to-add-custom-css-theme-option-to-tinymce
      * @package Customizr
      * @since Customizr 3.2.11
      *
      */
      function tc_user_defined_tinymce_css( $init ) {
        if ( ! apply_filters( 'tc_add_custom_fonts_to_editor' , true ) )
          return $init;
        //some plugins fire tiny mce editor in the customizer
        //in this case, the TC_resource class has to be loaded
        if ( ! class_exists('TC_resources') )
          TC___::$instance -> tc__( array('fire' => array( array('inc' , 'resources') ) ), true );

        //fonts
        $_css = TC_resources::$instance -> tc_write_fonts_inline_css( '', 'mce-content-body');
        //icons
        $_css .= TC_resources::$instance -> tc_get_inline_font_icons_css();
       ?>

          <script type="text/javascript">
            function add_user_defined_CSS( ed ) {
              //http://www.tinymce.com/wiki.php/Tutorial:Migration_guide_from_3.x
                ed.on('init', function() {
                    tinyMCE.activeEditor.dom.addStyle(<?php echo json_encode($_css) ?>);
                } );
            };
          </script>

          <?php
          if (wp_default_editor() == 'tinymce')
              $init['setup'] = 'add_user_defined_CSS';

          return $init;
      }

  }//end of class
endif;