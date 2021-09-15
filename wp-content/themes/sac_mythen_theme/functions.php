<?php
/*
*  Author: Matthias Ulrich
*  URL: https://ulrich.digital
*/

setlocale(LC_TIME, "de_DE.utf8");

/* =============================================================== *\ 
 	 
	 Customized Core
	 
\* =============================================================== */ 

/* =============================================================== *\ 
   JavaScripts + Styles
\* =============================================================== */ 

/* Backend */
add_action( 'admin_enqueue_scripts', 'add_backend_javascripts' );
function add_backend_javascripts() {
	wp_enqueue_media();

	$url_h0 = get_stylesheet_directory_uri() . '/js/ulrich_admin.js';
   	wp_enqueue_script( 'jquery' );
   	wp_enqueue_script( 'my-admin-js' );
	wp_enqueue_script( 'my-admin-js', $url_h0, array('jquery', 'acf'), null, true );

	wp_localize_script( 'my-admin-js', 'myAjax', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' ), 
		'post_id' => get_the_ID(),
		)
	);        
}

add_action('admin_enqueue_scripts', 'add_backend_styles');  
function add_backend_styles() {	
	wp_enqueue_style('admin-styles', get_template_directory_uri().'/style-admin.css');
	wp_register_style('font_awesome', get_stylesheet_directory_uri() . '/css/all.css', array(), '1.0', 'all');
	wp_enqueue_style('font_awesome');
}

/* Frontend */
add_action( 'wp_enqueue_scripts', 'add_frontend_javascripts' );
function add_frontend_javascripts() {
	//$url_h1 = get_stylesheet_directory_uri().'/js/slick.min.js';
	//$url_h2 = get_stylesheet_directory_uri().'/js/slick-lightbox.js';
	$url_h0 = get_stylesheet_directory_uri().'/js/jquery-ui.min.js';
	$url_h2 = get_stylesheet_directory_uri().'/js/isotope.pkgd.min.js?v='. time() . '';
	$url_h3 = get_stylesheet_directory_uri().'/js/ulrich.js?v='. time() . '';
    //wp_enqueue_script( 'eigener_Name', pfad_zum_js, abhaengigkeit (zb jquery zuerst laden), versionsnummer, bool (true=erst im footer laden) );
	//wp_enqueue_script( 'jquery' );
	//wp_enqueue_script( 'handler_name_1', $url_h1, array('jquery'), null, false );
	//wp_enqueue_script( 'handler_name_2', $url_h2, array('jquery'), null, false );
	wp_enqueue_script( 'handler_name_0', $url_h0, array('jquery'), null, false );
	wp_enqueue_script( 'handler_name_2', $url_h2, array('jquery'), null, true );
	wp_enqueue_script( 'handler_name_3', $url_h3, array('jquery'), null, true );
}

add_action('wp_enqueue_scripts', 'add_frontend_styles');
function add_frontend_styles() {
	//wp_register_style( $handle, $src, $deps, $ver, $media );
    wp_register_style('main',  get_stylesheet_directory_uri() . "/style.css?" . date("h:i:s"), array(), '1.0', 'all');
    wp_enqueue_style('main');
	wp_register_style('font_awesome', get_stylesheet_directory_uri() . '/webfonts/all.css', array(), '1.0', 'all');
    wp_enqueue_style('font_awesome');

	/*
	wp_register_style('adobe_fonts', 'https://use.typekit.net/owr0crc.css', array(), '1.0', 'all');
	wp_enqueue_style('adobe_fonts');
	wp_register_style('slick',  get_stylesheet_directory_uri() . '/js/slick.css', array(), '1.0', 'all');
	wp_enqueue_style('slick');
	wp_register_style('slick-lightbox', get_stylesheet_directory_uri() . '/js/slick-lightbox.css', array(), '1.0', 'all');
	wp_enqueue_style('slick-lightbox');
    */
}

/* =============================================================== *\ 
 	 Clean-Up <header>
\* =============================================================== */ 
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

function remove_tinymce_emoji($plugins){
	if (!is_array($plugins)){
		return array();
	}
	return array_diff($plugins, array('wpemoji'));
}

/* =============================================================== *\ 
 	 Admin
	 - Remove Admin-Menu-Elements
	 - Remove Admin-Menu-Bar-Elements
	 - Custom Admin-Menu Order
\* =============================================================== */ 
add_action('admin_menu', 'remove_menus');
function remove_menus () {
	global $menu;
	$restricted = array(__('Beiträge'), __('Kommentare'));
	$restricted = array(__('Kommentare'));
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
			unset($menu[key($menu)]);
		}
	}
}

add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );
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

/* Benutzerdefinierte Reihenfolge des Backend-Menu */
//add_filter( 'custom_menu_order', '__return_true' );
//add_filter( 'menu_order', 'wpse_custom_menu_order', 10, 1 );
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

/* =============================================================== *\ 
 	 Add Options-Page 
\* =============================================================== */ 
//include('theme_options.php');

