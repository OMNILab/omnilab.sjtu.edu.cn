<section class="parallax-section clearfix blog_template" id="blog">
    <div class="overlay"></div>
    <div class="mid-content">
        <h1><span>Blog</span></h1>
        <div class="parallax-content">
            <div class="page-content">
                <p style="text-align: center;">Read our latest blogs</p>
            </div>
        </div>
        <div class="blog-listing clearfix">
            <?php $the_query = new WP_Query( 'showposts=3' ); ?>
            <?php $i = 0; ?>
            <?php while ($the_query -> have_posts()) : $the_query -> the_post(); $i += 1; ?>

                <a href="<?php the_permalink() ?>" class="blog-list wow fadeInDown" data-wow-delay="0.25s">
                    <div class="blog-image">
                        <img src="<?php echo get_template_directory_uri()?><?php echo "/images/demo/portfolio" . $i . ".jpg" ?>" alt="See picture">
                    </div>
                    <div class="blog-excerpt">
                        <h3><?php echo shortenText(the_title(), 10); ?></h3>
                        <h4 class="posted-date"><i class="fa fa-calendar"></i><?php the_date() ?></h4>
                        <?php
                        $blog_length_forced = 8 * 9;
                        $blog_display = $post->post_content;

                        // Aligning blogs with different lengths
                        if (mb_strlen($blog_display) < $blog_length_forced) {
                            $blog_display = str_pad($blog_display, $blog_length_forced, " ");
                        }
                        echo shortenText(strip_tags($blog_display), $blog_length_forced); ?>

                        <span>Read More&nbsp;&nbsp;<i class="fa fa-angle-right"></i></span>
                    </div>
                </a>
            <?php endwhile;?>
        </div>
        <div class="clearfix btn-wrap">
            <a class="btn" href="blogs"><?php _e( 'Read All', 'accesspress_parallax' ); ?></a>
        </div>
    </div>
</section>