<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package stronghold
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'stronghold' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="top-nav">
			<div class="container">
			<?php if( is_active_sidebar( 'top-left' ) ) : ?>
				<div class="cart eight columns">
					<?php dynamic_sidebar('top-left' ); ?>
				</div>
			<?php endif; ?>
			<?php if( is_active_sidebar('top-right' ) ) : ?>
				<div class="eight columns social">
					<?php dynamic_sidebar('top-right' ); ?>
				</div>
			<?php endif; ?>
			</div>
		</div> <!-- .top-nav -->
		<div class="branding">
			<div class="container">
				<div class="site-branding six columns">
				<?php 
					$logo_title = get_theme_mod( 'logo_title' );
					$logo = get_theme_mod( 'logo', '' );
					$tagline = get_theme_mod( 'tagline');
					if( $logo_title && $logo != '' ) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url($logo) ?>"></a></h1>
				<?php else : ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php endif; ?>
				<?php if( $tagline ) : ?>
						<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
				<?php endif; ?>
				</div><!-- .site-branding -->

					<nav id="site-navigation" class="main-navigation ten columns" role="navigation">
						<button class="menu-toggle" aria-controls="menu" aria-expanded="false"><?php _e( 'Primary Menu', 'stronghold' ); ?></button>
						<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
					</nav><!-- #site-navigation -->
				</div>
			</div><!-- .branding -->

	</header><!-- #masthead -->
	
<?php if( ! is_front_page() ) : ?>
	<div id="content" class="site-content">
		<div class="container">
<?php endif; ?>