/* =============================================================== *\ 
 	 Load Comment-Reply-Script 
\* =============================================================== */ 
add_action( 'comment_form_before', 'enqueue_comment_reply_script' );
function enqueue_comment_reply_script() {
	if ( get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/* =============================================================== *\ 
 	 Remove "Load-More"-Button in Media-Library 
\* =============================================================== */ 
add_filter( 'media_library_infinite_scrolling', '__return_true' );

/* =============================================================== *\ 
 	 Add Title-Separator 
\* =============================================================== */ 
add_filter( 'document_title_separator', 'document_title_separator' );
function document_title_separator( $sep ) {
    $sep = '|';
    return $sep;
}

/* =============================================================== *\ 
 	 Add ... to title, if necessary 
\* =============================================================== */ 
add_filter( 'the_title', 'mytitle' );
function mytitle( $title ) {
    if ( $title == '' ) {
        return '...';
    } else {
        return $title;
    }
}

/* =============================================================== *\ 
 	 Remove automatically P-Tags 
\* =============================================================== */ 
$priority = has_filter( 'the_content', 'wpautop' );
if ( false !== $priority ) {
	remove_filter( 'the_content', 'wpautop', $priority );
}

/* =============================================================== *\ 
 	 Add Title-Tag to <head> 
	 Add Post-thumbnails 
	 Remove unnecessary "type"-attribute from javascript files
	 Add RSS feed links to HTML <head>	 
	 Register Nav-Menus
\* =============================================================== */ 
//https://developer.wordpress.org/reference/functions/add_theme_support/
add_action( 'after_setup_theme', 'ulrich_digital_setup' );
function ulrich_digital_setup(){
    add_theme_support( 'title-tag' );
    //add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'script', 'style' ] );
	add_theme_support( 'automatic-feed-links' );
    register_nav_menus(
	   array(
		   'main-menu' => __( 'Main Menu', 'ulrich_digital_blank' ),
		   'footer_menu_1' => __( 'Footer Menu 1', 'ulrich_digital_blank' ),
		   'footer_menu_2' => __( 'Footer Menu 2', 'ulrich_digital_blank' )
	    )
    );
}

/* =============================================================== *\ 
 	 Add Custom Image-Sizes 
	 Add Custom Image-Sizes to Backend-Choose
	 Enable SVG
\* =============================================================== */ 

//add_action('after_setup_theme', 'eigene_bildgroessen', 11);
function eigene_bildgroessen() {
	add_image_size('facebook_share', 1200, 630, true);
	add_image_size('startseiten_slider', 2000, 1125, true);
	add_image_size('angebot_header_bild', 2000, 2000, false);
	add_image_size('galerie_thumb', 700, 700, true);
}

/* Add Image-Sizes to Backend-Choose */
//add_filter('image_size_names_choose', 'bildgroessen_auswaehlen');
function bildgroessen_auswaehlen($sizes) {
	$custom_sizes = array('facebook_share' => 'Facebook-Vorschaubild');
	return array_merge($sizes, $custom_sizes);
}

/* SVG erlauben */
add_filter('upload_mimes', 'add_svg_to_upload_mimes');
function add_svg_to_upload_mimes($upload_mimes){
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}

/* =============================================================== *\ 
 	 Allow Contributors to uplaod media 
\* =============================================================== */ 
if ( current_user_can('contributor') && !current_user_can('upload_files') ){
    add_action('admin_init', 'allow_contributor_uploads');
}
function allow_contributor_uploads() {
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
}

/* =============================================================== *\ 
 	 Enable Widgets 
\* =============================================================== */ 
add_action( 'widgets_init', 'ulrichdigital_blank_widgets_init' );
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

/* =============================================================== *\ 
 	 Custom Admin-Logo 
\* =============================================================== */ 
add_action( 'login_enqueue_scripts', 'my_login_logo' );
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

/* =============================================================== *\ 
	Admin
 	- Add Custom Footer 
\* =============================================================== */ 
add_filter( 'admin_footer_text', 'backend_entwickelt_mit_herz' );
function backend_entwickelt_mit_herz( $text ) {
	return ('<span style="color:black;">Entwickelt mit </span><span style="color: red;font-size:20px;vertical-align:-3px">&hearts;</span><span style="color:black;"</span><span> von <a href="https://ulrich.digital" target="_blank">ulrich.digital</a></span>' );
}

/* =============================================================== *\ 
   Custom-Post-Types 
\* =============================================================== */ 
add_action('init','ab_register_post_type_touren');
function ab_register_post_type_touren(){
	$supports = array('title', 'editor', 'custom-fields', 'revisions');
	$labels = array(
		'menu_name' => 'Touren',
	    'name' => 'Touren',
	    'singular_name' => 'Touren',
	    'add_new' => 'Tour hinzuf&uuml;gen',
	    'add_new_item' => 'Neue Tour hinzuf&uuml;gen',
	    'edit_item' => 'Tour bearbeiten',
	    'new_item' => 'Neue Tour',
	    'view_item' => 'Tour anzeigen',
	    'search_items' => 'Tour suchen',
	    'not_found' => 'Keibe Tour gefunden',
	    'not_found_in_trash' => 'Keine Tour im Papierkorb',
		);
	$touren_args = array(
	    'supports' => $supports,
	    'labels' => $labels,
	    'description' => 'Post-Type f&uuml;r Touren',
	    'public' => true,
	    'show_in_nav_menus' => true,
	    'show_in_menu' => true,
		'show_in_rest' => true,
	    'has_archive' => true,
	    'query_var' => true,
		'menu_icon' => 'dashicons-hammer',
	    'taxonomies' => array('topics', 'category'),
	    'rewrite' => array(
	        'slug' => 'touren',
	        'with_front' => true
	   		),
		);
	register_post_type('touren', $touren_args);
}


/* =============================================================== *\ 

 	 Customized Plugins

\* =============================================================== */ 
  
/* ACF - Options-Page */ 
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}


/* =============================================================== *\ 
 	 ACF-Blocks 
\* =============================================================== */ 
add_action('acf/init', 'my_acf_init_block_types');
function my_acf_init_block_types() {

    if( function_exists('acf_register_block_type') ) {

        acf_register_block_type(array(
            'name'              => 'touren-kurzinfo',
            'title'             => 'Touren-Kurzinfo',
            'description'       => 'Info-Daten der Tour',
            'render_template'   => 'blocks/acf-touren-kurzinfo/block.php',
            'category'          => 'formatting',
            'icon'              => '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="mountain" class="svg-inline--fa fa-mountain fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M503.2 393.8L280.1 44.25c-10.42-16.33-37.73-16.33-48.15 0L8.807 393.8c-11.11 17.41-11.75 39.42-1.666 57.45C17.07 468.1 35.92 480 56.31 480h399.4c20.39 0 39.24-11.03 49.18-28.77C514.9 433.2 514.3 411.2 503.2 393.8zM256 111.8L327.8 224H256L208 288L177.2 235.3L256 111.8z"></path></svg>',
            'keywords'          => array( 'testimonial', 'quote' ),
			'mode'			=> 'edit',
			'supports'		=> [
				'align'			=> false,
				'mode'			=> false,
				'anchor'		=> true,
				'customClassName'	=> true,
				'jsx' 			=> false,
			]
        ));
		
		acf_register_block_type(array(
            'name'              => 'tourdatum',
            'title'             => 'Datum der Tour',
            'description'       => 'Fügt ein Datum ein.',
            'render_template'   => 'blocks/acf-tourdatum/block.php',
            'category'          => 'formatting',
            'icon'              => '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar-days" class="svg-inline--fa fa-calendar-days fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M0 464C0 490.5 21.5 512 48 512h352c26.5 0 48-21.5 48-48V192H0V464zM320 272C320 263.2 327.2 256 336 256h32C376.8 256 384 263.2 384 272v32c0 8.836-7.162 16-16 16h-32C327.2 320 320 312.8 320 304V272zM320 400c0-8.836 7.164-16 16-16h32c8.838 0 16 7.164 16 16v32c0 8.836-7.162 16-16 16h-32c-8.836 0-16-7.164-16-16V400zM192 272C192 263.2 199.2 256 208 256h32C248.8 256 256 263.2 256 272v32c0 8.836-7.162 16-16 16h-32C199.2 320 192 312.8 192 304V272zM192 400C192 391.2 199.2 384 208 384h32c8.838 0 16 7.164 16 16v32c0 8.836-7.162 16-16 16h-32C199.2 448 192 440.8 192 432V400zM64 272C64 263.2 71.16 256 80 256h32C120.8 256 128 263.2 128 272v32C128 312.8 120.8 320 112 320h-32C71.16 320 64 312.8 64 304V272zM64 400C64 391.2 71.16 384 80 384h32C120.8 384 128 391.2 128 400v32C128 440.8 120.8 448 112 448h-32C71.16 448 64 440.8 64 432V400zM400 64H352V31.1C352 14.4 337.6 0 320 0C302.4 0 288 14.4 288 31.1V64H160V31.1C160 14.4 145.6 0 128 0S96 14.4 96 31.1V64H48C21.49 64 0 85.49 0 112V160h448V112C448 85.49 426.5 64 400 64z"></path></svg>',
            'keywords'          => array( 'datum', '' ),
			'mode'			=> 'edit',
			'supports'		=> [
				'align'			=> false,
				'mode'			=> false,
				'anchor'		=> true,
				'customClassName'	=> true,
				'jsx' 			=> false,
			]
        ));
		acf_register_block_type(array(
			'name'              => 'touren-details',
			'title'             => 'Details der Tour',
			'description'       => 'Details der Tour erfassen',
			'render_template'   => 'blocks/acf-touren-details/block.php',
			'category'          => 'formatting',
			'icon'              => '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="person-hiking" class="svg-inline--fa fa-person-hiking fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M240 96c26.5 0 48-21.5 48-48S266.5 0 240 0C213.5 0 192 21.5 192 48S213.5 96 240 96zM80.01 287.1c7.31 0 13.97-4.762 15.87-11.86L137 117c.3468-1.291 .5125-2.588 .5125-3.866c0-7.011-4.986-13.44-12.39-15.13C118.4 96.38 111.7 95.6 105.1 95.6c-36.65 0-70 23.84-79.32 59.53L.5119 253.3C.1636 254.6-.0025 255.9-.0025 257.2c0 7.003 4.961 13.42 12.36 15.11L76.01 287.5C77.35 287.8 78.69 287.1 80.01 287.1zM368 160h-15.1c-8.875 0-15.1 7.125-15.1 16V192h-34.75l-46.75-46.75C243.4 134.1 228.6 128 212.9 128C185.9 128 162.5 146.3 155.9 172.5L129 280.3C128.4 282.8 128 285.5 128 288.1c0 8.325 3.265 16.44 9.354 22.53l86.62 86.63V480c0 17.62 14.37 32 31.1 32s32-14.38 32-32v-82.75c0-17.12-6.625-33.13-18.75-45.25l-46.87-46.88c.25-.5 .5-.875 .625-1.375l19.1-79.5l22.37 22.38C271.4 252.6 279.5 256 288 256h47.1v240c0 8.875 7.125 16 15.1 16h15.1C376.9 512 384 504.9 384 496v-320C384 167.1 376.9 160 368 160zM81.01 472.3c-.672 2.63-.993 5.267-.993 7.86c0 14.29 9.749 27.29 24.24 30.89C106.9 511.8 109.5 512 112 512c14.37 0 27.37-9.75 30.1-24.25l25.25-101l-52.75-52.75L81.01 472.3z"></path></svg>',
			'keywords'          => array( 'datum', '' ),
			'mode' => 'edit',
			'supports' =>[
				'mode' => false,
				]
		));
    }
}


/* =============================================================== *\ 
   Block-Template: Tourenportal 
   @link https://developer.wordpress.org/block-editor/developers/block-api/block-templates/
\* =============================================================== */ 
function block_template_tourenportal() {
   $page_type_object = get_post_type_object( 'touren' );
   $page_type_object->template = [
	   //[ 'core/group', [], [
		   [ 'acf/tourdatum'],
		   [ 'acf/touren-kurzinfo'],
		   [ 'acf/touren-details'],
		   
		   /*[ 'core/paragraph',['placeholder' => 'test'] ],*/
	   //] ],
   ];
}
add_action( 'init', 'block_template_tourenportal' );

/* Woocommerce */
/*Woocommerce Unterstützung Backend > WooCommerce > Status deklarieren */
/*add_action( 'after_setup_theme', 'setup_woocommerce_support' ); 
function setup_woocommerce_support() { 
	add_theme_support('woocommerce'); 
	} */
	
/* Contact Form 7 */


/* =============================================================== *\ 

	Customized for Customer
	
\* =============================================================== */ 


 
/* =============================================================== *\ 
 	 Meta-Keys befüllen 
	 - Tourdatum
	 - Autor
	 - Bereiche
\* =============================================================== */ 
//add_action('pre_post_update', 'before_data_is_saved_function', 100, 2);
add_action('save_post', 'save_in_meta', 10, 2);

function save_in_meta($post_id) {
	if('touren'==get_post_type($post_id)):	
		$meta = get_post_meta($post_id);
		
		$my_tourdatum = get_current_tourdatum($post_id);
		$my_tourenleiter_name = get_current_tourenleiter_name($post_id);
		$my_bereiche = get_current_bereiche($post_id);
		
		if(isset($meta['current_tour_date'])):
			delete_post_meta($post_id, 'current_tour_date');
		endif;
		
		if(isset($meta['tourenleiter_name'])):
			delete_post_meta($post_id, 'tourenleiter_name');
		endif;
		
		if(isset($meta['bereiche'])):
			delete_post_meta($post_id, 'bereiche');
		endif;
		
		update_post_meta( $post_id, 'current_tour_date',  $my_tourdatum);
		update_post_meta( $post_id, 'tourenleiter_name',  $my_tourenleiter_name);
		update_post_meta( $post_id, 'bereiche',  $my_bereiche);
	endif;
}  
/* =============================================================== *\ 

 	 DEV 

\* =============================================================== */ 
 /*

$alle_posts = array(261,390,386,380,267,211,180,29);
foreach($alle_posts as $my_post):
	save_in_meta($my_post);
endforeach;
*/
/* =============================================================== *\ 
 	 Aktuelles-Tourdatum 
\* =============================================================== */ 
function get_current_tourdatum($post_id){			
	$aktuelles_tourdatum = ""; // wird nachher mit Datum überschrieben
	$block_data = "";
	$blocks = parse_blocks( get_the_content(null, false, $post_id) );
	foreach($blocks as $block){
		if('acf/tourdatum' === $block['blockName']){ //alle Tourdatum-Blöcke durchgehen			
			
			$block_data = $block['attrs']['data']; 
			$aktuelles_tourdatum = $block_data['tourdatum']; // tourdatum, wenn tour_findet_statt oder tour_abgesagt 
			
			$durchfuhrungs_status = $block_data['durchfuhrung']; //tour_findet_statt // tour_verschieben // tour_abgesagt	
			if($durchfuhrungs_status == "tour_verschieben"):
				/*
				["verschiebe_daten_(0)_tour_verschoben_auf"] > wenn array enthält, ist die Checkbox (auf dieses Datum verschieben) gewählt worden
				["verschiebe_daten_(0)_verschiebe_datum"]=> das dazugehörige verschiebe datum
				*/
				foreach($block_data as $key => $value):
					if( ("verschiebe_daten" == substr($key,0,16)) && ("tour_verschoben_auf" == substr($key,-19)) ):
						if($value>0): // wenn ja, dann ist checkbox (auf dieses Datum verschieben) gewählt worden
						
							//iterator herausfinden
							$my_iterator = $key;
							$my_iterator = str_replace("verschiebe_daten_", "", $my_iterator);
							$my_iterator = str_replace("_tour_verschoben_auf", "", $my_iterator);
							
							//das verschiebe datum ist abgelegt unter: verschiebe_daten_0_verschiebe_datum
							$current_verschiebe_datum = "verschiebe_daten_" . $my_iterator . "_verschiebe_datum";
							if(array_key_exists($current_verschiebe_datum,  $block_data)==true):
								$aktuelles_tourdatum = $block_data[$current_verschiebe_datum];
							endif;
							
						endif;
					endif;
				endforeach;
			endif;
			
			/* =============================================================== *\ 
			   Tour regulär oder abgesagt 
			\* =============================================================== */ 
			/*foreach($block_data as $mydata=>$myvalue){
				if(($myvalue=="tour_findet_statt")||($myvalue=="tour_abgesagt")){
					$aktuelles_tourdatum = $block_data['tourdatum'];
				}
			}*/
		}//block tourdatum
	}//foreach blocks as block

	return $aktuelles_tourdatum;	
}


/* =============================================================== *\ 
 	 Aktueller Tourenleiter-Name 
\* =============================================================== */ 
function get_current_tourenleiter_name($post_id){
	$post_author_id = get_post_field( 'post_author', $post_id );
	$auth_first_name = get_the_author_meta( 'first_name', $post_author_id );
	$auth_last_name = get_the_author_meta( 'last_name', $post_author_id);
	$name_tourenleiter = "$auth_first_name $auth_last_name";
	if(($auth_first_name == "") && ($auth_last_name == "")){
		$name_tourenleiter = get_the_author_meta( 'user_email', $post_author_id);
	}	
	
	// Tourenleiter-Name ggf. überschreiben 
	$blocks = parse_blocks( get_the_content($post_id) );
	foreach ( $blocks as $block ) {
		if('acf/touren-details' === $block['blockName']):
			if(array_key_exists('anmeldung_und_auskunft_name', $block['attrs']['data'])==true): 
				$name_tourenleiter = $block['attrs']['data']['anmeldung_und_auskunft_name'];
			endif;
		endif;
	}	
	return $name_tourenleiter;
}

/* =============================================================== *\ 
 	Aktuelle Bereiche, wie Sektion, Vetereanen, JO usw. 
\* =============================================================== */   
function get_current_bereiche($post_id){
//	$blocks = parse_blocks( get_the_content($post_id) );
	$blocks = parse_blocks( get_the_content(null, false, $post_id) );

	$my_bereiche= "";
	$my_bereiche_as_array = array();
	foreach ( $blocks as $block ) { 
		if('acf/touren-kurzinfo' === $block['blockName']){
			if((isset($block['attrs']['data']['bereich'])) && ($block['attrs']['data']['bereich']!="")): // Sektion usw.
				$anzahl_bereiche = count($block['attrs']['data']['bereich']);
				$my_iterator = 0;
				foreach($block['attrs']['data']['bereich'] as $my_bereich):
					$my_iterator++;
					if($anzahl_bereiche>$my_iterator):
						$my_bereiche .= $my_bereich . ", ";
					else:
						$my_bereiche .= $my_bereich;
					endif;
					array_push($my_bereiche_as_array, $my_bereich);
				endforeach;
			endif;
		} 
	} 
	return($my_bereiche);
};





/* =============================================================== *\ 
 	 Autodraft 
	 @header.php
\* =============================================================== */ 
function set_to_draft() {
	//if(!is_admin()):
		$the_query = get_posts( array('post_type'=>'touren','post_status'=>'publish,draft', 'numberposts' => -1));	
		foreach($the_query as $single_post) {
			$id=$single_post->ID;
			$current_tour_date=get_post_meta($id, 'current_tour_date', true );
			//echo $ad_close_date . "<br />";
			$today = date("Ymd");
			//echo $today . "<br />";
			if($current_tour_date!=''){
				if($current_tour_date<$today){
					$update_post = array(
						'ID' 			=> $id,
						'post_status'	=>	'draft',
						'post_type'	=>	'touren',
						'meta_key' => $current_tour_date,
					);
					wp_update_post($update_post);
				}else{
					//echo "id: " . $id . "tour-date: " .  $current_tour_date . "<br />";
				}	
			}
		}
	//endif;
}

/* =============================================================== *\ 
 	 Den Autoren nur die eigenen Beiträge im Backend anzeigen 
\* =============================================================== */ 
add_action('pre_get_posts', 'query_set_only_author' );
function query_set_only_author( $wp_query ) {
	global $current_user;
	if( is_admin() && !current_user_can('edit_others_posts') ) {
		$wp_query->set( 'author', $current_user->ID );
		add_filter('views_edit-post', 'fix_post_counts');
		add_filter('views_upload', 'fix_media_counts');
	}
}

/* =============================================================== *\ 
 	 Restrict Blocks by user roles
\* =============================================================== */ 
//https://www.role-editor.com/forums/topic/restrict-hide-gutenberg-blocks-by-role/
add_filter( 'allowed_block_types_all', 'misha_allowed_block_types', 10, 2 );
 
function misha_allowed_block_types( $allowed_blocks, $post ) {
    $user = wp_get_current_user();
    
    if ( in_array('administrator', $user->roles ) ) { // Do not change the list of default blocks for user with administrator role
        return $allowed_blocks;
    }

    if ( in_array('author', $user->roles ) ) {
        $allowed_blocks = array(
			'acf/tourdatum',
			'acf/touren-details',
			'acf/touren-kurzinfo',
            'core/image',
            'core/paragraph',
            'core/heading',
            'core/list'
        );    
        return $allowed_blocks;
    }
	  
	return $allowed_blocks;
} 

/* =============================================================== *\ 
 	 Admin-Columns anpassen 
	 + Touren-Leiter
	 + Tour-Datum
	 + Bereich
	 //https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
\* =============================================================== */ 
add_filter( 'manage_touren_posts_columns', 'touren_columns' );
function touren_columns( $columns ) {
	$columns = array(
		'title' => 'Title',
		'tourenleiter' => 'Tourenleiter',
		'current_tour_date' => 'Tour-Datum',
		'bereiche' => 'Bereich',
	);
	return $columns;
}

// Inhalte aus den Meta-Keys in die Kolonnen hinzufügen
add_action( 'manage_touren_posts_custom_column', 'touren_custom_column', 10, 2);
function touren_custom_column( $column, $post_id ) {
	if('tourenleiter' === $column){
		echo get_current_tourenleiter_name($post_id);
	}
	if('current_tour_date' === $column){
		echo make_human_date(get_post_meta($post_id, 'current_tour_date', true));
	}
	if('bereiche' === $column){
		echo get_post_meta($post_id, 'bereiche', true);
	}
}

// Kolonnen sortierbar machen
add_filter( 'manage_edit-touren_sortable_columns', 'touren_sortable_columns');
function touren_sortable_columns( $columns ) {
	$columns['current_tour_date'] = 'current_tour_date';
	$columns['tourenleiter'] = 'tourenleiter_name';
	$columns['bereiche'] = 'bereiche';
  	return $columns;
}

add_action( 'pre_get_posts', 'touren_posts_orderby' );
function touren_posts_orderby( $query ) {
	if( ! is_admin() || ! $query->is_main_query() ) { return; }
	
	$orderby = $query->get( 'orderby');
   	if( 'current_tour_date' == $orderby ) {
		$query->set('meta_key','current_tour_date');
		$query->set('orderby','meta_value');
	}elseif('tourenleiter_name'==$orderby){
		$query->set('meta_key','tourenleiter_name');
		$query->set('orderby','meta_value');
	}elseif('bereiche'==$orderby){
		$query->set('meta_key','bereiche');
		$query->set('orderby','meta_value');
	}
}



/* =============================================================== *\ 
	 Breadcrumb - Menu
	 @template archive.php
\* =============================================================== */ 
//https://kulturbanause.de/blog/wordpress-breadcrumb-navigation-ohne-plugin/

function nav_breadcrumb() {
	global $post;
	$delimiter = ' <i class="fa-solid fa-chevron-right" style="font-size:66%; vertical-align: middle; padding: 0 5px"></i>';
	$delimiter_two = ' | ';
	$home = 'Home'; 
	$before = '<span class="current-page">'; 
	$after = '</span>'; 
	$my_chip = "";
	$chip_start = "<div class='breadcrumb_item_container'>";
	$chip_end = "</div>";
	$homeLink = get_bloginfo('url');

	$blocks = parse_blocks( get_the_content() );
	foreach ( $blocks as $block ) {              
		if('acf/touren-kurzinfo' === $block['blockName']){
			// Sektion usw.
			if((isset($block['attrs']['data']['bereich'])) && ($block['attrs']['data']['bereich']!="")): // Sektion usw.
				$anzahl_bereiche = count($block['attrs']['data']['bereich']);
				$my_iterator = 0;
				foreach($block['attrs']['data']['bereich'] as $my_bereich):
					$my_iterator++;
					$my_bereich_class=strtolower($my_bereich);
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					$my_url = $homeLink . '/' . $slug['slug'] . '/#filter=.' . $my_bereich_class;
					if($anzahl_bereiche>$my_iterator):
						$my_chip .= '<a href="' . $my_url . '" class="' . $my_bereich_class . '">' . $my_bereich . '</a> <span> | </span> ';
					else:
						$my_chip .= '<a href="' . $my_url . '" class="' . $my_bereich_class . '">' . $my_bereich . '</a>';
					endif;
				endforeach;
			endif;
			// Bergtour T2
			$kurzinfo = render_block($block);
		}		
	}

	if ( !is_home() && !is_front_page() || is_paged() ) {

		echo '<nav class="breadcrumb">';

		if ( is_category()) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$thisCat = $cat_obj->term_id;
			$thisCat = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
			echo $before . single_cat_title('', false) . $after;

		} elseif ( is_day() ) {
			echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			echo $before . get_the_time('d') . $after;

		} elseif ( is_month() ) {
			echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			echo $before . get_the_time('F') . $after;

		} elseif ( is_year() ) {
			echo $before . get_the_time('Y') . $after;

		} elseif ( is_single() && !is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				//echo "hier";
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
				//echo $before . get_the_title() . $after;
				echo $chip_start . $my_chip . $chip_end;
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
				echo $before . get_the_title() . $after;
			}

		// archiv		
		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());

		} elseif ( is_attachment() ) {
			$parent = get_post($post->post_parent);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
			echo $before . get_the_title() . $after;

		} elseif ( is_page() && !$post->post_parent ) {
			echo $before . get_the_title() . $after;

		} elseif ( is_page() && $post->post_parent ) {
			$parent_id = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
			echo $before . get_the_title() . $after;

		} elseif ( is_search() ) {
			echo $before . 'Ergebnisse für Ihre Suche nach "' . get_search_query() . '"' . $after;

		} elseif ( is_tag() ) {
			echo $before . 'Beiträge mit dem Schlagwort "' . single_tag_title('', false) . '"' . $after;

		} elseif ( is_404() ) {
			echo $before . 'Fehler 404' . $after;
		}

		if ( get_query_var('paged') ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			echo ': ' . __('Seite') . ' ' . get_query_var('paged');
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
		}
		echo '</nav>';
	} 
} 
  
