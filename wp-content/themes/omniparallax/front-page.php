<?php
/**
 * The main landing page of our site.
 *
 * @package omniparallax
 */
get_header(); ?>

<?php
$sections = of_get_option('parallax_section');

echo $sections;

if (!empty($sections)):
    foreach ($sections as $section) :
        $page = get_post($section['page']);
        $overlay = $section['overlay'];
        $image = $section['image'];
        $layout = $section['layout'];
        $category = $section['category'];
        $googlemapclass = $layout == "googlemap_template" ? " google-map" : "";
        ?>

        <?php if (!empty($section['page'])): ?>
        <section class="parallax-section clearfix<?php echo $googlemapclass . " " . $layout; ?>"
                 id="<?php echo "section-" . $page->ID; ?>">
            <?php if (!empty($image) && $overlay != "overlay0") : ?>
                <div class="overlay"></div>
            <?php endif; ?>

            <?php if ($layout != "googlemap_template") : ?>
            <div class="mid-content">
                <?php endif; ?>

                <?php
                if ($layout != "action_template" && $layout != "blank_template" && $layout != "googlemap_template"): ?>
                    <h1><span><?php echo $page->post_title; ?></span></h1>

                    <div class="parallax-content">
                        <?php if ($page->post_content != "") : ?>
                            <div class="page-content">
                                <?php echo wpautop(do_shortcode($page->post_content)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php

                switch ($layout) {
                    case 'default_template':
                        $template = "layouts/default";
                        break;

                    case 'service_template':
                        $template = "layouts/service";
                        break;

                    case 'team_template':
                        $template = "layouts/team";
                        break;

                    case 'portfolio_template':
                        $template = "layouts/portfolio";
                        break;

                    case 'testimonial_template':
                        $template = "layouts/testimonial";
                        break;

                    case 'action_template':
                        $template = "layouts/action";
                        break;

                    case 'blank_template':
                        $template = "layouts/blank";
                        break;

                    case 'googlemap_template':
                        $template = "layouts/googlemap";
                        break;

                    case 'blog_template':
                        $template = "layouts/blog";
                        break;

                    default:
                        $template = "layouts/default";
                        break;
                } ?>

                <?php include($template . "-section.php"); ?>

                <?php if ($layout != "googlemap_template") : ?>
            </div>
        <?php endif; ?>
        </section>
    <?php endif; ?>
    <?php endforeach; ?>
<?php else:
    get_template_part("sections/introduction");
    get_template_part("sections/topics");
    get_template_part("sections/team");
    get_template_part("sections/blank");
    get_template_part("sections/blog");
    get_template_part("sections/feed");
    get_template_part("sections/contact");
endif; ?>
</div>

<?php get_footer(); ?>

<style type='text/css' media='all'>#features{ background:url() no-repeat scroll top left #f6f6f6; background-size:cover; color:#333333}
    #features .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay0.png);}
    #portfolio{ background:url(<?php echo get_template_directory_uri(); ?>/images/demo/bg1.jpg) no-repeat fixed bottom center #e3633b; background-size:auto; color:#ffffff}
    #portfolio .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay3.png);}
    #team{ background:url() no-repeat scroll top left #f6f6f6; background-size:cover; color:#333333}
    #team .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay0.png);}
    #page-1{ background:url(<?php echo get_template_directory_uri(); ?>/images/demo/bg2.jpg) no-repeat fixed top center #1e73be; background-size:auto; color:}
    #page-1 .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay0.png);}
    #testimonials{ background:url() no-repeat fixed top left #f6f6f6; background-size:cover; color:#333333}
    #testimonials .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay0.png);}
    #blog{ background:url(<?php echo get_template_directory_uri(); ?>/images/demo/bg3.jpg) no-repeat fixed center center ; background-size:cover; color:#ffffff}
    #blog .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay3.png);}
    #for-news-and-updates-subscribe-us{ background:url() no-repeat fixed top left #F6F6F6; background-size:cover; color:#333}
    #contact{ background:url(<?php echo get_template_directory_uri(); ?>/images/demo/bg4.jpg) no-repeat scroll top left ; background-size:cover; color:#FFF}
    #contact .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay3.png);}
    #google-map{ background:url() no-repeat scroll top left ; background-size:auto; color:}
    #google-map .overlay { background:url(<?php echo get_template_directory_uri(); ?>/images/overlay0.png);}
    #content{margin:0 !important}
</style>