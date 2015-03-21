<?php
$post_date = of_get_option('post_date');
$post_footer = of_get_option('post_footer');
$post_date_class = ($post_date != 1 || has_post_thumbnail()) ? " no-date" : "";
?>

<a href="<?php get_permalink(); ?>" class="blog-list wow fadeInDown" data-wow-delay="0.25s">
    <div class="blog-image">
        <?php if( has_post_thumbnail() ) : ?>
            <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'blog-header' )[0]; ?>
            <?php else: ?>
            <?php $image = get_template_directory_uri() . "/images/demo/portfolio1.jpg"; ?>
        <?php endif; ?>
        <img src="<?php echo $image ?>" alt="Featured blog1">
    </div>

    <div class="blog-excerpt">
        <h3><?php _e( the_title(), 'accesspress_parallax'); ?></h3>
        <h4 class="posted-date"><i class="fa fa-calendar"></i><?php echo $post_date; ?></h4>
        <?php the_excerpt() ?><br />
        <span><?php 'Read More'; ?>&nbsp;&nbsp;<i class="fa fa-angle-right"></i></span>
    </div>
</a>