/* =============================================================== *\ 
 	 Human-Date
	 Funktion, welche aus einem YYYYMMDD ein d. M. YYYY macht
	 @acf-tourdatum/block.php
\* =============================================================== */ 
function make_human_date($date){
	$format = '%A, %e. %B %Y'; 	// Montag, 02. Januar 1970
	if(is_archive()){
		$format = '%d.%m.%Y'; 	// 01.01.2021
	} 
	
	$timestamp = strtotime($date);
	$human_date = strftime ( $format , $timestamp);		
	return $human_date;
}



/* =============================================================== *\ 
 	 Single-Tour-Details
	 erzeugt HTML-Output mit Icon und Content:
	 div.detail_item (+class)
 	  - div.icon
 	  - div.content 
	  	 
	 @acf-touren-details/block.php
\* =============================================================== */ 

// Icons verfügbar machen 
require_once( get_template_directory()  . '/template_parts/icons_art_der_tour.php'); 

// HTML-Otuput einfache Textfelder 
function single_tour_textfield($label, $content){
	global ${'icon_' . $label}; // $icon_programm	
	$single_tour_textfield_html = "<div class='detail_item " . $label . "'>";
	$single_tour_textfield_html .= "<div class='icon'>" . ${'icon_' . $label} . "</div>";
	$single_tour_textfield_html .= "<div class='content'>" . $content . "</div>";
	$single_tour_textfield_html .= "</div>";
	return $single_tour_textfield_html;
}

