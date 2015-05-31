<?php
/**
* Defines the customizer setting map
* On live context, used to generate the default option values
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
if ( ! class_exists( 'TC_utils_settings_map' ) ) :
  class TC_utils_settings_map {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    private $is_wp_version_before_4_0;

    function __construct () {
        self::$instance =& $this;

        //update remove section map, since 3.2.0
        add_filter ( 'tc_remove_section_map'                , array( $this , 'tc_update_remove_sections') );
        //theme switcher's enabled when user opened the customizer from the theme's page
        add_filter ( 'tc_remove_section_map'                , array( $this , 'tc_set_theme_switcher_visibility') );
        //update section map, since 3.2.0
        add_filter ( 'tc_add_section_map'                   , array( $this , 'tc_update_section_map') );
        //update setting_control_map
        add_filter ( 'tc_add_setting_control_map'           , array( $this , 'tc_update_setting_control_map'), 100 );
        //update setting_control_map with post list design, v3.2.18+
        add_filter ( 'tc_add_setting_control_map'           , array( $this , 'tc_grid_map'), 101 );
        //declare a private property to check wp version >= 4.0
        global $wp_version;
        $this -> is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;
    }//end of construct



    /**
    * Print the themes section (themes switcher) when previewing the themes from wp-admin/themes.php
    * hook : tc_remove_section_map
    */
    function tc_set_theme_switcher_visibility( $_sections) {
      //Don't do anything is in preview frame
      //=> because once the preview is ready, a postMessage is sent to the panel frame to refresh the sections and panels
      if ( TC___::$instance -> tc_is_customize_preview_frame() )
        return $_sections;

      //when user access the theme switcher from the admin bar
      $_theme_switcher_requested = false;
      if ( isset( $_GET['autofocus'] ) ) {
        $autofocus = wp_unslash( $_GET['autofocus'] );
        if ( is_array( $autofocus ) && isset($autofocus['section']) ) {
          $_theme_switcher_requested = 'themes' == $autofocus['section'];
        }
      }

      if ( isset($_GET['theme']) || ! isset($_sections['remove_section']) || $_theme_switcher_requested )
        return $_sections;

      array_push( $_sections['remove_section'] , 'themes' );
      return $_sections;
    }



    /**
    * Generates the featured pages options
    *
    * @package Customizr
    * @since Customizr 3.0.15
    *
    */
    private function tc_generates_featured_pages() {
      $default = array(
        'dropdown'  =>  array(
              'one'   => __( 'Home featured page one' , 'customizr' ),
              'two'   => __( 'Home featured page two' , 'customizr' ),
              'three' => __( 'Home featured page three' , 'customizr' )
        ),
        'text'    => array(
              'one'   => __( 'Featured text one (200 car. max)' , 'customizr' ),
              'two'   => __( 'Featured text two (200 car. max)' , 'customizr' ),
              'three' => __( 'Featured text three (200 car. max)' , 'customizr' )
        )
      );

      //declares some loop's vars and the settings array
      $priority       = 70;
      $incr         = 0;
      $fp_setting_control = array();

      //gets the featured pages id from init
      $fp_ids       = apply_filters( 'tc_featured_pages_ids' , TC_init::$instance -> fp_ids);

      //dropdown field generator
      foreach ( $fp_ids as $id ) {
        $priority = $priority + $incr;
        $fp_setting_control['tc_theme_options[tc_featured_page_'. $id.']']    =  array(
                      'label'       => isset($default['dropdown'][$id]) ? $default['dropdown'][$id] :  sprintf( __('Custom featured page %1$s' , 'customizr' ) , $id ),
                      'section'     => 'tc_frontpage_settings' ,
                      'type'        => 'dropdown-pages' ,
                      'priority'      => $priority
                    );
        $incr += 10;
      }

      //text field generator
      $incr         = 10;
      foreach ( $fp_ids as $id ) {
        $priority = $priority + $incr;
        $fp_setting_control['tc_theme_options[tc_featured_text_' . $id . ']']   = array(
                      'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
                      'transport'   => 'postMessage',
                      'control'   => 'TC_controls' ,
                      'label'       => isset($default['text'][$id]) ? $default['text'][$id] : sprintf( __('Featured text %1$s (200 car. max)' , 'customizr' ) , $id ),
                      'section'     => 'tc_frontpage_settings' ,
                      'type'        => 'textarea' ,
                      'notice'    => __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
                      'priority'      => $priority,
                    );
        $incr += 10;
      }

      return $fp_setting_control;
    }




    private function tc_generates_socials() {
      //gets the social network array
      $socials      = apply_filters( 'tc_default_socials' , TC_init::$instance -> socials );

      //declares some loop's vars and the settings array
      $priority       = 50;//start priority
      $incr         = 0;
      $socials_setting_control  = array();

      foreach ( $socials as $key => $data ) {
        $priority += $incr;
        $socials_setting_control['tc_theme_options[' . $key . ']']  = array(
                      'default'         => ( isset($data['default']) && !is_null($data['default']) ) ? $data['default'] : null ,
                      'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
                      'control'       => 'TC_controls' ,
                      'label'         => ( isset($data['option_label']) ) ? call_user_func( '__' , $data['option_label'] , 'customizr' ) : $key,
                      'section'       => 'tc_social_settings' ,
                      'type'          => 'url',
                      'priority'      => $priority,
                      'icon'          => "tc-icon-". str_replace('tc_', '', $key)
                    );
        $incr += 5;
      }

      return $socials_setting_control;
    }



    private function tc_get_skins($path) {
      //checks if path exists
      if ( !file_exists($path) )
        return;

      //gets the skins from init
      $default_skin_list    = TC_init::$instance -> skins;

      //declares the skin list array
      $skin_list        = array();

      //gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
      $files            = scandir($path) ;
      foreach( $files as $file ) {
          //skips the minified and tc_common
          if ( false !== strpos($file, '.min.') || false !== strpos($file, 'tc_common') )
            continue;

          if ( $file[0] != '.' && !is_dir($path.$file) ) {
            if ( substr( $file, -4) == '.css' ) {
              $skin_list[$file] = isset($default_skin_list[$file]) ?  call_user_func( '__' , $default_skin_list[$file] , 'customizr' ) : substr_replace( $file , '' , -4 , 4);
            }
          }
        }//endforeach
      $_to_return = array();

      //Order skins like in the default array
      foreach( $default_skin_list as $_key => $value ) {
        if( isset($skin_list[$_key]) ) {
          $_to_return[$_key] = $skin_list[$_key];
        }
      }
      //add skins not included in default
      foreach( $skin_list as $_file => $_name ) {
        if( ! isset( $_to_return[$_file] ) )
          $_to_return[$_file] = $_name;
      }
      return $_to_return;
    }//end of function




    /**
    * Returns the layout choices array
    *
    * @package Customizr
    * @since Customizr 3.1.0
    */
    private function tc_layout_choices() {
        $global_layout  = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
        $layout_choices = array();
        foreach ($global_layout as $key => $value) {
          $layout_choices[$key]   = ( $value['customizer'] ) ? call_user_func(  '__' , $value['customizer'] , 'customizr' ) : null ;
        }
        return $layout_choices;
    }



    /**
     * Retrieves slider names and generate the select list
     * @package Customizr
     * @since Customizr 3.0.1
     */
    private function tc_slider_choices() {
      $__options    =   get_option('tc_theme_options');
      $slider_names   =   isset($__options['tc_sliders']) ? $__options['tc_sliders'] : array();

      $slider_choices = array(
        0     =>  __( '&mdash; No slider &mdash;' , 'customizr' ),
        'demo'  =>  __( '&mdash; Demo Slider &mdash;' , 'customizr' )
        );
      if ( $slider_names ) {
        foreach( $slider_names as $tc_name => $slides) {
          $slider_choices[$tc_name] = $tc_name;
        }
      }
      return $slider_choices;
    }



    /**
    * Returns the list of available skins from child (if exists) and parent theme
    *
    * @package Customizr
    * @since Customizr 3.0.11
    * @updated Customizr 3.0.15
    */
    private function tc_build_skin_list() {
        $parent_skins   = $this -> tc_get_skins(TC_BASE .'inc/assets/css');
        $child_skins    = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css') ) ? $this -> tc_get_skins(TC_BASE_CHILD .'inc/assets/css') : array();
        $skin_list      = array_merge( $parent_skins , $child_skins );

      return apply_filters( 'tc_skin_list', $skin_list );
    }


    /**
    * Defines sections, settings and function of customizer and return and array
    * Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_customizer_map( $get_default = null ) {
      $add_panel = array(
        'add_panel'       =>   array(
              'tc-global-panel' => array(
                        'priority'       => 10,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Global settings' , 'customizr' ),
                        'description'    => __( "Global settings for the Customizr theme :skin, socials, links..." , 'customizr' )
              ),
              'tc-header-panel' => array(
                        'priority'       => 20,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Header' , 'customizr' ),
                        'description'    => __( "Header settings for the Customizr theme." , 'customizr' )
              ),
              'tc-content-panel' => array(
                        'priority'       => 30,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Content : home, posts, ...' , 'customizr' ),
                        'description'    => __( "Content settings for the Customizr theme." , 'customizr' )
              ),
              'tc-footer-panel' => array(
                        'priority'       => 40,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Footer' , 'customizr' ),
                        'description'    => __( "Footer settings for the Customizr theme." , 'customizr' )
              ),
              'tc-advanced-panel' => array(
                        'priority'       => 1000,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Advanced options' , 'customizr' ),
                        'description'    => __( "Advanced settings for the Customizr theme." , 'customizr' )
              )
        )
      );//end of add_panel array
      $add_panel = apply_filters( 'tc_add_panel_map', $add_panel );

      //customizer option array
      $remove_section = array(
              'remove_section'       =>   array(
                          'background_image' ,
                          'static_front_page' ,
                          'colors'
              )
      );//end of remove_sections array
      $remove_section = apply_filters( 'tc_remove_section_map', $remove_section );



      $add_section = array(
              'add_section'       =>   array(
                      'tc_skins_settings'         => array(
                                        'title'     =>  __( 'Skin' , 'customizr' ),
                                        'priority'    =>  10,
                                        'description' =>  __( 'Select a skin for Customizr' , 'customizr' )
                      ),

                      'tc_logo_settings'          => array(
                                        'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                                        'priority'    =>  20,
                                        'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                      ),

                      'tc_frontpage_settings'       => array(
                                        'title'     =>  __( 'Front Page' , 'customizr' ),
                                        'priority'    =>  30,
                                        'description' =>  __( 'Set up front page options' , 'customizr' )
                      ),

                      'tc_layout_settings'        => array(
                                        'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                                        'priority'    =>  150,
                                        'description' =>  __( 'Set up layout options' , 'customizr' )
                      ),

                      'tc_page_comments'          => array(
                                        'title'     =>  __( 'Comments' , 'customizr' ),
                                        'priority'    =>  160,
                                        'description' =>  __( 'Set up comments options' , 'customizr' ),
                      ),

                      'tc_social_settings'        => array(
                                        'title'     =>  __( 'Social links' , 'customizr' ),
                                        'priority'    =>  170,
                                        'description' =>  __( 'Set up your social links' , 'customizr' ),
                      ),

                      'tc_image_settings'         => array(
                                        'title'     =>  __( 'Images' , 'customizr' ),
                                        'priority'    =>  180,
                                        'description' =>  __( 'Various images settings' , 'customizr' ),
                      ),

                      'tc_links_settings'         => array(
                                        'title'     =>  __( 'Links' , 'customizr' ),
                                        'priority'    =>  190,
                                        'description' =>  __( 'Various links settings' , 'customizr' ),
                      ),

                      'tc_custom_css'           => array(
                                        'title'     =>  __( 'Custom CSS' , 'customizr' ),
                                        'priority'    =>  200,
                                        'description' =>  __( 'Add your own CSS' , 'customizr' ),
                      ),

                      'tc_responsive'           => array(
                                        'title'     =>  __( 'Responsive settings' , 'customizr' ),
                                        'priority'    =>  210,
                                        'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                      ),
              )

      );//end of add_sections array
      $add_section = apply_filters( 'tc_add_section_map', $add_section );

      //specifies the transport for some options
      $get_setting    = array(
              'get_setting'       =>   array(
                      'blogname' ,
                      'blogdescription'
              )
      );//end of get_setting array
      $get_setting = apply_filters( 'tc_get_setting_map', $get_setting );




      /*-----------------------------------------------------------------------------------------------------
                          NAVIGATION SECTION
      ------------------------------------------------------------------------------------------------------*/
      $navigation_option_map = array(
              'menu_button'           => array(
                                'setting_type'  =>  null,
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'nav' ,
                                'type'      =>  'button' ,
                                'link'      =>  'nav-menus.php' ,
                                'buttontext'  => __( 'Manage menus' , 'customizr' ),
              ),
              //The hover menu type has been introduced in v3.1.0.
              //For users already using the theme (no theme's option set), the default choice is click, for new users, it is hover.
              'tc_theme_options[tc_menu_type]'  => array(
                                'default'   =>  TC_utils::$inst -> tc_user_started_before_version( '3.1.0' , '1.0.0' ) ? 'click' : 'hover',
                                'control'   =>  'TC_controls' ,
                                'title'     => __( 'Design and effects' , 'customizr'),
                                'label'     =>  __( 'Select a submenu expansion option' , 'customizr' ),
                                'section'   =>  'nav' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'click'   => __( 'Expand submenus on click' , 'customizr'),
                                        'hover'   => __( 'Expand submenus on hover' , 'customizr'  ),
                                ),
              ),
      ); //end of navigation options
      $navigation_option_map = apply_filters( 'tc_navigation_option_map', $navigation_option_map , $get_default );



      /*-----------------------------------------------------------------------------------------------------
                                        SKIN SECTION
      ------------------------------------------------------------------------------------------------------*/
      $skin_option_map    = array(
              //skin select
              'tc_theme_options[tc_skin]'     => array(
                                'default'   =>  'blue3.css' ,
                                'control'   => 'TC_controls' ,
                                'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                                'section'   =>  'tc_skins_settings' ,
                                'type'      =>  'select' ,
                                'choices'    =>  $this -> tc_build_skin_list(),
                                'transport'   =>  'postMessage',
              ),
              'tc_theme_options[tc_skin_random]' => array(
                                'default'   => 0,
                                'control'   => 'TC_controls',
                                'label'     => __('Randomize the skin', 'customizr'),
                                'section'   => 'tc_skins_settings',
                                'type'      => 'checkbox',
                                'notice'    => __( 'Apply a random color skin on each page load.' , 'customizr' )
              ),
              'tc_theme_options[tc_minified_skin]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Performance : use the minified CSS stylesheet", 'customizr' ),
                                'section'     => 'tc_skins_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'Using the minified version of the skin stylesheet will speed up your webpage load time.' , 'customizr' ),
              ),

              //enable/disable top border
              'tc_theme_options[tc_top_border]' => array(
                                'default'   =>  1,//top border on by default
                                'label'     =>  __( 'Display top border' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'tc_skins_settings' ,
                                'type'      =>  'checkbox' ,
                                'notice'    =>  __( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
              )
      );//end of skin options
      apply_filters( 'tc_skin_option_map', $skin_option_map, $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                     LOGO & FAVICON SECTION
      ------------------------------------------------------------------------------------------------------*/
      $logo_favicon_option_map = array(
              'tc_theme_options[tc_logo_upload]'  => array(
                                'control'   =>  'TC_Customize_Upload_Control' ,
                                'label'     =>  __( 'Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                                'title'     => __( 'LOGO' , 'customizr'),
                                'section'   => 'tc_logo_settings' ,
                                'type'      => 'tc_upload',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' )
              ),

              //force logo resize 250 * 85
              'tc_theme_options[tc_logo_resize]'  => array(
                                'default'   =>  1,
                                'label'     =>  __( 'Force logo dimensions to max-width:250px and max-height:100px' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'tc_logo_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( "Uncheck this option to keep your original logo dimensions." , 'customizr')
              ),
              'tc_theme_options[tc_sticky_logo_upload]'  => array(
                                'control'   =>  'TC_Customize_Upload_Control' ,
                                'label'     =>  __( 'Sticky Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                                'section'   =>  'tc_logo_settings' ,
                                'type'      => 'tc_upload',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'notice'    => __( "Use this upload control to specify a different logo on sticky header mode." , 'customizr')
              ),

              //favicon
              'tc_theme_options[tc_fav_upload]' => array(
                                'control'   =>  'TC_Customize_Upload_Control' ,
                                'label'       => __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
                                'title'     => __( 'FAVICON' , 'customizr'),
                                'section'   =>  'tc_logo_settings' ,
                                'type'      => 'tc_upload',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number'),
              )
      );
      $logo_favicon_option_map = apply_filters( 'tc_logo_favicon_option_map', $logo_favicon_option_map , $get_default );



      /*-----------------------------------------------------------------------------------------------------
                                     FRONT PAGE SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $front_page_option_map = array(
              //title
              'homecontent_title'         => array(
                      'setting_type'  =>  null,
                      'control'   =>  'TC_controls' ,
                      'title'       => __( 'Choose content and layout' , 'customizr' ),
                      'section'     => 'tc_frontpage_settings' ,
                      'type'      => 'title' ,
                      'priority'      => 0,
              ),

              //show on front
              'show_on_front'           => array(
                                'label'     =>  __( 'Front page displays' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'      => 'select' ,
                                'priority'      => 1,
                                'choices'     => array(
                                        'nothing'   => __( 'Don\'t show any posts or page' , 'customizr'),
                                        'posts'   => __( 'Your latest posts' , 'customizr'),
                                        'page'    => __( 'A static page' , 'customizr'  ),
                                ),
              ),

              //page on front
              'page_on_front'           => array(
                                'label'     =>  __( 'Front page' , 'customizr'  ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'dropdown-pages' ,
                                'priority'      => 1,
              ),

              //page for posts
              'page_for_posts'          => array(
                                'label'     =>  __( 'Posts page' , 'customizr'  ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'dropdown-pages' ,
                                'priority'      => 1,
              ),

              //layout
              'tc_theme_options[tc_front_layout]' => array(
                                'default'       => 'f' ,//Default layout for home page is full width
                                'label'       =>  __( 'Set up the front page layout' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'control'     => 'TC_controls' ,
                                'type'        => 'select' ,
                                'choices'     => $this -> tc_layout_choices(),
                                'priority'    => 2,
              ),

              //select slider
              'tc_theme_options[tc_front_slider]' => array(
                                'default'     => 'demo' ,
                                'control'     => 'TC_controls' ,
                                'title'       => __( 'Slider options' , 'customizr' ),
                                'label'       => __( 'Select front page slider' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'select' ,
                                //!important
                                'choices'     => ($get_default == true) ? null : $this -> tc_slider_choices(),
                                'priority'    => 20
              ),

              //select slider
              'tc_theme_options[tc_slider_width]' => array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Full width slider' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 30,
              ),

              //Delay between each slides
              'tc_theme_options[tc_slider_delay]' => array(
                                'default'       => 5000,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Delay between each slides' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'number' ,
                                'step'      => 500,
                                'min'     => 1000,
                                'notice'    => __( 'in ms : 1000ms = 1s' , 'customizr' ),
                                'priority'      => 50,
              ),

              //Front page widget area
              'tc_theme_options[tc_show_featured_pages]'  => array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'title'       => __( 'Featured pages options' , 'customizr' ),
                                'label'       => __( 'Display home featured pages area' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'select' ,
                                'choices'     => array(
                                        1 => __( 'Enable' , 'customizr' ),
                                        0 => __( 'Disable' , 'customizr' ),
                                ),
                                'priority'        => 55,
              ),

              //display featured page images
              'tc_theme_options[tc_show_featured_pages_img]' => array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Show images' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'The images are set with the "featured image" of each pages (in the page edit screen). Uncheck the option above to disable the featured page images.' , 'customizr' ),
                                'priority'      => 60,
              ),

              //display featured page images
              'tc_theme_options[tc_featured_page_button_text]' => array(
                                'default'       => __( 'Read more &raquo;' , 'customizr' ),
                                'transport'     =>  'postMessage',
                                'label'       => __( 'Button text' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'text' ,
                                'priority'      => 65,
              )

      );//end of front_page_options
      $front_page_option_map = array_merge( $front_page_option_map , $this -> tc_generates_featured_pages() );
      $front_page_option_map = apply_filters( 'tc_front_page_option_map', $front_page_option_map , $get_default );





      /*-----------------------------------------------------------------------------------------------------
                                     SITE LAYOUT
      ------------------------------------------------------------------------------------------------------*/
      $layout_option_map = array(
              //Breadcrumb
              'tc_theme_options[tc_breadcrumb]' => array(
                                'default'       => 1,//Breadcrumb is checked by default
                                'label'       => __( 'Display Breadcrumb' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 1,
              ),

              //Global sidebar layout
              'tc_theme_options[tc_sidebar_global_layout]' => array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the global default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'      => 2,
              ),

              //force default layout on every posts
              'tc_theme_options[tc_sidebar_force_layout]' =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Force default layout everywhere' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
                                'priority'      => 3,
              ),

              //Post sidebar layout
              'tc_theme_options[tc_sidebar_post_layout]'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the posts default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'      => 4,
              ),

              //Post per page
              'posts_per_page'  =>  array(
                                'default'     => get_option( 'posts_per_page' ),
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Maximum number of posts per page' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'     => 1,
                                //'priority'       => 8,
              ),

              //Post list length
              'tc_theme_options[tc_post_list_length]' =>  array(
                                'default'     => 'excerpt',
                                'label'       => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => array(
                                        'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                        'full'    => __( 'Display the full content' , 'customizr' )
                                        )
                                //'priority'       => 6,
              ),

              //Page sidebar layout
              'tc_theme_options[tc_sidebar_page_layout]'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                //'priority'       => 6,
                                ),
      );//end of layout_options
      $layout_option_map = apply_filters( 'tc_layout_option_map', $layout_option_map , $get_default);




      /*-----------------------------------------------------------------------------------------------------
                                     COMMENTS SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $comment_option_map = array(
              'tc_theme_options[tc_page_comments]'  =>  array(
                                'default'     => 0,
                                'control'     => 'TC_controls',
                                'label'       => __( 'Enable comments on pages' , 'customizr' ),
                                'section'     => 'tc_page_comments',
                                'type'        => 'checkbox',
                                'priority'    => 20,
                                'notice'      => __( 'This option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
              )
      );
      $comment_option_map = apply_filters( 'tc_comment_option_map', $comment_option_map , $get_default );



      /*-----------------------------------------------------------------------------------------------------
                               SOCIAL POSITIONS AND NETWORKS
      ------------------------------------------------------------------------------------------------------*/
      $social_layout_map = array(
              //Social position checkboxes
              'tc_theme_options[tc_social_in_header]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in header' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 10
              ),

              'tc_theme_options[tc_social_in_left-sidebar]' =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in left sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 20
              ),

              'tc_theme_options[tc_social_in_right-sidebar]'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in right sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 30
              ),
              'tc_theme_options[tc_social_in_footer]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in footer' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 40
              )
      );//end of social layout map

      $social_option_map = array_merge( $social_layout_map , $this -> tc_generates_socials() );
      $social_option_map = apply_filters( 'tc_social_option_map', $social_option_map, $get_default );




      /*-----------------------------------------------------------------------------------------------------
                                     IMAGE SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $images_option_map = array(
              'tc_theme_options[tc_fancybox]' =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Lightbox effect on images' , 'customizr' ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option activates a popin window whith a zoom effect when an image is clicked. Note : to enable this effect on the images of your pages and posts, images have to be linked to the Media File.' , 'customizr' ),
              ),

              'tc_theme_options[tc_fancybox_autoscale]' =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Autoscale images on zoom' , 'customizr' ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
              ),

              'tc_theme_options[tc_retina_support]' =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'High resolution (Retina) support' , 'customizr' ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => sprintf('%1$s <strong>%2$s</strong> : <a href="%4$splugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails" title="%5$s" target="_blank">%3$s</a>.',
                                    __( 'If enabled, your website will include support for high resolution devices.' , 'customizr' ),
                                    __( "It is strongly recommended to regenerate your media library images in high definition with this free plugin" , 'customizr'),
                                    __( "regenerate thumbnails" , 'customizr'),
                                    admin_url(),
                                    __( "Open the description page of the Regenerate thumbnails plugin" , 'customizr')
                                )
              ),
               'tc_theme_options[tc_display_slide_loader]'  =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Sliders : display on loading icon before rendering the slides" , "customizr" ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'When checked, this option displays a loading icon when the slides are being setup.' , 'customizr' ),
              ),
               'tc_theme_options[tc_center_slider_img]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Dynamic slider images centering on any devices" , "customizr" ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                //'notice'    => __( 'This option dynamically centers your images on any devices vertically or horizontally (without stretching them) according to their initial dimensions.' , 'customizr' ),
              ),
              'tc_theme_options[tc_center_img]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Dynamic thumbnails centering on any devices" , "customizr" ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'This option dynamically centers your images on any devices, vertically or horizontally according to their initial aspect ratio.' , 'customizr' ),
              )
      );//end of images options
      $images_option_map = apply_filters( 'tc_images_option_map', $images_option_map , $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                     LINKS SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $links_option_map = array(
              'tc_theme_options[tc_link_scroll]'  =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Smooth scroll on click' , 'customizr' ),
                                'section'     => 'tc_links_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option activates a smooth page scroll when clicking on a link to an anchor of the same page.' , 'customizr' ),
              )
      );//end of links options
      $links_option_map = apply_filters( 'tc_links_option_map', $links_option_map , $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                    RESPONSIVE SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $responsive_option_map = array(
              'tc_theme_options[tc_block_reorder]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Dynamic sidebar reordering on small devices' , 'customizr' ),
                                'section'     => 'tc_responsive' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'Activate this option to move the sidebars (if any) after the main content block, for smartphones or tablets viewport.' , 'customizr' ),
              )
      );//end of links options
      $responsive_option_map = apply_filters( 'tc_responsive_option_map', $responsive_option_map , $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                     CUSTOM CSS
      ------------------------------------------------------------------------------------------------------*/
      $custom_css_option_map = array(

              'tc_theme_options[tc_custom_css]' =>  array(
                                'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
                                'section'     => 'tc_custom_css' ,
                                'type'        => 'textarea' ,
                                'notice'    => sprintf('%1$s <a href="%4$ssnippet/creating-child-theme-customizr/" title="%3$s" target="_blank">%2$s</a>',
                                    __( "Use this field to test small chunks of CSS code. For important CSS customizations, you'll want to modify the style.css file of a" , 'customizr' ),
                                    __( 'child theme.' , 'customizr'),
                                    __( 'How to create and use a child theme ?' , 'customizr'),
                                    TC_WEBSITE
                                )
              )
      );//end of custom_css_options
      $custom_css_option_map = apply_filters( 'tc_custom_css_option_map', $custom_css_option_map , $get_default );

      $add_setting_control = array(
              'add_setting_control'   =>   array_merge(
                $navigation_option_map,
                $skin_option_map,
                $logo_favicon_option_map,
                $front_page_option_map,
                $layout_option_map,
                $comment_option_map,
                $social_option_map,
                $images_option_map,
                $links_option_map,
                $responsive_option_map,
                $custom_css_option_map,
                apply_filters( 'tc_custom_setting_control', array() )
              )
      );
      $add_setting_control = apply_filters( 'tc_add_setting_control_map', $add_setting_control );

      //merges all customizer arrays
      $customizer_map = array_merge( $add_panel, $remove_section , $add_section , $get_setting , $add_setting_control );

      return apply_filters( 'tc_customizer_map', $customizer_map );

    }//end of tc_customizer_map function


    /**
     * adds sanitization callback funtion : textarea
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_textarea( $value) {
      $value = esc_html( $value);
      return $value;
    }



    /**
     * adds sanitization callback funtion : number
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_number( $value) {
      if ( ! $value || is_null($value) )
        return $value;
      $value = esc_attr( $value); // clean input
      $value = (int) $value; // Force the value into integer type.
        return ( 0 < $value ) ? $value : null;
    }




    /**
     * adds sanitization callback funtion : url
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_url( $value) {
      $value = esc_url( $value);
      return $value;
    }



    /**
     * adds sanitization callback funtion : colors
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_hex_color( $color ) {
      if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
        return '#' . $unhashed;

      return $color;
    }


    /**
    * Change upload's path to relative instead of absolute
    * @package Customizr
    * @since Customizr 3.1.11
    */
    function tc_sanitize_uploads( $url ) {
      $upload_dir = wp_upload_dir();
      return str_replace($upload_dir['baseurl'], '', $url);
    }



    /**
    * Update initial remove section map defined in class-fire-utils.php.
    * (nav and title_tagline sections are added back in tc_update_section_map() )
    * hook : tc_remove_section_map
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_update_remove_sections( $_unchanged ) {
      return array(
        'remove_section'       =>   array(
                              'background_image' ,
                              'static_front_page' ,
                              'colors',
                              'nav',
                              'title_tagline',
                              'tc_page_comments'
            )
      );
    }



    /**
    * Update initial section map defined in class-fire-utils.php.
    * Add panel parameter (since WP4.0)
    * hook : tc_add_section_map
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_update_section_map( $_unchanged ) {
      //For nav menus option
      $locations      = get_registered_nav_menus();
      $menus          = wp_get_nav_menus();
      $num_locations  = count( array_keys( $locations ) );

      return array(
                  'add_section'       =>   array(
                        'title_tagline'         => array(
                                            'title'    => __( 'Site Title & Tagline', 'customizr' ),
                                            'priority' => $this -> is_wp_version_before_4_0 ? 7 : 0,
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_logo_settings'            => array(
                                            'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 8 : 5,
                                            'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_skins_settings'         => array(
                                            'title'     =>  __( 'Skin' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 1 : 7,
                                            'description' =>  __( 'Select a skin for Customizr' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_fonts'          => array(
                                            'title'     =>  __( 'Fonts' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 40 : 10,
                                            'description' =>  __( 'Set up the font global settings' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_social_settings'        => array(
                                            'title'     =>  __( 'Social links' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 9 : 20,
                                            'description' =>  __( 'Set up your social links' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_links_settings'         => array(
                                            'title'     =>  __( 'Links style and effects' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 22 : 30,
                                            'description' =>  __( 'Various links settings' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_titles_icons_settings'        => array(
                                            'title'     =>  __( 'Titles icons settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 18 : 40,
                                            'description' =>  __( 'Set up the titles icons options' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_image_settings'         => array(
                                            'title'     =>  __( 'Image settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 95 : 50,
                                            'description' =>  __( 'Various images settings' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_responsive'           => array(
                                            'title'     =>  __( 'Responsive settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 96 : 60,
                                            'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_authors'               => array(
                                            'title'     =>  __( 'Authors' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 220 : 70,
                                            'description' =>  __( 'Post authors settings' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_header_layout'         => array(
                                            'title'    => $this -> is_wp_version_before_4_0 ? __( 'Header design and layout', 'customizr' ) : __( 'Design and layout', 'customizr' ),
                                            'priority' => $this -> is_wp_version_before_4_0 ? 5 : 20,
                                            'panel'   => 'tc-header-panel'
                        ),
                        'nav'           => array(
                                  'title'          => __( 'Navigation' , 'customizr' ),
                                  'theme_supports' => 'menus',
                                  'priority'       => $this -> is_wp_version_before_4_0 ? 10 : 40,
                                  'description'    => sprintf( _n('Your theme supports %s menu. Select which menu you would like to use.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations ), number_format_i18n( $num_locations ) ) . "\n\n" . __('You can edit your menu content on the Menus screen in the Appearance section.'),
                                  'panel'   => 'tc-header-panel'
                        ),

                        'tc_frontpage_settings'       => array(
                                            'title'     =>  __( 'Front Page' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 12 : 10,
                                            'description' =>  __( 'Set up front page options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),

                        'tc_layout_settings'        => array(
                                            'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 15 : 15,
                                            'description' =>  __( 'Set up layout options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),

                        'tc_post_list_settings'        => array(
                                            'title'     =>  __( 'Post lists : blog, archives, ...' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 16 : 20,
                                            'description' =>  __( 'Set up post lists options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_single_post_settings'        => array(
                                            'title'     =>  __( 'Single posts' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 17 : 24,
                                            'description' =>  __( 'Set up single posts options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_breadcrumb_settings'        => array(
                                            'title'     =>  __( 'Breadcrumb' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 11 : 30,
                                            'description' =>  __( 'Set up breadcrumb options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),


                        /*'tc_page_settings'        => array(
                                            'title'     =>  __( 'Pages' , 'customizr' ),
                                            'priority'    =>  25,
                                            'description' =>  __( 'Set up pages options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),*/
                        'tc_post_metas_settings'        => array(
                                            'title'     =>  __( 'Post metas (category, tags, custom taxonomies)' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 50,
                                            'description' =>  __( 'Set up post metas options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_galleries_settings'        => array(
                                            'title'     =>  __( 'Galleries' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 55,
                                            'description' =>  __( 'Set up gallery options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_paragraphs_settings'        => array(
                                            'title'     =>  __( 'Paragraphs' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 55,
                                            'description' =>  __( 'Set up paragraphs options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_comments_settings'          => array(
                                            'title'     =>  __( 'Comments' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 25 : 60,
                                            'description' =>  __( 'Set up comments options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_post_navigation_settings'          => array(
                                            'title'     =>  __( 'Post/Page Navigation' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 30 : 65,
                                            'description' =>  __( 'Set up post/page navigation options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_footer_global_settings'          => array(
                                            'title'     =>  __( 'Footer global settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 40 : 10,
                                            'description' =>  __( 'Set up footer global options' , 'customizr' ),
                                            'panel'   => 'tc-footer-panel'
                        ),
                        'tc_custom_css'           => array(
                                            'title'     =>  __( 'Custom CSS' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 100 : 10,
                                            'description' =>  __( 'Add your own CSS' , 'customizr' ),
                                            'panel'   => 'tc-advanced-panel'
                        ),
                        'tc_performances'           => array(
                                            'title'     =>  __( 'Website Performances' , 'customizr' ),
                                            'priority'    => 20,
                                            'description' =>  __( 'On the web, speed is key ! Improve the load time of your pages with those options.' , 'customizr' ),
                                            'panel'   => 'tc-advanced-panel'
                        ),
                  )

        );//end of add_sections array
    }


    /**
    * Update initial setting_control map
    * hook : tc_add_setting_control_map
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_update_setting_control_map( $_map ) {
      //remove options for original section
      $_to_unset = array(
        'tc_theme_options[tc_breadcrumb]',
        'posts_per_page',
        'tc_theme_options[tc_post_list_length]',
        'tc_theme_options[tc_sidebar_global_layout]',
        'tc_theme_options[tc_sidebar_force_layout]',
        'tc_theme_options[tc_sidebar_post_layout]',
        'tc_theme_options[tc_sidebar_page_layout]',
        'tc_theme_options[tc_social_in_header]',
        'tc_theme_options[tc_social_in_left-sidebar]',
        'tc_theme_options[tc_social_in_right-sidebar]',
        'tc_theme_options[tc_social_in_footer]',
        'tc_theme_options[tc_top_border]',
        'tc_theme_options[tc_custom_css]',
        'tc_theme_options[tc_page_comments]',
        'tc_theme_options[tc_minified_skin]'
      );
      foreach ($_to_unset as $_value) {
        if ( ! isset($_map['add_setting_control'][$_value]) )
          continue;
        unset( $_map['add_setting_control'][$_value] );
      }

      //adds back previously removed settings + brand new settings
      $_new_settings = array(
        /*********** OLD **************/
        //Breadcrumb
              'tc_theme_options[tc_breadcrumb]' => array(
                              'default'       => 1,//Breadcrumb is checked by default
                              'label'         => __( 'Display Breadcrumb' , 'customizr' ),
                              'control'     =>  'TC_controls' ,
                              'section'       => 'tc_breadcrumb_settings' ,
                              'type'          => 'checkbox' ,
                              'priority'      => 1,
              ),

              //Global sidebar layout
              'tc_theme_options[tc_sidebar_global_layout]' => array(
                              'default'       => 'l' ,//Default sidebar layout is on the left
                              'label'         => __( 'Choose the global default layout' , 'customizr' ),
                              'section'     => 'tc_layout_settings' ,
                              'type'          => 'select' ,
                              'choices'     => $this -> tc_layout_choices(),
                              'notice'      => __( 'Note : the home page layout has to be set in the home page section' , 'customizr' ),
                              'priority'      => 10
               ),

               //force default layout on every posts
              'tc_theme_options[tc_sidebar_force_layout]' =>  array(
                              'default'       => 0,
                              'control'     => 'TC_controls' ,
                              'label'         => __( 'Force default layout everywhere' , 'customizr' ),
                              'section'       => 'tc_layout_settings' ,
                              'type'          => 'checkbox' ,
                              'notice'      => __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
                              'priority'      => 20
              ),
              //Post sidebar layout
              'tc_theme_options[tc_sidebar_post_layout]'  =>  array(
                              'default'       => 'l' ,//Default sidebar layout is on the left
                              'label'       => __( 'Choose the posts default layout' , 'customizr' ),
                              'section'     => 'tc_layout_settings' ,
                              'type'        => 'select' ,
                              'choices'   => $this -> tc_layout_choices(),
                              'priority'      => 30
              ),
              //Page sidebar layout
                'tc_theme_options[tc_sidebar_page_layout]'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'       => 40
              ),


              //Post per page
              'posts_per_page'  =>  array(
                              'default'     => get_option( 'posts_per_page' ),
                              'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                              'control'     => 'TC_controls' ,
                              'title'         => __( 'Global Post Lists Settings' , 'customizr' ),
                              'label'         => __( 'Maximum number of posts per page' , 'customizr' ),
                              'section'       => 'tc_post_list_settings' ,
                              'type'          => 'number' ,
                              'step'        => 1,
                              'min'         => 1,
                              'priority'       => 10,
              ),
              //Post list length
              'tc_theme_options[tc_post_list_length]' =>  array(
                                'default'       => 'excerpt',
                                'label'         => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'select' ,
                                'choices'       => array(
                                        'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                        'full'    => __( 'Display the full content' , 'customizr' )
                                        ),
                                'priority'       => 20,
              ),

              /********** NEW **********/
              /* Authors info */
              'tc_theme_options[tc_show_author_info]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display an author box after each single post content" , "customizr" ),
                                'section'       => 'tc_authors',
                                'type'          => 'checkbox',
                                'priority'      => 1,
                                'notice'        =>  __( 'Check this option to display an author info block after each single post content. Note : the Biographical info field must be filled out in the user profile.' , 'customizr' ),
              ),

              /* Fonts */
              'tc_theme_options[tc_fonts]'      => array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.2.9' , '1.0.1') ? 'helvetica_arial' : '_g_fjalla_cantarell',
                                'label'         => __( 'Select a beautiful font pair (headings &amp; default fonts) or single font for your website.' , 'customizr' ),
                                'control'       =>  'TC_controls',
                                'section'       => 'tc_fonts',
                                'type'          => 'select' ,
                                'choices'       => TC_utils::$inst -> tc_get_font( 'list' , 'name' ),
                                'priority'      => 10,
                                'transport'     => 'postMessage',
                                'notice'        => __( "This font picker allows you to preview and select among a handy selection of font pairs and single fonts. If you choose a pair, the first font will be applied to the site main headings : site name, site description, titles h1, h2, h3., while the second will be the default font of your website for any texts or paragraphs." , 'customizr' )
              ),
              'tc_theme_options[tc_body_font_size]'      => array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.2.9', '1.0.1' ) ? 14 : 15,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'label'         => __( 'Set your website default font size in pixels.' , 'customizr' ),
                                'control'       =>  'TC_controls',
                                'section'       => 'tc_fonts',
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 20,
                                'transport'     => 'postMessage',
                                'notice'        => __( "This option sets the default font size applied to any text element of your website, when no font size is already applied." , 'customizr' )
              ),
              /* Header */
              //enable/disable top border
              'tc_theme_options[tc_top_border]' => array(
                                'default'       =>  1,//top border on by default
                                'title'         => __( 'Header design and layout' , 'customizr'),
                                'label'         =>  __( 'Display top border' , 'customizr' ),
                                'control'       =>  'TC_controls' ,
                                'section'       =>  'tc_header_layout' ,
                                'type'          =>  'checkbox' ,
                                'notice'        =>  __( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
                                'priority'      => 5
              ),
              'tc_theme_options[tc_show_tagline]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display the tagline" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 10,
                                'transport'     => 'postMessage'
              ),
              'tc_theme_options[tc_display_boxed_navbar]'  =>  array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.3.13', '1.0.18' ) ? 1 : 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display menu in a box" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'transport'     => 'postMessage',
                                'notice'    => __( 'If checked, this option wraps the header menu/tagline/social in a light grey box.' , 'customizr' ),
              ),
              'tc_theme_options[tc_header_layout]'  =>  array(
                                'default'       => 'left',
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Layout" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'left'      => __( 'Logo / title on the left' , 'customizr' ),
                                        'centered'  => __( 'Logo / title centered' , 'customizr'),
                                        'right'     => __( 'Logo / title on the right' , 'customizr' )
                                ),
                                'priority'      => 30,
                                'transport'     => 'postMessage'
              ),
              'tc_theme_options[tc_sticky_header]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'title'         => __( 'Sticky header settings' , 'customizr'),
                                'label'         => __( "Sticky on scroll" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'transport'     => 'postMessage',
                                'notice'    => __( 'If checked, this option makes the header stick to the top of the page on scroll down.' , 'customizr' )
              ),
              'tc_theme_options[tc_sticky_show_tagline]'  =>  array(
                                'default'       => 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : display the tagline" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_show_title_logo]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : display the title / logo" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_shrink_title_logo]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : shrink title / logo" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 60,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_show_menu]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : display the menu" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 60,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_transparent_on_scroll]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : semi-transparent on scroll" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 67,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_z_index]'  =>  array(
                                'default'       => 100,
                                'control'       => 'TC_controls' ,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'label'         => __( "Set the header z-index" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 70,
                                'transport'     => 'postMessage',
                                'notice'    => sprintf('%1$s <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/z-index" target="_blank">%2$s</a> ?',
                                    __( "What is" , 'customizr' ),
                                    __( "the z-index" , 'customizr')
                                ),
              ),

              /* Menu */
              'tc_theme_options[tc_menu_position]'  =>  array(
                                'default'       => 'left',
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Menu position" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                        'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                ),
                                'priority'      => 20,
                                'transport'     => 'postMessage'
              ),
              'tc_theme_options[tc_menu_submenu_fade_effect]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Reveal the sub-menus blocks with a fade effect" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_menu_submenu_item_move_effect]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Hover move effect for the sub menu items" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_menu_resp_dropdown_limit_to_viewport]'  =>  array(
                                'default'       => 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "In responsive mode, limit the height of the dropdown menu block to the visible viewport" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50,
                                //'transport'     => 'postMessage',
              ),
              /* Links */
              'tc_theme_options[tc_link_hover_effect]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Fade effect on link hover" , "customizr" ),
                                'section'       => 'tc_links_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_ext_link_style]'  =>  array(
                                'default'       => 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display an icon next to external links" , "customizr" ),
                                'section'       => 'tc_links_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                                'transport'     => 'postMessage'
              ),

              'tc_theme_options[tc_ext_link_target]'  =>  array(
                                'default'       => 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Open external links in a new tab" , "customizr" ),
                                'section'       => 'tc_links_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                                'transport'     => 'postMessage'
              ),
              /* Breadcrumb*/
              'tc_theme_options[tc_show_breadcrumb_home]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb on home page" , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20
              ),
              'tc_theme_options[tc_show_breadcrumb_in_pages]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb in pages" , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30

              ),
              'tc_theme_options[tc_show_breadcrumb_in_single_posts]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb in single posts" , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40

              ),
              'tc_theme_options[tc_show_breadcrumb_in_post_lists]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb in posts lists : blog page, archives, search results..." , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50

              ),

              /* Icons */
              'tc_theme_options[tc_show_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display icons next to titles" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 10,
                                'notice'    => __( 'When this option is checked, a contextual icon is displayed next to the titles of pages, posts, archives, and WP built-in widgets.' , 'customizr' ),
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_page_title_icon]'  =>  array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display a page icon next to the page title" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_title_icon]'  =>  array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display a post icon next to the single post title" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 30,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_archive_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display an icon next to the archive title" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, an archive type icon is displayed in the heading of every types of archives, on the left of the title. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                'priority'      => 40,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_list_title_icon]'  =>  array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.3.0' , '1.0.11' ) ? 1 : 0,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display an icon next to each post title in an archive page" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, a post type icon is displayed on the left of each post titles in an archive page. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                'priority'      => 50,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_sidebar_widget_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "WP sidebar widgets : display icons next to titles" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 60,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_footer_widget_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "WP footer widgets : display icons next to titles" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 70,
                                'transport'   => 'postMessage'
              ),


              /* Post metas */
              'tc_theme_options[tc_show_post_metas]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts metas" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, the post metas (like taxonomies, date and author) are displayed below the post titles.' , 'customizr' ),
                                'priority'      => 5,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_post_metas_design]'  =>  array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'buttons' : 'no-buttons',
                                'control'     => 'TC_controls' ,
                                'title'         => __( 'Metas Design' , 'customizr' ),
                                'label'         => __( "Select a design for the post metas" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                    'buttons'     => __( 'Buttons and text' , 'customizr' ),
                                    'no-buttons'  => __( 'Text only' , 'customizr' )
                                ),
                                'priority'      => 10
              ),
              'tc_theme_options[tc_show_post_metas_home]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls' ,
                                'title'         => __( 'Select the contexts' , 'customizr' ),
                                'label'         => __( "Display posts metas on home" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 15,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_metas_single_post]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts metas for single posts" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_metas_post_lists]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts metas in post lists (archives, blog page)" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 25,
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_show_post_metas_categories]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'title'         => __( 'Select the metas to display' , 'customizr' ),
                                'label'         => __( "Display hierarchical taxonomies (like categories)" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 30
              ),

              'tc_theme_options[tc_show_post_metas_tags]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display non-hierarchical taxonomies (like tags)" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 35
              ),

              'tc_theme_options[tc_show_post_metas_publication_date]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display the publication date" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 40
              ),
              'tc_theme_options[tc_show_post_metas_author]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display the author" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 45
              ),
              'tc_theme_options[tc_show_post_metas_update_date]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display the update date" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 50,
                                'notice'    => __( 'If this option is checked, additional date informations about the the last post update can be displayed (nothing will show up if the post has never been updated).' , 'customizr' ),
              ),

              'tc_theme_options[tc_post_metas_update_date_format]'  =>  array(
                                'default'       => 'days',
                                'control'       => 'TC_controls',
                                'label'         => __( "Select the last update format" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'days'     => __( 'Nb of days since last update' , 'customizr' ),
                                        'date'     => __( 'Date of the last update' , 'customizr' )
                                ),
                                'priority'      => 55
              ),

              'tc_theme_options[tc_post_metas_update_notice_in_title]'  =>  array(
                                'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.3.2' , '1.0.11' ) ? 1 : 0,
                                'control'       => 'TC_controls',
                                'title'         => __( 'Recent update notice after post titles' , 'customizr' ),
                                'label'         => __( "Display a recent update notice" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 65,
                                'notice'    => __( 'If this option is checked, a customizable recent update notice is displayed next to the post title.' , 'customizr' )
              ),
              'tc_theme_options[tc_post_metas_update_notice_interval]'  =>  array(
                                'default'       => 10,
                                'control'       => 'TC_controls',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'label'         => __( "Display the notice if the last update is less (strictly) than n days old" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 70,
                                'notice'    => __( 'Set a maximum interval (in days) during which the last update notice will be displayed.' , 'customizr' ),
              ),
              'tc_theme_options[tc_post_metas_update_notice_text]'  =>  array(
                                'default'       => __( "Recently updated !" , "customizr" ),
                                'control'       => 'TC_controls',
                                'label'         => __( "Update notice text" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'text',
                                'priority'      => 75,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_post_metas_update_notice_format]'  =>  array(
                                'default'       => 'label-default',
                                'control'       => 'TC_controls',
                                'label'         => __( "Update notice style" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'label-default'   => __( 'Default (grey)' , 'customizr' ),
                                        'label-success'   => __( 'Success (green)' , 'customizr' ),
                                        'label-warning'   => __( 'Alert (orange)' , 'customizr' ),
                                        'label-important' => __( 'Important (red)' , 'customizr' ),
                                        'label-info'      => __( 'Info (blue)' , 'customizr' )
                                ),
                                'priority'      => 80,
                                'transport'   => 'postMessage'
              ),

              /* Post list layout */
              'tc_theme_options[tc_post_list_excerpt_length]'  =>  array(
                                'default'       => 50,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Set the excerpt length (in number of words) " , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 23
              ),
              'tc_theme_options[tc_post_list_show_thumb]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'title'         => __( 'Thumbnails options' , 'customizr' ),
                                'label'         => __( "Display the post thumbnails" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 68,
                                'notice'        => sprintf( '%s %s' , __( 'When this option is checked, the post thumbnails are displayed in all post lists : blog, archives, author page, search pages, ...' , 'customizr' ), __( 'Note : thumbnails are always displayed when the grid layout is choosen.' , 'customizr') )
              ),
              'tc_theme_options[tc_post_list_use_attachment_as_thumb]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "If no featured image is set, use the last image attached to this post." , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 70
              ),
              'tc_theme_options[tc_post_list_default_thumb]' => array(
                                'control'       =>  'TC_Customize_Upload_Control',
                                'label'         => __( 'Upload a default thumbnail' , 'customizr' ),
                                'section'       =>  'tc_post_list_settings',
                                'type'          =>  'tc_upload',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number'),
                                'priority'      =>  73,
              ),

              'tc_theme_options[tc_post_list_thumb_shape]'  =>  array(
                                'default'       => 'rounded',
                                'control'     => 'TC_controls' ,
                                'title'         => __( 'Thumbnails options for the alternate thumbnails layout' , 'customizr' ),
                                'label'         => __( "Thumbnails shape" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'rounded'               => __( 'Rounded, expand on hover' , 'customizr'),
                                        'rounded-expanded'      => __( 'Rounded, no expansion' , 'customizr'),
                                        'squared'               => __( 'Squared, expand on hover' , 'customizr'),
                                        'squared-expanded'      => __( 'Squared, no expansion' , 'customizr'),
                                        'rectangular'           => __( 'Rectangular with no effect' , 'customizr'  ),
                                        'rectangular-blurred'   => __( 'Rectangular with blur effect on hover' , 'customizr'  ),
                                        'rectangular-unblurred' => __( 'Rectangular with unblur effect on hover' , 'customizr'),
                                ),
                                'priority'      => 77
              ),
              'tc_theme_options[tc_post_list_thumb_height]' => array(
                                'default'       => 250,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                'section'     => 'tc_post_list_settings' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'     => 0,
                                'priority'      => 80,
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_post_list_thumb_position]'  =>  array(
                                'default'       => 'right',
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Thumbnails position" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'top'     => __( 'Top' , 'customizr' ),
                                        'right'   => __( 'Right' , 'customizr' ),
                                        'bottom'    => __( 'Bottom' , 'customizr' ),
                                        'left'    => __( 'Left' , 'customizr' ),
                                ),
                                'priority'      => 90
              ),
              'tc_theme_options[tc_post_list_thumb_alternate]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Alternate thumbnail/content" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 95
              ),

              /* ARCHIVE TITLES */
              'tc_theme_options[tc_cat_title]'  =>  array(
                                'default'       => '',
                                'title'         => __( 'Archive titles' , 'customizr' ),
                                'label'       => __( 'Category pages titles' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_post_list_settings' ,
                                'type'        => 'text' ,
                                'priority'       => 100
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_theme_options[tc_tag_title]'  =>  array(
                                'default'         => '',
                                'label'       => __( 'Tag pages titles' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_post_list_settings' ,
                                'type'        => 'text' ,
                                'priority'       => 105
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_theme_options[tc_author_title]'  =>  array(
                                'default'         => '',
                                'label'       => __( 'Author pages titles' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_post_list_settings' ,
                                'type'        => 'text' ,
                                'priority'       => 110
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_theme_options[tc_search_title]'  =>  array(
                                'default'         => __( 'Search Results for :' , 'customizr' ),
                                'label'       => __( 'Search results page titles' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_post_list_settings' ,
                                'type'        => 'text' ,
                                'priority'       => 115
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),

              /* SINGLE POST SETTINGS */
              'tc_theme_options[tc_single_post_thumb_location]'  =>  array(
                                'default'       => 'hide',
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Post thumbnail position" , "customizr" ),
                                'section'       => 'tc_single_post_settings' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'hide'                    => __( "Don't display" , 'customizr' ),
                                        '__before_main_wrapper|200'   => __( 'Before the title in full width' , 'customizr' ),
                                        '__before_content|0'     => __( 'Before the title boxed' , 'customizr' ),
                                        '__after_content_title|10'    => __( 'After the title' , 'customizr' ),
                                ),
                                'priority'      => 10
              ),
              'tc_theme_options[tc_single_post_thumb_height]' => array(
                                'default'       => 250,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                'section'     => 'tc_single_post_settings' ,
                                'type'        => 'number' ,
                                'step'        => 1,
                                'min'         => 0,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              /* Galleries */
              'tc_theme_options[tc_enable_gallery]'  =>  array(
                                'default'       => 1,
                                'label'         => __('Enable Customizr galleries' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'notice'         => __( "Apply Customizr effects to galleries images" , "customizr" ),
                                'section'       => 'tc_galleries_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),
              'tc_theme_options[tc_gallery_fancybox]'=>  array(
                                'default'       => 1,
                                'label'         => __('Enable Lightbox effect in galleries' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'notice'         => __( "Apply lightbox effects to galleries images" , "customizr" ),
                                'section'       => 'tc_galleries_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),
              'tc_theme_options[tc_gallery_style]'=>  array(
                                'default'       => 1,
                                'label'         => __('Enable Customizr effects on hover' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'notice'         => __( "Apply nice on hover expansion effect to the galleries images" , "customizr" ),
                                'section'       => 'tc_galleries_settings' ,
                                'type'          => 'checkbox',
                                'transport'     => 'postMessage',
                                'priority'      => 1
              ),
              /* Paragraphs */
              'tc_theme_options[tc_enable_dropcap]'  =>  array(
                                'default'       => 0,
                                'title'         => __( 'Drop caps', 'customizr'),
                                'label'         => __('Enable drop caps' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'notice'         => __( "Apply a drop cap to the first paragraph of your post / page content" , "customizr" ),
                                'section'       => 'tc_paragraphs_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),
              'tc_theme_options[tc_dropcap_minwords]'  =>  array(
                                'default'       => 50,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Apply a drop cap when the paragraph includes at least the following number of words :" , "customizr" ),
                                'notice'         => __( "(number of words)" , "customizr" ),
                                'section'       => 'tc_paragraphs_settings' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 1,
                                'priority'      => 10
              ),
              'tc_theme_options[tc_dropcap_design]' => array(
                                'default'     => 'skin-shadow',
                                'control'     => 'TC_controls',
                                'label'       => __( 'Drop cap style' , 'customizr' ),
                                'section'     => 'tc_paragraphs_settings',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'skin-shadow'    => __( "Skin color with shadow" , 'customizr' ),
                                        'simple-black'   => __( 'Simple black' , 'customizr' ),
                                ),
                                'priority'    => 20,
              ),
              'tc_theme_options[tc_post_dropcap]'  =>  array(
                                'default'       => 0,
                                'label'         => __('Enable drop caps in posts' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'notice'         => __( "Apply a drop cap to the first paragraph of your single posts content" , "customizr" ),
                                'section'       => 'tc_paragraphs_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 30
              ),
              'tc_theme_options[tc_page_dropcap]'  =>  array(
                                'default'       => 0,
                                'label'         => __('Enable drop caps in pages' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'notice'         => __( "Apply a drop cap to the first paragraph of your pages" , "customizr" ),
                                'section'       => 'tc_paragraphs_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 40
              ),


              /* Comments */
              'tc_theme_options[tc_comment_show_bubble]'  =>  array(
                                'default'       => 1,
                                'title'         => __('Comments bubbles' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display the number of comments in a bubble next to the post title" , "customizr" ),
                                'section'       => 'tc_comments_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),

              'tc_theme_options[tc_comment_bubble_shape]' => array(
                                'default'     => 'default',
                                'control'     => 'TC_controls',
                                'label'       => __( 'Comments bubble shape' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'default'             => __( "Small bubbles" , 'customizr' ),
                                        'custom-bubble-one'   => __( 'Large bubbles' , 'customizr' ),
                                ),
                                'priority'    => 10,
              ),

              'tc_theme_options[tc_comment_bubble_color_type]' => array(
                                'default'     => TC_utils::$inst -> tc_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'custom' : 'skin',
                                'control'     => 'TC_controls',
                                'label'       => __( 'Comments bubble color' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'skin'     => __( "Skin color" , 'customizr' ),
                                        'custom'   => __( 'Custom' , 'customizr' ),
                                ),
                                'priority'    => 20,
              ),

              'tc_theme_options[tc_comment_bubble_color]' => array(
                                'default'     => TC_utils::$inst -> tc_user_started_before_version( '3.3.2' , '1.0.11' ) ? '#F00' : TC_utils::$inst -> tc_get_skin_color(),
                                'control'     => 'WP_Customize_Color_Control',
                                'label'       => __( 'Comments bubble color' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'        =>  'color' ,
                                'priority'    => 30,
                                'sanitize_callback'    => array( $this, 'tc_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color',
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_page_comments]'  =>  array(
                                'default'     => 0,
                                'control'     => 'TC_controls',
                                'title'       => __( 'Other comments settings' , 'customizr'),
                                'label'       => __( 'Enable comments on pages' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'        => 'checkbox',
                                'priority'    => 40,
                                'notice'      => sprintf('%1$s<br/> %2$s <a href="%3$s" target="_blank">%4$s</a>',
                                    __( 'If checked, this option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
                                    __( "You can also change other comments settings in :" , 'customizr'),
                                    admin_url() . 'options-discussion.php',
                                    __( 'the discussion settings page.' , 'customizr' )
                                ),
              ),
              'tc_theme_options[tc_post_comments]'  =>  array(
                                'default'     => 1,
                                'control'     => 'TC_controls',
                                'label'       => __( 'Enable comments on posts' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'        => 'checkbox',
                                'priority'    => 45,
                                'notice'      => sprintf('%1$s <a href="%2$s" target="_blank">%3$s</a>.<br/>%4$s <a href="%5$s" target="_blank">%6$s</a>',
                                    __( 'If checked, this option enables comments on all types of single posts. You can disable comments for a single post in quick edit mode from the' , 'customizr' ),
                                    'http://codex.wordpress.org/Posts_Screen',
                                    __( 'post screen', 'customizr'),
                                    __( "You can also change other comments settings in the" , 'customizr'),
                                    admin_url() . 'options-discussion.php',
                                    __( 'discussion settings page.' , 'customizr' )
                                ),
              ),
              'tc_theme_options[tc_show_comment_list]'  =>  array(
                                'default'     => 1,
                                'control'     => 'TC_controls',
                                'label'       => __( 'Display the comment list' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'        => 'checkbox',
                                'priority'    => 50,
                                'notice'      =>__( 'By default, WordPress displays the past comments, even if comments are disabled in posts or pages. Unchecking this option allows you to not display this comment history.' , 'customizr' )
              ),

              /* Post navigation */
              'tc_theme_options[tc_show_post_navigation]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts navigation" , "customizr" ),
                                'section'       => 'tc_post_navigation_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, the posts navigation is displayed below the posts' , 'customizr' ),
                                'priority'      => 5,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_navigation_page]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls' ,
                                'title'         => __( 'Select the contexts' , 'customizr' ),
                                'label'         => __( "Display navigation in pages" , "customizr" ),
                                'section'       => 'tc_post_navigation_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 10,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_navigation_single]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts navigation in single posts" , "customizr" ),
                                'section'       => 'tc_post_navigation_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_navigation_archive]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts navigation in post lists (archives, blog page, categories, search results ..)" , "customizr" ),
                                'section'       => 'tc_post_navigation_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 25,
                                'transport'   => 'postMessage'
              ),
              /* Footer */
              'tc_theme_options[tc_show_back_to_top]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display a back to top arrow on scroll" , "customizr" ),
                                'section'       => 'tc_footer_global_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),

              /* SOCIALS */
              //Social position checkboxes
              'tc_theme_options[tc_social_in_header]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in header' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 10,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_social_in_footer]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in footer' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 15,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_social_in_left-sidebar]' =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in left sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 20,
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_social_in_right-sidebar]'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in right sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 25,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_social_in_sidebar_title]'  =>  array(
                                'default'       => __( 'Social links' , 'customizr' ),
                                'label'       => __( 'Social link title in sidebars' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'text' ,
                                'priority'       => 30,
                                'transport'   => 'postMessage',
                                'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_theme_options[tc_custom_css]' =>  array(
                                'sanitize_callback' => 'wp_filter_nohtml_kses',
                                'sanitize_js_callback' => 'wp_filter_nohtml_kses',
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
                                'section'     => 'tc_custom_css' ,
                                'type'        => 'textarea' ,
                                'notice'    => sprintf('%1$s <a href="%4$ssnippet/creating-child-theme-customizr/" title="%3$s" target="_blank">%2$s</a>',
                                    __( "Use this field to test small chunks of CSS code. For important CSS customizations, you'll want to modify the style.css file of a" , 'customizr' ),
                                    __( 'child theme.' , 'customizr'),
                                    __( 'How to create and use a child theme ?' , 'customizr'),
                                    TC_WEBSITE
                                ),
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_minified_skin]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Performance : use the minified CSS stylesheet", 'customizr' ),
                                'section'     => 'tc_performances' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'Using the minified version of the skin stylesheet will speed up your webpage load time.' , 'customizr' ),
              ),
              'tc_theme_options[tc_img_smart_load]'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Load images on scroll' , 'customizr' ),
                                'control'     =>  'TC_controls' ,
                                'section'     => 'tc_performances' ,
                                'type'        => 'checkbox',
                                'priority'    => 20,
                                'notice'      => __('Check this option to delay the loading of non visible images. Images below the viewport will be loaded dynamically on scroll. This can boost performances by reducing the weight of long web pages with images.' , 'customizr')
              ),


              //Default slider's height
              'tc_theme_options[tc_slider_default_height]' => array(
                                'default'       => 500,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Set slider's height in pixels" , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'       => 0,
                                'priority'      => 52,
                                'transport' => 'postMessage'
              ),
              'tc_theme_options[tc_slider_default_height_apply_all]'  =>  array(
                                'default'       => 1,
                                'label'       => __( 'Apply this height to all sliders' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 53,
              ),
              'tc_theme_options[tc_slider_change_default_img_size]'  =>  array(
                                'default'       => 0,
                                'label'       => __( "Replace the default image slider's height" , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 54,
                                'notice'    => sprintf('%1$s <a href="http://doc.presscustomizr.com/customizr/recommended-plugins/#images" target="_blank">%2$s</a>',
                                    __( "If this option is checked, your images will be resized with your custom height on upload. This is better for your overall loading performance." , 'customizr' ),
                                    __( "You might want to regenerate your thumbnails." , 'customizr')
                                ),
              )
            );

      $_map['add_setting_control'] = array_merge($_map['add_setting_control'] , $_new_settings );
      return $_map;
    }


    /**
    * Update initial setting_control map defined in class-fire-utils.php.
    *
    * @package Customizr
    * @since Customizr 3.2.18
    */
    function tc_grid_map( $_map ) {
        $_new_settings = array(
          'tc_theme_options[tc_post_list_grid]'  =>  array(
                            'default'       => TC_utils::$inst -> tc_user_started_before_version( '3.2.18', '1.0.13' ) ? 'alternate' : 'grid',
                            'control'       => 'TC_controls' ,
                            'title'         => __( 'Post List Design' , 'customizr' ),
                            'label'         => __( 'Select a Layout' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'select',
                            'choices'       => array(
                                    'alternate'       => __( 'Alternate thumbnails layout' , 'customizr'),
                                    'grid'            => __( 'Grid layout' , 'customizr')
                            ),
                            'priority'      => 40,
                            'notice'    => __( 'When you select the grid Layout, the post content is limited to the excerpt.' , 'customizr' ),
          ),
          'tc_theme_options[tc_grid_columns]'  =>  array(
                            'default'       => '3',
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Number of columns per row' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'select',
                            'choices'       => array(
                                    '1'                     => __( '1' , 'customizr'),
                                    '2'                     => __( '2' , 'customizr'),
                                    '3'                     => __( '3' , 'customizr'),
                                    '4'                     => __( '4' , 'customizr')
                            ),
                            'priority'      => 45,
                            'notice'        => __( 'Note : columns are limited to 3 for single sidebar layouts and to 2 for double sidebar layouts.' , 'customizr' )
          ),
          'tc_theme_options[tc_grid_expand_featured]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Expand the last sticky post (for home and blog page only)' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 47
          ),
          'tc_theme_options[tc_grid_in_blog]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Apply the grid layout to Home/Blog' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 57
          ),
          'tc_theme_options[tc_grid_in_archive]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Apply the grid layout to Archives (archives, categories, author posts)' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 58
          ),
          'tc_theme_options[tc_grid_in_search]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Apply the grid layout to Search results' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 60,
                            'notice'        => __( 'Unchecked contexts are displayed with the alternate thumbnails layout.' , 'customizr' ),
           ),
          'tc_theme_options[tc_grid_shadow]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Apply a shadow to each grid items' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 61,
                            'transport'   => 'postMessage'
           ),
          'tc_theme_options[tc_grid_bottom_border]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Apply a colored bottom border to each grid items' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 62,
                            'transport'   => 'postMessage'
           ),
          'tc_theme_options[tc_grid_icons]'  =>  array(
                            'default'       => 1,
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Display post format icons in the background' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'checkbox',
                            'priority'      => 63,
                            'transport'   => 'postMessage'
           ),
          'tc_theme_options[tc_grid_num_words]'  =>  array(
                            'default'       => 10,
                            'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                            'control'       => 'TC_controls' ,
                            'label'         => __( 'Max. length for post titles (in words)' , "customizr" ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 1,
                            'priority'      => 64
           ),
          'tc_theme_options[tc_grid_thumb_height]' => array(
                            'default'       => 350,
                            'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                            'control'       => 'TC_controls' ,
                            'title'         => __( 'Thumbnails max height for the grid layout' , 'customizr' ),
                            'label'         => __( "Set the post grid thumbnail's max height in pixels" , 'customizr' ),
                            'section'       => 'tc_post_list_settings' ,
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 0,
                            'priority'      => 65
                            //'transport'   => 'postMessage'
          )
        );//$_new_settings

      $_map['add_setting_control'] = array_merge($_map['add_setting_control'] , $_new_settings );
      return $_map;

    }//end of fn

  }//end of class
endif;
