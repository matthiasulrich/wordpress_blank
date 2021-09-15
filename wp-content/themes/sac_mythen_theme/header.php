<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width initial-scale=1.0">
<meta name="description" content="Beschreibung hier">

<!-- Global site tag (gtag.js) - Google Analytics -->
<!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78418230-17"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-78418230-17');
</script>
-->

<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/favicon-16x16.png">
<link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/safari-pinned-tab.svg" color="#000000">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">


<script>
var isIE = /*@cc_on!@*/false || !!document.documentMode;
if(isIE){
	window.MSInputMethodContext && document.documentMode && document.write('<script src="https://cdn.jsdelivr.net/gh/nuxodin/ie11CustomProperties@4.1.0/ie11CustomProperties.min.js"><\/script>');	
}
</script>
<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>

	<?php set_to_draft(); ?>
	<div id="page_wrapper" class="hfeed">
		<header id="header">
			
			<nav id="menu" class="out">
				<div class="main_menu_container">
					<?php wp_nav_menu( array( 'theme_location' => 'main-menu' ) ); ?>
				</div>			
				<button class="hamburger hamburger--collapse" type="button">
  					<span class="hamburger-box">
    					<span class="hamburger-inner"></span>
  					</span>
				</button>
				<!--<div id="search"><?php get_search_form(); ?></div>-->
			</nav>
			
			<?php 
			if (function_exists('nav_breadcrumb')): ?>
				<div class="breadcrumb_container"><?php nav_breadcrumb(); ?></div>
			<?php endif;?>
			<div id="branding">
				<?php
				if(get_field('logo','options')):
					$logo = get_field('logo', 'options');
					$logo_id = $logo['ID']; ?>					
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home">
						<?php echo wp_get_attachment_image( $logo_id, 'thumbnail', false, array('class' => 'logo', 'alt' => 'logo', 'id' => 'logo') ); ?>
					</a>
				<?php endif; ?>
			</div>

		</header>
		<div id="content_container">