// HTML-Otuput Karten
function single_tour_array($label, $content){
	global ${'icon_' . $label}; // $icon_programm
	$single_tour_textfield_html = "<div class='detail_item " . $label . "'>";
	$single_tour_textfield_html .= "<div class='icon'>" . ${'icon_' . $label} . "</div>";
	$single_tour_textfield_html .= "<div class='content'>" . $content . "</div>";
	$single_tour_textfield_html .= "</div>";
	return $single_tour_textfield_html;
}

 // HTML-Output Button (hier Anmelden-Feld)
function single_tour_buttton_field($label, $link_text, $link_target, $link_url){
	global ${'icon_' . $label}; // $icon_programm
	$single_tour_textfield_html = "<div class='detail_item " . $label . "'>";
	$single_tour_textfield_html .= "<div class='icon'>" . ${'icon_' . $label} . "</div>";
	$single_tour_textfield_html .= "<a href='" . $link_target . ":" . $link_url . "' class='content'>" . $link_text . "</a>";
	$single_tour_textfield_html .= "</div>";
	return $single_tour_textfield_html;
}


/* =============================================================== *\ 
 	 Mails, wenn Tour eingereicht wird
	 - Mails an alle Redakteure
\* =============================================================== */ 
function notify_editors_for_pending( $post ) {

	if ( 'touren' == get_post_type($post) ):
		//Autor des Posts
		$user_info = get_userdata ($post->post_author);
		$user_email = $user_info->user_email;
		$strSubject = $user_info->user_nicename . ' hat die Tour «' . $post->post_title . '» eingereicht';

		//Empfänger
		$touren_admins = get_users( array( 'role__in' => array( 'administrator', 'editor' ) ) );
		$strMessage = "";
		$redakteur = array();
		$headers = array('Content-Type: text/html; charset=UTF-8');

		foreach ( $touren_admins as $touren_admin ) {
			$redakteur['email'] = $touren_admin->user_email;
			$redakteur['nicename'] = $touren_admin->user_nicename;
			$strMessage  = "Hallo <strong>" . $redakteur['nicename'] . "</strong><br /><br />";
			$strMessage .= $user_info->user_nicename . ' hat die Tour <strong>&laquo;' . $post->post_title . '&raquo;</strong> eingereicht. <br/>';
			$strMessage .= "<a href=" . get_edit_post_link( $post->ID, $context ) . ">Hier</a> geht es zur Tour: " . get_edit_post_link( $post->ID, $context ) . "<br/><br/>";
			$strMessage .= "Bei Fragen oder Unklarheiten erreichst Du den/die Verfasser/in über folgende Mail-Adresse: <a href='mailto:'" . $user_email . "'> " . $user_email . "</a><br/><br/>";
			$strMessage .= "Wenn alles in Ordnung ist, kannst Du die Tour veröffentlichen, der/die Verfasser/in wird darüber automatisch informiert. <br/><br/>";
			$strMessage .= "Vielen Dank für deine wertvolle Mitarbeit!<br/><br/>";
			$strMessage .= "SAC Sektion-Mythen <br/>";
			$strMessage .= "<i>Gesendet vom Webseiten-Käfer</i> <br/>";
		
			wp_mail($redakteur['email'], $strSubject, $strMessage, $headers);
		}
	endif;
}

