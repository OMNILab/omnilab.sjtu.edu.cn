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
 * Hook a new bxSlider callback
 */

// remove_action('accesspress_bxslider','accesspress_bxslidercb', 1);
// 此函数在原主题中没有add accesspress-parallax/inc/accesspress-functions.php
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
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/omnilab/slider-networks.png" alt="slider1">
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
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/omnilab/slider-discuss.jpg" alt="slider2">
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

add_action('accesspress_bxslider','my_accesspress_bxslidercb', 10);



/**
 * Get team members from WooThemes My Team plugin
 */
function get_team_members() {
    $filter = array(
        'limit' 					=> 10,
        'per_row' 					=> null,
        'orderby' 					=> 'menu_order',
        'order' 					=> 'DESC',
        'id' 						=> 0,
        'slug'						=> null,
        'display_author' 			=> true,
        'display_additional' 		=> true,
        'display_avatar' 			=> true,
        'display_url' 				=> true,
        'display_twitter' 			=> true,
        'display_author_archive'	=> true,
        'display_role'	 			=> true,
        'contact_email'				=> true,
        'tel'						=> true,
        'effect' 					=> 'fade', // Options: 'fade', 'none'
        'pagination' 				=> false,
        'echo' 						=> false,
        'size' 						=> 256, // default image size
        'title' 					=> '',
        'category' 					=> 0
    );

    return woothemes_get_our_team($filter);
}

/**
 * Shorten a multibyte string by specific length.
 */
function shorten_text($text, $maxlength = 70, $appendix = "...")
{
    if (mb_strlen($text) <= $maxlength) {
        return $text;
    }
    $text = mb_substr($text, 0, $maxlength - mb_strlen($appendix));
    $text .= $appendix;
    return $text;
}

/**
 * Extract the first $n sentences from a paragraph.
 */
function extract_sentences($text, $n = 1) {
    $pattern_ch = "/([^\x{3002}\x{FF1F}\x{FF01}]*[\x{3002}\x{FF1F}\x{FF01}]+){" . $n . "}/uU";
    $pattern_en  = "/^([^?!]*[\.!?]+\s){" . $n . "}/U";
    if ( preg_match($pattern_en, strip_tags($text), $abstract) ) {
        $res = $abstract[0];
    } else if (preg_match($pattern_ch, strip_tags($text), $abstract)){
        $res = $abstract[0];
    } else {
        $res = $text;
    }
    return $res;
}

/**
 * 截取UTF8编码字符串从首字节开始指定宽度(非长度), 适用于字符串长度有限的如新闻标题的等宽度截取
 * 中英文混排情况较理想. 全中文与全英文截取后对比显示宽度差异最大,且截取宽度远大越明显.
 * @param string $str   UTF-8 encoding
 * @param int[option] $width 截取宽度
 * @param string[option] $end 被截取后追加的尾字符
 * @param float[option] $x3<p>
 *  3字节（中文）字符相当于希腊字母宽度的系数coefficient（小数）
 *  中文通常固定用宋体,根据ascii字符字体宽度设定,不同浏览器可能会有不同显示效果</p>
 *
 * @return string
 * @author waiting
 * Thanks to http://waiting.iteye.com
 */
function u8_substr_equal_width($str, $width = 0, $end = '...', $x3 = 0) {
    global $CFG; // 全局变量保存 x3 的值
    if ($width <= 0 || $width >= strlen($str)) {
        return $str;
    }
    $arr = str_split($str);
    $len = count($arr);
    $w = 0;
    $width *= 10;

    // 不同字节编码字符宽度系数
    $x1 = 11;   // ASCII
    $x2 = 16;
    $x3 = $x3===0 ? ( $CFG['cf3']  > 0 ? $CFG['cf3']*10 : $x3 = 21 ) : $x3*10;
    $x4 = $x3;

    // http://zh.wikipedia.org/zh-cn/UTF8
    for ($i = 0; $i < $len; $i++) {
        if ($w >= $width) {
            $e = $end;
            break;
        }
        $c = ord($arr[$i]);
        if ($c <= 127) {
            $w += $x1;
        }
        elseif ($c >= 192 && $c <= 223) { // 2字节头
            $w += $x2;
            $i += 1;
        }
        elseif ($c >= 224 && $c <= 239) { // 3字节头
            $w += $x3;
            $i += 2;
        }
        elseif ($c >= 240 && $c <= 247) { // 4字节头
            $w += $x4;
            $i += 3;
        }
    }

    return implode('', array_slice($arr, 0, $i) ). $e;
}