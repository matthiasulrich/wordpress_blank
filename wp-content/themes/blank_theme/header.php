<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php ud_schema_type(); ?>>
<head>
	
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width" />
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

<script>
var isIE = /*@cc_on!@*/false || !!document.documentMode;
if(isIE){
	window.MSInputMethodContext && document.documentMode && document.write('<script src="https://cdn.jsdelivr.net/gh/nuxodin/ie11CustomProperties@4.1.0/ie11CustomProperties.min.js"><\/script>');	
}
</script>	
	
	
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/favicon-16x16.png">
<link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon/safari-pinned-tab.svg" color="#000000">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
	
<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>

	<div id="page_wrapper" class="hfeed">
		<header id="header">
			<div id="branding">
				<div id="site-title">
					<?php if ( is_front_page() || is_home() || is_front_page() && is_home() ) { echo '<h1>'; } ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
						<?php if ( is_front_page() || is_home() || is_front_page() && is_home() ) { echo '</h1>'; } ?>
				</div>
				<div id="site-description"><?php bloginfo( 'description' ); ?></div>
			</div>
			<nav id="menu">
				<div id="search"><?php get_search_form(); ?></div>
				<?php wp_nav_menu( array( 'theme_location' => 'main-menu' ) ); ?>
			</nav>
		</header>
		<div id="content_container">
<?php 
$test_1 = get_stylesheet_directory_uri() . "/style.css";
$test_2 = get_stylesheet_directory( __DIR__ . '/style.css' );

echo "<br />";
var_dump("test_1: " . $test_1);
echo "<br />";
var_dump("test_2: " . $test_2);


?>