add_action( 'new_to_pending', 'notify_editors_for_pending');
add_action( 'draft_to_pending', 'notify_editors_for_pending' );
add_action( 'auto-draft_to_pending', 'notify_editors_for_pending' );
add_action( 'pending_to_pending', 'notify_editors_for_pending' );


// Mail an Verfasser
function notify_author_for_pending($post){
	$user_info = get_userdata ($post->post_author);
	$user_email = $user_info->user_email;
	$user_name = esc_html($user_info->user_nicename);
	$strSubject = 'Die Tour «' . $post->post_title . '» wurde eingereicht';
	$headers = array('Content-Type: text/html; charset=UTF-8');

	$strMessage = 'Hallo <strong>' . $user_name . '</strong><br /><br />';
	$strMessage .= 'Die Touren-Chefs haben Deine Tour <strong>«' . $post->post_title . '»</strong> erhalten.<br /><br />';
	$strMessage .= 'Sollte der zuständige Touren-Chef noch Fragen an Dich haben, wird er direkt mit Dir Kontakt aufnehmen.<br /><br />';
	$strMessage .= 'Wenn Deine Ausschreibung in Ordnung ist, wird diese veröffentlicht.<br /> ';
	$strMessage .= 'Selbstverständlich wirst Du darüber ebenfalls per Mail benachrichtigt.<br /><br />';
	$strMessage .= "Wir danken Dir herzlich für Deine wertvolle Mitarbeit!<br/><br/>";
	$strMessage .= "SAC Sektion-Mythen <br/>";
	$strMessage .= "<i>diese Nachricht wurde automatisch generiert</i> <br/>";
	wp_mail($user_email, $strSubject, $strMessage, $headers);
}

