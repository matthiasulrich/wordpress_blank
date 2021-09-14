<?php
/*
*  Author: Matthias Ulrich
*  URL: https://ulrich.digital
*/

setlocale(LC_TIME, "de_DE.utf8");

/* ============================================ *\
    header.php aufraeumen
\* ============================================ */
remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'index_rel_link' ); // index link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action( 'wp_head', 'rest_output_link_wp_head');
remove_action( 'wp_head', 'wp_oembed_add_discovery_links');
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
/// emojis weg
add_action('init', 'remove_emoji');
function remove_emoji(){
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'remove_tinymce_emoji');
}

function remove_tinymce_emoji($plugins){if (!is_array($plugins)){return array();}return array_diff($plugins, array('wpemoji'));}

// jquery migrate weg
add_action( 'wp_default_scripts', 'cedaro_dequeue_jquery_migrate' );
function cedaro_dequeue_jquery_migrate( $scripts ) {
	if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {$jquery_dependencies = $scripts->registered['jquery']->deps;$scripts->registered['jquery']->deps = array_diff( $jquery_dependencies, array( 'jquery-migrate' ) );}
}

/* =============================================================== *\ 
 	 Admin CSS + Javascripts
\* =============================================================== */ 
function add_admin_sripts() {
	wp_enqueue_media();
	wp_register_script('my-admin-js', get_stylesheet_directory_uri() . '/js/ulrich_admin.js', array('jquery'));
	wp_enqueue_script('my-admin-js');
}
add_action('admin_enqueue_scripts', 'add_admin_sripts');

function add_admin_styles() {	
	wp_enqueue_style('admin-styles', get_template_directory_uri().'/style-admin.css');
}
add_action('admin_enqueue_scripts', 'add_admin_styles');  


/* ============================================ *\
    CSS
\* ============================================ */
function theme_styles() {
    //wp_register_style( $handle, $src, $deps, $ver, $media );

    wp_register_style('main',  get_stylesheet_directory_uri() . "/style.css?" . date("h:i:s"), array(), '1.0', 'all');
    wp_enqueue_style('main');

	wp_register_style('font_awesome', get_stylesheet_directory_uri() . '/css/all.css', array(), '1.0', 'all');
    wp_enqueue_style('font_awesome');

	/*
	wp_register_style('adobe_fonts', 'https://use.typekit.net/owr0crc.css', array(), '1.0', 'all');
	wp_enqueue_style('adobe_fonts');

	wp_register_style('slick',  get_stylesheet_directory_uri() . '/js/slick.css', array(), '1.0', 'all');
	wp_enqueue_style('slick');
	
	wp_register_style('slick-lightbox', get_stylesheet_directory_uri() . '/js/slick-lightbox.css', array(), '1.0', 'all');
	wp_enqueue_style('slick-lightbox');

    wp_register_style('google_fonts', 'https://fonts.googleapis.com/css?family=Abel|Roboto:300', array(), '1.0', 'all');
    wp_enqueue_style('google_fonts');
    */
}
add_action('wp_enqueue_scripts', 'theme_styles');


/* ============================================ *\
    Javascripts
\* ============================================ */
function fuege_javascripts_ein() {
	//$url_h1 = get_stylesheet_directory_uri().'/js/slick.min.js';
	//$url_h2 = get_stylesheet_directory_uri().'/js/slick-lightbox.js';
	$url_h0 = get_stylesheet_directory_uri().'/js/jquery-ui.min.js';
	$url_h3 = get_stylesheet_directory_uri().'/js/ulrich.js?v='. time() . '';
    //wp_enqueue_script( 'eigener_Name', pfad_zum_js, abhaengigkeit (zb jquery zuerst laden), versionsnummer, bool (true=erst im footer laden) );
	//wp_enqueue_script( 'jquery' );
	//wp_enqueue_script( 'handler_name_1', $url_h1, array('jquery'), null, false );
	//wp_enqueue_script( 'handler_name_2', $url_h2, array('jquery'), null, false );
	wp_enqueue_script( 'handler_name_0', $url_h0, array('jquery'), null, false );
	wp_enqueue_script( 'handler_name_3', $url_h3, array('jquery'), null, false );
}
add_action( 'wp_enqueue_scripts', 'fuege_javascripts_ein' );


