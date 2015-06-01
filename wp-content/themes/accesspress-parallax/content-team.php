<?php
/**
 * @package accesspress_parallax
 */
?>
<?php 
$post_date = of_get_option('post_date');
$post_footer = of_get_option('post_footer');
$post_date_class = ($post_date != 1 || has_post_thumbnail()) ? " no-date" : "";
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if(has_post_thumbnail()) : ?>
	<div class="entry-thumb">
		<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'medium' ); ?>
		<a href="<?php echo home_url()?>/team"><div class="overlay"></div><img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(); ?>"></a>
	</div>
	<?php endif; ?>

	<header class="entry-header">
		<h1 class="entry-title<?php echo $post_date_class; ?>"><a href="<?php echo home_url()?>/team"><?php the_title(); ?></a></h1>
		<h5><?php echo get_post_meta(get_the_ID(), "wpcf-degree", true);?></h5>	
	</header><!-- .entry-header -->

	<div class="entry-content">
		<!-- <div class="personal-basic">
			<span class="entry-date"><?php echo get_post_meta(get_the_ID(), "wpcf-entry-date", true);?></span>
			<span class="gender"><?php echo get_post_meta(get_the_ID(), "wpcf-gender", true);?></span>
		</div> -->
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'accesspress_parallax' ) ); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'accesspress_parallax' ),
				'after'  => '</div>',
			) );
		?>
		<div class="excerpt"><p><?php echo the_excerpt();?></p></div>
		<div class="personal-extra">
			<p><i class="fa fa-home fa-lg"></i>&nbsp;&nbsp;&nbsp;<a class="Website" href="<?php echo get_post_meta(get_the_ID(), 'wpcf-personal-homepage', true);?>"><?php echo get_post_meta(get_the_ID(), 'wpcf-personal-homepage', true);?></a></p>
			<p><i class="fa fa-envelope fa-lg"></i>&nbsp;&nbsp;&nbsp;<a class="Email" href="mailto:<?php echo get_post_meta(get_the_ID(), 'wpcf-e-mail', true);?>"><?php echo get_post_meta(get_the_ID(), 'wpcf-e-mail', true);?></a></p>

			<a title="Website" href="<?php echo get_post_meta(get_the_ID(), 'wpcf-personal-homepage', true);?>"><i class="fa fa-home fa-lg"></i></a>
			<a title="Github" href="https://github.com/<?php echo get_post_meta(get_the_ID(), 'wpcf-github', true);?>"><i class="fa fa-github fa-lg"></i></a>
			<a title="Twitter" href="https://twitter.com/<?php echo get_post_meta(get_the_ID(), 'wpcf-twitter', true);?>"><i class="fa fa-twitter fa-lg"></i></a>
			<a title="Wechat" href="https://wx.qq.com/#<?php echo get_post_meta(get_the_ID(), 'wpcf-wechat', true);?>"><i class="fa fa-weixin fa-lg"></i></a>
			<a title="Weibo" href="http://weibo.com/<?php echo get_post_meta(get_the_ID(), 'wpcf-weibo', true);?>"><i class="fa fa-weibo fa-lg"></i></a>
			<a title="Google+" href="http://plus.google.com/<?php echo get_post_meta(get_the_ID(), 'wpcf-google', true);?>"><i class="fa fa-google-plus fa-lg"></i></a>
			<a title="Linkedin" href="http://www.linkedin.com/in/<?php echo get_post_meta(get_the_ID(), 'wpcf-linkedin', true);?>"><i class="fa fa-linkedin-square fa-lg"></i></a>
			<a title="Email" href="mailto:<?php echo get_post_meta(get_the_ID(), 'wpcf-e-mail', true);?>"><i class="fa fa-paper-plane-o fa-lg"></i></a>

			<!-- <span class="tele"><?php echo get_post_meta(get_the_ID(), "wpcf-telephone-number", true);?></span> -->
			<!-- <span class="gravatar"><?php echo get_post_meta(get_the_ID(), "wpcf-gravatar", true);?></span> -->

		</div>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link"><?php comments_popup_link( __( '<i class="fa fa-comments"></i>Leave a comment', 'accesspress_parallax' ), __( '<i class="fa fa-comments"></i>1 Comment', 'accesspress_parallax' ), __( '<i class="fa fa-comments"></i>% Comments', 'accesspress_parallax' ) ); ?></span>
		<?php endif; ?>
	</footer><!-- .entry-footer -->
	<?php edit_post_link( __( '<i class="fa fa-pencil-square-o"></i>Edit', 'accesspress_parallax' ), '<span class="edit-link">', '</span>' ); ?>
</article><!-- #post-## -->