add_action( 'new_to_pending', 'notify_author_for_pending');
add_action( 'draft_to_pending', 'notify_author_for_pending' );
add_action( 'auto-draft_to_pending', 'notify_author_for_pending' );
add_action( 'pending_to_pending', 'notify_author_for_pending' );

//Mail beim Veröffentlichen


/* =============================================================== *\ 
 	 Tourenportal: Platzhalter für Titel anpassen
\* =============================================================== */ 
function wpb_change_title_text( $title ){
     $screen = get_current_screen();
  
     if  ( 'touren' == $screen->post_type ) {
          $title = 'Hier Touren-Titel eintragen';
     }
     return $title;
}
add_filter( 'enter_title_here', 'wpb_change_title_text' );

function enhancement_gutenberg_isu(){
    $imageIsRequired = __('Featured image is required', 'wpvue')
    ?>
    <script type="text/javascript">
	(function($, undefined){

	var gutenbergValidation = new acf.Model({
		wait: 'ready',
		initialize: function(){
			
			// Customize Gutenberg editor only.
			if( acf.isGutenberg() ) {
				this.customizeEditor();
			}
		},
		customizeEditor: function(){
			
			// Extract vars.
			var editor = wp.data.dispatch( 'core/editor' );
			var notices = wp.data.dispatch( 'core/notices' );
			
			// Reference original method.
			var savePost = editor.savePost;
			
			// Override core method.
			editor.savePost = function(){
				
				// Validate the editor form.
				var valid = acf.validateForm({
					form: $('#editor'),
					reset: true,
					complete: function( $form, validator ){
						
						// Always unlock the form after AJAX.
						editor.unlockPostSaving( 'acf' );
					},
					failure: function( $form, validator ){
						
						// Get validation error and append to Gutenberg notices.
						var notice = validator.get('notice');
						notices.createErrorNotice( notice.get('text'), { 
							id: 'acf-validation', 
							isDismissible: true
						});
						notice.remove();
					},
					success: function(){
						
						// Save post on success.
						savePost();
					}
				});
				
				// Lock the form if waiting for AJAX response.
				if( !valid ) {
					editor.lockPostSaving( 'acf' );
					return false;
				}
				
				// Save post as normal.
				savePost();
			}
		}
	});

	})(jQuery);
    
    </script>
	
<?php
}
add_action('admin_footer', 'enhancement_gutenberg_isu');
?>
