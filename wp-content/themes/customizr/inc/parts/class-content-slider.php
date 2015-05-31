<?php
/**
* Slider Model / Views / Helpers Class
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013 - 2015 , Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_slider' ) ) :
class TC_slider {

  static $instance;

  function __construct () {
    self::$instance =& $this;
    add_action( 'template_redirect'        , array($this, 'tc_set_slider_hooks') );
    //set user customizer options. @since v3.2.0
    add_filter( 'tc_slider_layout_class'   , array( $this , 'tc_set_slider_wrapper_class' ) );
    //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
    //fired on hook : wp_enqueue_scripts
    //Set thumbnail specific design based on user options
    //Set user defined height
    add_filter( 'tc_user_options_style'    , array( $this , 'tc_write_slider_inline_css' ) );
    //tc_slider_height is fired in TC_slider::tc_write_slider_inline_css()
    add_filter( 'tc_slider_height'         , array( $this, 'tc_set_demo_slider_height') );
  }//end of construct






  /******************************
  HOOK SETUP
  *******************************/
  /**
  * callback of template_redirect
  * Set slider hooks
  * @return  void
  */
  function tc_set_slider_hooks() {
    //get slides model
    //extract $slider_name_id, $slides, $layout_class, $img_size
    extract( $this -> tc_get_slider_model() );
    //returns nothing if no slides to display
    if ( ! isset($slides) || ! $slides )
      return;

    add_action( '__after_header'           , array( $this , 'tc_slider_display' ) );
    add_action( '__after_carousel_inner'   , array( $this , 'tc_slider_control_view' ) );

    //adds the center-slides-enabled css class
    add_filter( 'tc_carousel_inner_classes', array( $this, 'tc_set_inner_class') );
  }






  /******************************
  MODELS
  *******************************/
  /**
  * Return a single slide model
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  private function tc_get_single_slide_model( $_loop_index , $id , $img_size ) {
    //check if slider enabled for this attachment and go to next slide if not
    $slider_checked         = esc_attr(get_post_meta( $id, $key = 'slider_check_key' , $single = true ));
    if ( ! isset( $slider_checked) || $slider_checked != 1 )
      return;

    //title
    $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
    $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );
    $title                  = ( strlen($title) > $default_title_length ) ? substr( $title,0,strpos( $title, ' ' , $default_title_length) ). ' ...' : $title;

    //lead text
    $text                   = get_post_meta( $id, $key = 'slide_text_key' , $single = true );
    $default_text_length    = apply_filters( 'tc_slide_text_length', 250 );
    $text                   = ( strlen($text) > $default_text_length ) ? substr( $text,0,strpos( $text, ' ' ,$default_text_length) ). ' ...' : $text;

    //button text
    $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
    $default_button_length  = apply_filters( 'tc_slide_button_length', 80 );
    $button_text            = ( strlen($button_text) > $default_button_length ) ? substr( $button_text,0,strpos( $button_text, ' ' ,$default_button_length)). ' ...' : $button_text;

    //link post id
    $link_id                = esc_attr(get_post_meta( $id, $key = 'slide_link_key' , $single = true ));

    //button link
    $link_url               = $link_id ? get_permalink( $link_id ) : 'javascript:void(0)';

    //checks if $text_color is set and create an html style attribute
    $text_color             = esc_attr(get_post_meta( $id, $key = 'slide_color_key' , $single = true ));
    $color_style            = ( $text_color != null) ? 'style="color:'.$text_color.'"' : '';

    //attachment image
    $alt                    = apply_filters( 'tc_slide_background_alt' , trim(strip_tags(get_post_meta( $id, '_wp_attachment_image_alt' , true))) );
    $slide_background       = wp_get_attachment_image( $id, $img_size, false, array( 'class' => 'slide' , 'alt' => $alt ) );

    //adds all values to the slide array only if the content exists (=> handle the case when an attachment has been deleted for example). Otherwise go to next slide.
    if ( !isset($slide_background) || empty($slide_background) )
      return;

    return array(
      'title'               =>  $title,
      'text'                =>  $text,
      'button_text'         =>  $button_text,
      'link_id'             =>  $link_id,
      'link_url'            =>  $link_url,
      'active'              =>  ( 0 == $_loop_index ) ? 'active' : '',
      'color_style'         =>  $color_style,
      'slide_background'    =>  $slide_background
    );
  }



  /**
  * Helper
  * Return an array of the slide models from option or default
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  private function tc_get_the_slides( $slider_name_id, $img_size ) {
    //returns the default slider if requested
    if ( 'demo' == $slider_name_id )
      return apply_filters( 'tc_default_slides', TC_init::$instance -> default_slides );

    //if not demo, we get slides from options
    $all_sliders    = TC_utils::$inst -> tc_opt( 'tc_sliders');
    $saved_slides   = ( isset($all_sliders[$slider_name_id]) ) ? $all_sliders[$slider_name_id] : false;

    //if the slider not longer exists or exists but is empty, return false
    if ( !isset($saved_slides) || !is_array($saved_slides) || empty($saved_slides) )
      return;

    //inititalize the slides array
    $slides   = array();

    //init slide active state index
    $_loop_index        = 0;

    //GENERATE SLIDES ARRAY
    foreach ( $saved_slides as $s ) {
      $slide_object           = get_post($s);
      //next loop if attachment does not exist anymore (has been deleted for example)
      if ( ! isset( $slide_object) )

        continue;
      $id                     = $slide_object -> ID;
      if ( ! $this -> tc_get_single_slide_model( $_loop_index, $id, $img_size ) )
        continue;

      $slides[$id] = $this -> tc_get_single_slide_model( $_loop_index, $id , $img_size );

      $_loop_index++;
    }//end of slides loop
    //returns the slides or false if nothing
    return ! empty($slides) ? $slides : false;
  }



  /**
  * return the slider block model
  * @return  array($slider_name_id, $slides, $layout_class)
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  private function tc_get_slider_model() {
    global $wp_query;

    //Do we have a slider to display in this context ?
    if ( ! $this -> tc_is_slider_possible() )
      return array();

    //gets the actual page id if we are displaying the posts page
    $queried_id                   = $this -> tc_get_real_id();

    $slider_name_id               = $this -> tc_get_current_slider( $queried_id );

    if ( ! $this -> tc_is_slider_active( $queried_id) )
      return array();

    //gets slider options if any
    $layout_value                 = tc__f('__is_home') ? TC_utils::$inst->tc_opt( 'tc_slider_width' ) : esc_attr(get_post_meta( $queried_id, $key = 'slider_layout_key' , $single = true ));
    $layout_value                 = apply_filters( 'tc_slider_layout', $layout_value, $queried_id );

    //declares the layout vars
    $layout_class                 = implode( " " , apply_filters( 'tc_slider_layout_class' , ( 0 == $layout_value ) ? array('container', 'carousel', 'customizr-slide') : array('carousel', 'customizr-slide') ) );
    $img_size                     = apply_filters( 'tc_slider_img_size' , ( 0 == $layout_value ) ? 'slider' : 'slider-full');

    //get slides
    $slides                       = $this -> tc_get_the_slides( $slider_name_id , $img_size );

    return compact( "slider_name_id", "slides", "layout_class" , "img_size" );
  }





  /******************************
  VIEWS
  *******************************/
  /**
  * Slider View
  * Displays the slider based on the context : home, post/page.
  * hook : __after_header
  * @package Customizr
  * @since Customizr 1.0
  *
  */
  function tc_slider_display() {
    //get slides model
    //extract $slider_name_id, $slides, $layout_class, $img_size
    extract( $this -> tc_get_slider_model() );
    //returns nothing if no slides to display
    if ( ! isset($slides) || ! $slides )
      return;

    //define carousel inner classes
    $_inner_classes = implode( ' ' , apply_filters( 'tc_carousel_inner_classes' , array( 'carousel-inner' ) ) );

    ob_start();
    ?>
    <div id="customizr-slider" class="<?php echo $layout_class ?> ">

        <?php if ( 'demo' == $slider_name_id || ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_display_slide_loader') ) && apply_filters( 'tc_display_slider_loader' , true ) ) ) : ?>
          <div class="tc-slider-loader-wrapper">
            <div class="tc-img-gif-loader">
              <img data-no-retina alt="loading" src="<?php echo apply_filters('tc_slider_loader_src' , sprintf( '%1$s/%2$s' , TC_BASE_URL , 'inc/assets/img/slider-loader.gif') ) ?>">
            </div>
          </div>
        <?php endif; ?>

        <?php do_action( '__before_carousel_inner' , $slides )  ?>

        <div class="<?php echo $_inner_classes?>">
          <?php
            foreach ($slides as $id => $data) {
              $this -> tc_render_single_slide_view( $id, $data , $slider_name_id, $img_size );
            }
          ?>
        </div><!-- /.carousel-inner -->

        <?php  do_action( '__after_carousel_inner' , $slides )  ?>

    </div><!-- /#customizr-slider -->

    <?php
    $html = ob_get_contents();
    if ($html) ob_end_clean();
    echo apply_filters( 'tc_slider_display', $html, $slider_name_id );
  }


  /**
  * Single slide view
  * Renders a single slide
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  public function tc_render_single_slide_view( $id, $data , $slider_name_id, $img_size ) {
      $slide_class = sprintf('%1$s %2$s',
        $data['active'],
        'slide-'.$id
      );
      ?>
    <div class="item <?php echo $slide_class; ?>">

      <div class="<?php echo apply_filters( 'tc_slide_content_class', sprintf('carousel-image %1$s' , $img_size ) ); ?>">
        <?php
          do_action('__before_all_slides');
          do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id ) );

            echo apply_filters( 'tc_slide_background', $data['slide_background'], $data['link_url'], $id, $slider_name_id );

          do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id ) );
          do_action('__after_all_slides');
        ?>
      </div> <!-- .carousel-image -->
      <?php
        if ( $data['title'] != null || $data['text'] != null || $data['button_text'] != null ) {
          //apply filters first
          $data['title']          = isset($data['title']) ? apply_filters( 'tc_slide_title', $data['title'] , $id, $slider_name_id ) : '';
          $data['text']           = isset($data['text']) ? esc_html( apply_filters( 'tc_slide_text', $data['text'], $id, $slider_name_id ) ) : '';
          $data['color_style']    = apply_filters( 'tc_slide_color', $data['color_style'], $id, $slider_name_id );
          $data['link_id']        = apply_filters( 'tc_slide_link_id', $data['link_id'], $id, $slider_name_id );
          $data['link_url']       = ( 'demo' == $slider_name_id && is_null($data['link_url']) ) ? admin_url().'customize.php' : $data['link_url'];
          $data['link_url']       = apply_filters( 'tc_slide_link_url', $data['link_url'], $id, $slider_name_id );
          $data['button_text']    = isset($data['button_text']) ? apply_filters( 'tc_slide_button_text', $data['button_text'], $id, $slider_name_id ) : '';

          //computes the link
          $button_link            = ( !is_user_logged_in() && 'demo' == $slider_name_id ) ? 'javascript:void(0)' : $data['link_url'];
          $button_link            = ( $data['link_id'] != null && $data['link_url'] != null ) ? $data['link_url'] : $button_link;

          printf('<div class="carousel-caption">%1$s %2$s %3$s</div>',
            //title
            ( $data['title'] != null ) ? sprintf('<%1$s %2$s>%3$s</%1$s>',
                                  apply_filters( 'tc_slide_title_tag', 'h1', $slider_name_id ),
                                  $data['color_style'],
                                  $data['title']
                                ) : '',
            //lead text
            ( $data['text'] != null ) ? sprintf('<p class="lead" %1$s>%2$s</p>',
                                  $data['color_style'],
                                  $data['text']
                                ) : '',
            //button call to action
            ( $data['button_text'] != null) ? sprintf('<a class="%1$s" href="%2$s">%3$s</a>',
                                        apply_filters( 'tc_slide_button_class', 'btn btn-large btn-primary', $slider_name_id ),
                                        $button_link,
                                        $data['button_text']
                                      ) : ''
          );
        }//end if there content to show in caption

        //display edit link for logged in users with edit posts capabilities
        $show_edit_link         = ( is_user_logged_in() && current_user_can('upload_files') ) ? true : false;
        $show_edit_link         = apply_filters('tc_show_slider_edit_link' , $show_edit_link && !is_null($data['link_id']) );
        if ( $show_edit_link ) {
          printf('<span class="slider edit-link btn btn-inverse"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
            get_edit_post_link($id) . '#slider_sectionid',
            __( 'Edit' , 'customizr' )
          );
        }//end edit attachment condition
      ?>

    </div><!-- /.item -->
    <?php
  }



  /*
  * Slider controls view
  * @param slides
  * @hook : __after_carousel_inner
  * @since v3.2.0
  *
  */
  function tc_slider_control_view( $_slides ) {
    if ( count( $_slides ) <= 1 )
      return;

    if ( ! apply_filters('tc_show_slider_controls' , ! wp_is_mobile() ) )
      return;

    $_html = '';
    $_html .= sprintf('<div class="tc-slider-controls %1$s">%2$s</div>',
      ! is_rtl() ? 'left' : 'right',
      sprintf('<a class="tc-carousel-control" href="#customizr-slider" data-slide="%1$s">%2$s</a>',
        ! is_rtl() ? 'prev' : 'next',
        apply_filters( 'tc_slide_left_control', '&lsaquo;' )
      )
    );
    $_html .= sprintf('<div class="tc-slider-controls %1$s">%2$s</div>',
      ! is_rtl() ? 'right' : 'left',
      sprintf('<a class="tc-carousel-control" href="#customizr-slider" data-slide="%1$s">%2$s</a>',
        ! is_rtl() ? 'next' : 'prev',
        apply_filters( 'tc_slide_right_control', '&rsaquo;' )
      )
    );
    echo apply_filters( 'tc_slider_control_view', $_html );
  }








  /******************************
  HELPERS / SETTERS / CALLBACKS
  *******************************/
  /**
  * Helper
  * @return  boolean
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  private function tc_is_slider_possible() {
    //gets the front slider if any
    $tc_front_slider              = esc_attr(TC_utils::$inst->tc_opt( 'tc_front_slider' ) );
    //when do we display a slider? By default only for home (if a slider is defined), pages and posts (including custom post types)
    $_show_slider = tc__f('__is_home') ? ! empty( $tc_front_slider ) : ! is_404() && ! is_archive() && ! is_search();

    return apply_filters( 'tc_show_slider' , $_show_slider );
  }



  /**
  * helper
  * returns the slider name id
  * @return  string
  *
  */
  private function tc_get_current_slider($queried_id) {
    //gets the current slider id
    $_home_slider     = TC_utils::$inst->tc_opt( 'tc_front_slider' );
    $slider_name_id   = ( tc__f('__is_home') && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
    return apply_filters( 'tc_slider_name_id', $slider_name_id , $queried_id);
  }


  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  number
  *
  */
  private function tc_get_real_id() {
    global $wp_query;
    $queried_id                   = get_queried_object_id();
    return apply_filters( 'tc_slider_get_real_id', ( ! tc__f('__is_home') && $wp_query -> is_posts_page && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
  }


  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  boolean
  *
  */
  private function tc_is_slider_active( $queried_id ) {
    //is the slider set to on for the queried id?
    if ( tc__f('__is_home') && TC_utils::$inst->tc_opt( 'tc_front_slider' ) )
      return apply_filters( 'tc_slider_active_status', true , $queried_id );

    $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );
    if ( ! empty( $_slider_on ) && $_slider_on )
      return apply_filters( 'tc_slider_active_status', true , $queried_id );

    return apply_filters( 'tc_slider_active_status', false , $queried_id );
  }



  /**
  * hook : tc_slider_height, fired in tc_user_options_style
  * @return number height value
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function tc_set_demo_slider_height( $_h ) {
    //this custom demo height is applied when :
    //1) current slider is demo
    if ( 'demo' != $this -> tc_get_current_slider( $this -> tc_get_real_id() ) )
      return $_h;

    //2) height option has not been changed by user yet
    //the possible customization context must be taken into account here
    if ( TC___::$instance -> tc_is_customizing() ) {
      if ( 500 != esc_attr( TC_utils::$inst->tc_opt( 'tc_slider_default_height') ) )
        return $_h;
    } else {
      if ( false !== (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_slider_default_height', TC___::$tc_option_group, $use_default = false ) ) )
        return $_h;
    }

    return apply_filters( 'tc_set_demo_slider_height' , 750 );
  }


  /**
  * Callback of tc_user_options_style hook
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function tc_write_slider_inline_css( $_css ) {
    // 1) Do we have a custom height ?
    // 2) check if the setting must be applied to all context
    $_custom_height     = apply_filters( 'tc_slider_height' , esc_attr( TC_utils::$inst->tc_opt( 'tc_slider_default_height') ) );
    $_slider_inline_css = "";

    //When shall we append custom slider style to the global custom inline stylesheet?
    $_bool = 500 != $_custom_height;
    $_bool = $_bool && ( tc__f('__is_home') || 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_slider_default_height_apply_all') ) );

    if ( ! apply_filters( 'tc_print_slider_inline_css' , $_bool ) )
      return $_css;

    $_resp_shrink_ratios = apply_filters( 'tc_slider_resp_shrink_ratios',
      array('1200' => 0.77 , '979' => 0.618, '480' => 0.38 , '320' => 0.28 )
    );

    $_slider_inline_css = "
      .carousel .item {
        line-height: {$_custom_height}px;
        min-height:{$_custom_height}px;
        max-height:{$_custom_height}px;
      }
      .tc-slider-loader-wrapper {
        line-height: {$_custom_height}px;
        height:{$_custom_height}px;
      }
      .carousel .tc-slider-controls {
        line-height: {$_custom_height}px;
        max-height:{$_custom_height}px;
      }\n";

    foreach ( $_resp_shrink_ratios as $_w => $_ratio) {
      if ( ! is_numeric($_ratio) )
        continue;
      $_item_dyn_height     = $_custom_height * $_ratio;
      $_caption_dyn_height  = $_custom_height * ( $_ratio - 0.1 );
      $_slider_inline_css .= "
        @media (max-width: {$_w}px) {
          .carousel .item {
            line-height: {$_item_dyn_height}px;
            max-height:{$_item_dyn_height}px;
            min-height:{$_item_dyn_height}px;
          }
          .item .carousel-caption {
            max-height: {$_caption_dyn_height}px;
            overflow: hidden;
          }
          .carousel .tc-slider-loader-wrapper {
            line-height: {$_item_dyn_height}px;
            height:{$_item_dyn_height}px;
          }
        }\n";
    }//end foreach

    return sprintf("%s\n%s", $_css, $_slider_inline_css);
  }



  /**
  * Set slider wrapper class
  * hook : tc_slider_layout_class filter
  *
  * @package Customizr
  * @since Customizr 3.2.0
  *
  */
  function tc_set_slider_wrapper_class($_classes) {
    if ( ! is_array($_classes) || 500 == esc_attr( TC_utils::$inst->tc_opt( 'tc_slider_default_height') ) )
      return $_classes;

    return array_merge( $_classes , array('custom-slider-height') );
  }


  /**
  * hook : tc_carousel_inner_classes fired in the slider view
  * @return  array of css classes
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function tc_set_inner_class( $_classes ) {
    if( ! (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_center_slider_img') ) || ! is_array($_classes) )
      return $_classes;
    array_push( $_classes, 'center-slides-enabled' );
    return $_classes;
  }


} //end of class
endif;