add_action( 'comment_form_before', 'enqueue_comment_reply_script' );
function enqueue_comment_reply_script() {
	if ( get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/* ============================================ *\
	Theme Support
\* ============================================ */
// add the admin options page

include('theme_options.php');

// Remove unnecessary type attripute from javascript files
add_action('after_setup_theme', function() { add_theme_support( 'html5', [ 'script', 'style' ] );});

/* Load More Button in Media-Library ausschalten*/
add_filter( 'media_library_infinite_scrolling', '__return_true' );




/*Title*/
add_filter( 'document_title_separator', 'document_title_separator' );
function document_title_separator( $sep ) {
    $sep = '|';
    return $sep;
}

add_filter( 'the_title', 'mytitle' );
function mytitle( $title ) {
    if ( $title == '' ) {
        return '...';
    } else {
        return $title;
    }
}

//entfernt die automatischen <p>-Auszeichnungen
//remove_filter ('the_content', 'wpautop');

/* Footer Menü 
function ulrich_digital_setup(){
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    global $content_width;
    register_nav_menus(
	   //register_nav_menu( 'primary', __( 'Primary Menu', 'theme-slug' ) );
	   array(
		   'main-menu' => __( 'Main Menu', 'ulrich_digital_blank' ),
		   'footer_menu_1' => __( 'Footer Menu 1', 'ulrich_digital_blank' ),
		   'footer_menu_2' => __( 'Footer Menu 2', 'ulrich_digital_blank' )
	    )
    );
}
add_action( 'after_setup_theme', 'ulrich_digital_setup' );
*/

/* Widgets 
function ulrichdigital_blank_widgets_init() {
	register_sidebar( array (
		'name' => __( 'Sidebar Widget Area', 'ulrich_digital_blank' ),
		'id' => 'primary-widget-area',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'ulrichdigital_blank_widgets_init' );
*/

/* only footer widget instead */
function ulrichdigital_blank_widgets_init() {
    register_sidebar( array(
      'name'          => __( 'Footer Widgets', 'ulrich_digital_blank' ),
      'id'            => 'sidebar-2',
      'description'   => __( 'Add widgets here to appear in your footer area.', 'ulrich_digital_blank' ),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget'  => '</aside>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'ulrichdigital_blank_widgets_init' );

/*===============================================================*\
  Eigene Bildgrössen
\*===============================================================*/
function eigene_bildgroessen() {
	add_image_size('facebook_share', 1200, 630, true);
	add_image_size('startseiten_slider', 2000, 1125, true);
	add_image_size('angebot_header_bild', 2000, 2000, false);
	add_image_size('galerie_thumb', 700, 700, true);
	}

//add_action('after_setup_theme', 'eigene_bildgroessen', 11);

//Bildgroessen zur Auswahl hinzufuegen
function bildgroessen_auswaehlen($sizes) {
	$custom_sizes = array('facebook_share' => 'Facebook-Vorschaubild');
	return array_merge($sizes, $custom_sizes);
	}

//add_filter('image_size_names_choose', 'bildgroessen_auswaehlen');


/*===============================================================*\
    SVG erlauben
\*===============================================================*/
function add_svg_to_upload_mimes($upload_mimes)
	{
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
	}
add_filter('upload_mimes', 'add_svg_to_upload_mimes');


/* =============================================================== *\ 
 	 ACF 
\* =============================================================== */ 
/* ACF Options Page*/
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}


/*===============================================================*\
    Custom Admin-Logo
\*===============================================================*/

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/BG_logo_rgb.svg);
            padding-bottom: 60px;
            width:320px;
            background-repeat: no-repeat;
 			background-size: 250px auto;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


/*===============================================================*\
    Backend anpassen
\*===============================================================*/

/* Menueelemente aus dem WordPress-Dashboard entfernen */
function remove_menus () {
	global $menu;
	$restricted = array(__('Beiträge'), __('Kommentare'));
 	//$restricted = array(__('Kommentare'));
	end ($menu);
	while (prev($menu)){
     $value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
		}
 }
add_action('admin_menu', 'remove_menus');

/* Menueelemente aus dem Menue-Bar oben entfernen */
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('new-content');
	/*
	my-account – link to your account (avatars disabled)
	my-account-with-avatar – link to your account (avatars enabled)
	my-blogs – the "My Sites" menu if the user has more than one site
	get-shortlink – provides a Shortlink to that page
	edit – link to the Edit/Write-Post page
	new-content – link to the "Add New" dropdown list
	comments – link to the "Comments" dropdown
	appearance – link to the "Appearance" dropdown
	updates – the "Updates" dropdown
	*/
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

/*Benutzerdefinierte Reihenfolge des Backend-Menu*/
function wpse_custom_menu_order( $menu_ord ) {
 if ( !$menu_ord ) return true;
 return array(
     'index.php', // Dashboard
     'link-manager.php', // Links
     'edit.php?post_type=page', // Pages
     'users.php', // Users
     'upload.php', // Media
     'separator1', // First separator
     'themes.php', // Appearance
     'plugins.php', // Plugins
     'tools.php', // Tools
     'options-general.php', // Settings
     'separator2', // Second separator
     'separator-last', // Last separator
 );
}
//add_filter( 'custom_menu_order', '__return_true' );
//add_filter( 'menu_order', 'wpse_custom_menu_order', 10, 1 );

/* Entwickelt mit Herz */
function backend_entwickelt_mit_herz( $text ) {
	return ('<span style="color:black;">Entwickelt mit </span><span style="color: red;font-size:20px;vertical-align:-3px">&hearts;</span><span style="color:black;"</span><span> von <a href="https://ulrich.digital" target="_blank">ulrich.digital</a></span>' );
}
add_filter( 'admin_footer_text', 'backend_entwickelt_mit_herz' );


/* ================================================== *\ 
 	 Woocommerce 
\* ================================================== */ 
/*Woocommerce Unterstützung Backend > WooCommerce > Status deklarieren */
/*add_action( 'after_setup_theme', 'setup_woocommerce_support' ); 
function setup_woocommerce_support() { 
	add_theme_support('woocommerce'); 
	}

*/
/*===============================================================*\
	Contact Form 7
\*===============================================================*/


/*===============================================================*\
	Custom Post Types
\*===============================================================*/

/*
add_action('init','ab_register_post_type_angebot_biegen');
function ab_register_post_type_angebot_biegen(){
$supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
$labels = array(
    'name' => 'Biegen',
    'singular_name' => 'Biegen',
    'add_new' => 'Hinzuf&uuml;gen',
    'add_new_item' => 'Neuer Eintrag hinzuf&uuml;gen',
    'edit_item' => 'Eintrag bearbeiten',
    'new_item' => 'Neuer Eintrag',
    'view_item' => 'Eintrag anzeigen',
    'search_items' => 'Eintrag suchen',
    'not_found' => 'Kein Eintrag gefunden',
    'not_found_in_trash' => 'Kein Eintrag im Papierkorb',
    'menu_name' => 'Biegen'
	);
$angebot_biegen_args = array(
    'supports' => $supports,
    'labels' => $labels,
    'description' => 'Post-Type f&uuml;r Biegen',
    'public' => true,
    'show_in_nav_menus' => true,
    'show_in_menu' => true,
    'has_archive' => true,
    'query_var' => true,
	'menu_icon' => 'dashicons-hammer',
    'taxonomies' => array('topics', 'category'),
    'rewrite' => array(
        'slug' => 'angebot_biegen',
        'with_front' => true
   		),
	);
register_post_type('angebot_biegen', $angebot_biegen_args);
}

*/
// Browser-Detection
// Opera 8.0+
var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

// Firefox 1.0+
var isFirefox = typeof InstallTrigger !== 'undefined';

// Safari 3.0+ "[object HTMLElementConstructor]" 
var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && window['safari'].pushNotification));

// Internet Explorer 6-11
var isIE = /*@cc_on!@*/false || !!document.documentMode;

// Edge 20+
var isEdge = !isIE && !!window.StyleMedia;

// Chrome 1 - 79
var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);

// Edge (based on chromium) detection
var isEdgeChromium = isChrome && (navigator.userAgent.indexOf("Edg") != -1);

// Blink engine detection
var isBlink = (isChrome || isOpera) && !!window.CSS;

/*
var output = 'Detecting browsers by ducktyping:<hr>';
output += 'isFirefox: ' + isFirefox + '<br>';
output += 'isChrome: ' + isChrome + '<br>';
output += 'isSafari: ' + isSafari + '<br>';
output += 'isOpera: ' + isOpera + '<br>';
output += 'isIE: ' + isIE + '<br>';
output += 'isEdge: ' + isEdge + '<br>';
output += 'isEdgeChromium: ' + isEdgeChromium + '<br>';
output += 'isBlink: ' + isBlink + '<br>';
document.body.innerHTML = output;
*/
