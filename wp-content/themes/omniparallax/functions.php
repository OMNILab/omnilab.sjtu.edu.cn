<?php
/**
 * accesspress_parallax_omnilab functions and definitions
 *
 * @package omniparallax
 */

/**
 * Enqueue the parent and child theme stylesheets.
 */
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

/**
 * Unhook original bxSlider Callback
 */
// add_action('init','unhook_accesspress_bxslidercb');
function unhook_accesspress_bxslidercb() {
    remove_action('accesspress_bxslider','accesspress_bxslidercb', 10);
}

/**
 * Shorten a multibyte string by specific length.
 */
function shortenText($text, $maxlength = 70, $appendix = "...")
{
    if (mb_strlen($text) <= $maxlength) {
        return $text;
    }
    $text = mb_substr($text, 0, $maxlength - mb_strlen($appendix));
    $text .= $appendix;
    return $text;
}

/**
 * Hook a new bxSlider callback
 */
// add_action('accesspress_bxslider','my_accesspress_bxslidercb', 10);
function my_accesspress_bxslidercb(){
    global $post;
    $accesspress_parallax = of_get_option('parallax_section');
    if(!empty($accesspress_parallax)) :
        $accesspress_parallax_first_page_array = array_slice($accesspress_parallax, 0, 1);
        $accesspress_parallax_first_page = sanitize_title(get_the_title($accesspress_parallax_first_page_array[0]['page']));
    endif;
    $accesspress_slider_category = of_get_option('slider_category');
    $accesspress_slider_full_window = of_get_option('slider_full_window') ;
    $accesspress_show_slider = of_get_option('show_slider') ;
    $accesspress_show_pager = (!of_get_option('show_pager') || of_get_option('show_pager') == "yes") ? "true" : "false";
    $accesspress_show_controls = (!of_get_option('show_controls') || of_get_option('show_controls') == "yes") ? "true" : "false";
    $accesspress_auto_transition = (!of_get_option('auto_transition') || of_get_option('auto_transition') == "yes") ? "true" : "false";
    $accesspress_slider_transition = (!of_get_option('slider_transition')) ? "fade" : of_get_option('slider_transition');
    $accesspress_slider_speed = (!of_get_option('slider_speed')) ? "5000" : of_get_option('slider_speed');
    $accesspress_slider_pause = (!of_get_option('slider_pause')) ? "5000" : of_get_option('slider_pause');
    $accesspress_show_caption = of_get_option('show_caption') ;
    $accesspress_enable_parallax = of_get_option('enable_parallax');
    ?>

    <?php if( $accesspress_show_slider == "yes" || empty($accesspress_show_slider)) : ?>
        <section id="main-slider" class="full-screen-<?php echo $accesspress_slider_full_window; ?>">

            <div class="overlay"></div>

            <?php if(!empty($accesspress_parallax_first_page)): ?>
                <div class="next-page"><a href="#<?php echo $accesspress_parallax_first_page; ?>"></a></div>
            <?php endif; ?>

            <script type="text/javascript">
                jQuery(function($){
                    $('#main-slider .bx-slider').bxSlider({
                        adaptiveHeight: true,
                        pager: <?php echo $accesspress_show_pager; ?>,
                        controls: <?php echo $accesspress_show_controls; ?>,
                        mode: '<?php echo $accesspress_slider_transition; ?>',
                        auto : '<?php echo $accesspress_auto_transition; ?>',
                        pause: '<?php echo $accesspress_slider_pause; ?>',
                        speed: '<?php echo $accesspress_slider_speed; ?>'
                    });

                    <?php if($accesspress_slider_full_window == "yes" && !empty($accesspress_slider_category)) : ?>
                    $(window).resize(function(){
                        var winHeight = $(window).height();
                        var headerHeight = $('#masthead').outerHeight();
                        $('#main-slider .bx-viewport , #main-slider .slides').height(winHeight-headerHeight);
                    }).resize();
                    <?php endif; ?>

                });
            </script>
            <?php
            if( !empty($accesspress_slider_category)) :

                $loop = new WP_Query(array(
                    'cat' => $accesspress_slider_category,
                    'posts_per_page' => -1
                ));
                if($loop->have_posts()) : ?>

                    <div class="bx-slider">
                        <?php
                        while($loop->have_posts()) : $loop-> the_post();
                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full', false );
                            $image_url = "";
                            if($accesspress_slider_full_window == "yes") :
                                $image_url =  "style = 'background-image:url(".$image[0].");'";
                            endif;
                            ?>
                            <div class="slides" <?php echo $image_url; ?>>

                                <?php if($accesspress_slider_full_window == "no") : ?>
                                    <img src="<?php echo $image[0]; ?>">
                                <?php endif; ?>

                                <?php if($accesspress_show_caption == 'yes'): ?>
                                    <div class="slider-caption">
                                        <div class="mid-content">
                                            <h1 class="caption-title"><?php the_title();?></h1>
                                            <h2 class="caption-description"><?php the_content();?></h2>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <div class="bx-slider">
                    <div class="slides">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/omnilab/slider1.jpg" alt="slider1">
                        <div class="slider-caption">
                            <div class="mid-content">
                                <h1 class="caption-title">Welcome to OMNILab!</h1>
                                <h2 class="caption-description">
                                    <p>A place for innovation, creation and hacking with Open Data!</p>
                                    <p><a href="researches">Read More</a></p>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="slides">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/omnilab/slider2.jpg" alt="slider2">
                        <div class="slider-caption">
                            <div class="ak-container">
                                <h1 class="caption-title">Amazing data mining hand-ons!</h1>
                                <h2 class="caption-description">
                                    <p>Practical data analysis techniques, tutorials, courses, and competetions – useful for anyone and everyone</p>
                                    <p><a href="projects">Read More</a></p>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            <?php  endif; ?>
        </section>
    <?php endif; ?>
<?php
}