<?php
/*
*  Author: Matthias Ulrich
*  URL: https://ulrich.digital
*/

setlocale(LC_TIME, "de_DE.utf8");

/* =============================================================== *\ 
 	 
	 Backend
	 
\* =============================================================== */ 

/* =============================================================== *\
   Backend JavaScripts + CSS
\* =============================================================== */

add_action( 'admin_enqueue_scripts', 'ud_add_backend_javascripts' );
function ud_add_backend_javascripts() {
	wp_enqueue_media(); // Enqueues all scripts, styles, settings, and templates necessary to use all media JS APIs.
   	
	wp_enqueue_style( 'admin-styles', get_stylesheet_directory_uri() . "/style-admin.css", [], filemtime( get_stylesheet_directory() . "/style-admin.css" ) );
	wp_enqueue_style( 'font_awesome', get_stylesheet_directory_uri() . "/css/all.css", [], filemtime( get_stylesheet_directory() . "/css/all.css" ) );
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'my-admin-js', get_stylesheet_directory_uri() . '/js/ulrich_admin.js', array('jquery'),  filemtime( get_stylesheet_directory() . "/js/ulrich_admin.js" ), true );

	wp_localize_script( 'my-admin-js', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'post_id' => get_the_ID(),)
	);        
}


/* =============================================================== *\ 
   Add Options-Page 
\* =============================================================== */ 
//include('theme_options.php');


/* =============================================================== *\ 
   Add Title-Tag to <head> 
   Add Post-thumbnails 
   Register Nav-Menus
\* =============================================================== */ 
//https://developer.wordpress.org/reference/functions/add_theme_support/
add_action( 'after_setup_theme', 'ulrich_digital_setup' );
function ulrich_digital_setup(){
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
	add_theme_support('responsive-embeds');
	//add_theme_support( 'automatic-feed-links' );
    	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	global $content_width;
	if (!isset($content_width)) {
		$content_width = 1920;
	}
	
	register_nav_menus(
	   array(
		   'main-menu' => 'Main Menu',
		   'footer_menu_1' => 'Footer Menu 1',
		   'footer_menu_2' => 'Footer Menu 2',
	    )
    );
}


/* =============================================================== *\ 
   Enable Widgets 
\* =============================================================== */ 

function ud_widgets_init() {
	register_sidebar( array (
		'name' =>'Sidebar Widget Area',
		'id' => 'primary-widget-area',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
}
add_action( 'widgets_init', 'ud_widgets_init' );
  
  
  
/* =============================================================== *\ 

 	 Media 

\* =============================================================== */ 

/* =============================================================== *\ 
 	 enable oversized images 
\* =============================================================== */ 
 //add_filter('big_image_size_threshold', '__return_false');

 
/* =============================================================== *\ 
   Remove unused image sizes 
\* =============================================================== */ 

function ud_remove_unused_image_sizes($sizes){
	unset($sizes['medium_large']);
	unset($sizes['1536x1536']);
	unset($sizes['2048x2048']);
	return $sizes;
}  
//add_filter('intermediate_image_sizes_advanced', 'ud_remove_unused_image_sizes');


/* =============================================================== *\ 
   Add custom image sizes  
\* =============================================================== */ 

function ud_add_custom_image_sizes() {
   add_image_size('facebook_share', 1200, 630, true);
   add_image_size('startseiten_slider', 2000, 1125, true);
   add_image_size('angebot_header_bild', 2000, 2000, false);
   add_image_size('galerie_thumb', 700, 700, true);
}
//add_action('after_setup_theme', 'ud_add_custom_image_sizes', 11);


/* =============================================================== *\ 
   Add custom image sizes to backend choose
\* =============================================================== */ 
 
function ud_add_custom_image_sizes_to_backend_choose($sizes) {
	$custom_sizes = array('facebook_share' => 'Facebook-Vorschaubild');
	return array_merge($sizes, $custom_sizes);
}
//add_filter('image_size_names_choose', 'ud_add_custom_image_sizes_to_backend_choose');


/* =============================================================== *\ 
   Enable SVG 
\* =============================================================== */ 
 
function ud_add_svg_to_upload_mimes($upload_mimes){
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter('upload_mimes', 'ud_add_svg_to_upload_mimes');


/* =============================================================== *\
   Allow Contributors to uplaod media
\* =============================================================== */

if ( current_user_can('contributor') && !current_user_can('upload_files') ){
    add_action('admin_init', 'ud_allow_contributor_uploads');
}
function ud_allow_contributor_uploads() {
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
}


/* =============================================================== *\
   Remove "Load-More"-Button in Media-Library
\* =============================================================== */

add_filter( 'media_library_infinite_scrolling', '__return_true' );


/* =============================================================== *\
   Regenerate image sizes
\* =============================================================== */

require_once(ABSPATH . 'wp-admin/includes/image.php');

// Put the function in a class to make it more extendable
class GB_regen_media {
    public function gb_regenerate($imageId) {
        $imagePath = wp_get_original_image_path($imageId);
        if ($imagePath && file_exists($imagePath)) {
            wp_generate_attachment_metadata($imageId, $imagePath);
        }
    }
}

function ud_regen_load() {
	$gb_regen_media = new GB_regen_media();
	//$i = imageID
	for($i = 5752; $i <= 5762; $i++):
		$gb_regen_media->gb_regenerate($i);
	endfor;
}
//add_action('init', 'ud_regen_load');


  
/* =============================================================== *\ 

 	 Custom Post Types 

\* =============================================================== */ 

function ud_register_post_type_touren(){
   $supports = array('title', 'editor', 'thumbnail','post-thumbnails', 'custom-fields', 'revisions');
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
//add_action('init','ud_register_post_type_touren');
  
  
/* =============================================================== *\ 

 	 Blocks 

\* =============================================================== */ 

/* =============================================================== *\ 
   ACF Blocks 
\* =============================================================== */ 
function ud_acf_init_block_types() {
    if( function_exists('acf_register_block_type') ) {
		
        acf_register_block_type(array(
            'name'              => 'touren-kurzinfo',
            'title'             => 'Touren-Kurzinfo',
            'description'       => 'Info-Daten der Tour',
            'render_template'   => 'blocks/acf-touren-kurzinfo/block.php',
            'category'          => 'formatting',
            'icon'              => 'admin-comments',
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
            'icon'              => 'admin-comments',
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
			'icon'              => 'admin-comments',
			'keywords'          => array( 'datum', '' ),
			'mode' => 'edit',
			'supports' =>[
				'mode' => false,
				]
		));
    }
}
//add_action('acf/init', 'ud_acf_init_block_types');


/* =============================================================== *\ 
   Block-Template
   @link https://developer.wordpress.org/block-editor/developers/block-api/block-templates/
\* =============================================================== */ 
function ud_block_template_tourenportal() {
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
//add_action( 'init', 'ud_block_template_tourenportal' );


/* =============================================================== *\ 
   Custom Block-Categories 
\* =============================================================== */ 

/*add_filter( 'block_categories_all', function( $categories, $post ) {
	$my_categories = array_merge(
		array(
			array(
				'slug'  => 'my_slug',
				'title' => 'My Title',
			),
		),
		$categories
	);
	return $my_categories;    
}, 10, 2 );  
*/


/* =============================================================== *\ 
   Block-Variations
\* =============================================================== */ 

function enqueue_block_variations() {
	$url_h0 = get_stylesheet_directory_uri() . '/blocks/block_variations.js';
	wp_enqueue_script('block-variations', $url_h0, array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ));
}
//add_action( 'enqueue_block_editor_assets', 'enqueue_block_variations' );
  
  
  
/* =============================================================== *\ 
   Remove Admin-Menu-Elements
\* =============================================================== */ 
function ud_remove_menus () {
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
add_action('admin_menu', 'ud_remove_menus');

/* =============================================================== *\ 
   Remove Admin-Menu-Bar-Elements
\* =============================================================== */ 
function ud_admin_bar_render() {
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
add_action( 'wp_before_admin_bar_render', 'ud_admin_bar_render' );

/* =============================================================== *\ 
   Admin-Menu custom order
\* =============================================================== */ 
/* r */
function ud_custom_menu_order( $menu_ord ) {
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
//add_filter( 'ud_custom_menu_order', '__return_true' );
//add_filter( 'menu_order', 'ud_custom_menu_order', 10, 1 );
  
    
/* =============================================================== *\ 
   Add Custom Admin Footer
\* =============================================================== */ 

function backend_entwickelt_mit_herz( $text ) {
	return ('<span style="color:black;">Entwickelt mit </span><span style="color: red;font-size:20px;vertical-align:-3px">&hearts;</span><span style="color:black;"</span><span> von <a href="https://ulrich.digital" target="_blank">ulrich.digital</a></span>' );
}
add_filter( 'admin_footer_text', 'backend_entwickelt_mit_herz' );
  

/* =============================================================== *\ 

 	 Users 

\* =============================================================== */ 
/* =============================================================== *\ 
	 Super-Admins 
	 - kann abgefragt werden mit: if(is_my_super_admin() == true)
	 - z.B. um gewisse Seiten zu verstecken 
\* =============================================================== */ 
/*
$my_super_admins = array("2"); // Hier User-ID's eintragen
function is_my_super_admin(){
	global $my_super_admins;	
	$is_super_admin = false;
	foreach($my_super_admins as $my_super_admin):
		if(get_current_user_id()==$my_super_admin):
			$is_super_admin = true;
		else:
			$is_usper_admin = false;
		endif;
	endforeach;
	return($is_super_admin);
}
*/  
  
/* =============================================================== *\ 

 	 Hide in Admin 

\* =============================================================== */ 
  
/* =============================================================== *\ 
 	 Gewisse Seiten im Wordpress-Admin-Bereich ausblenden 
	 - per Kategorie
	 - per Page-Template 
	 - per URL-Titelform (was bei Permalink angegeben werden kann)
\* =============================================================== */ 
/*
$page_template_array = array('archive-touren-archive.php', 'page-aktuell-archive.php', 'page_tourenbericht_erfassen.php');
$url_title_array = array('form-tourenbericht-thank-you');
$cat_slug_array = array('only-for-admin');
$cat_id_array = array(); //kann hier befüllt werden, die cat_slugs werden automatisch hinzugefügt.

foreach($cat_slug_array as $cat):	
	$idObj = get_category_by_slug( $cat );
	if ( $idObj instanceof WP_Term ) {
    	$id = $idObj->term_id;
		array_push($cat_id_array, $id);
	}
endforeach;

if(is_my_super_admin()== false):
	add_filter( 'parse_query', 'exclude_pages_from_admin' );
endif;
function exclude_pages_from_admin($query) {
	global $pagenow, $post_type, $page_template_array, $url_title_array, $cat_slug_array;
	
	$page_IDs_array = array(); //In diesem Array werden dann alle ID's gesammelt
			
  	if (is_admin() && $pagenow=='edit.php' && $post_type =='page') {
		$all_pages = get_pages();
		foreach($all_pages as $page):
			
			// Hide Page per Template 
			foreach($page_template_array as $page_template):
				if($page_template == get_post_meta($page->ID,'_wp_page_template',true) ):
					array_push($page_IDs_array, $page->ID);
				endif;
			endforeach;

			// Hide Page per URL-Titelform
			foreach($url_title_array as $my_url):
				if($my_url == $page->post_name):
					array_push($page_IDs_array, $page->ID);
				endif;
			endforeach;

			// Hide Page per Category
			foreach($cat_slug_array as $cat):
				if(has_category($cat, $page)):
					array_push($page_IDs_array, $page->ID);
				endif;				
			endforeach;
			
		endforeach;
			
		$page_IDs_array = array_unique($page_IDs_array);
	  	//Seiten ausblenden
	  	$query->query_vars['post__not_in'] = $page_IDs_array;
  	}
}
*/

/* =============================================================== *\ 
 	 Category hide 
	 //https://wordpress.org/support/topic/hide-some-categories-in-post-editor/
\* =============================================================== */ 
/*
if(is_my_super_admin()== false):
	add_filter( 'list_terms_exclusions', 'hide_categories_for_specific_user', 10, 2 );
endif;

function hide_categories_for_specific_user( $exclusions, $args ){
	global $cat_id_array;
	$exterms = wp_parse_id_list( $cat_id_array );
   	foreach ( $exterms as $exterm ):
	   	if ( empty($exclusions) ):
		   	$exclusions = ' AND ( t.term_id <> ' . intval($exterm) . ' ';
		else:
    		$exclusions .= ' AND t.term_id <> ' . intval($exterm) . ' ';
		endif;
   endforeach;
   if ( !empty($exclusions) )
       $exclusions .= ')';
   return $exclusions;  
}

// aus Kategorien-Auswahl entfernen
if(is_my_super_admin()== false):
	add_action( 'admin_head-post.php', 'hide_categories_by_css' );
	add_action( 'admin_head-post-new.php', 'hide_categories_by_css' );
endif;

function hide_categories_by_css() { 
	global $cat_id_array;
	$hide_style = "";
	foreach ($cat_id_array as $my_cat_id):
		$hide_style .= "#editor-post-taxonomies-hierarchical-term-" . $my_cat_id . ",";
		$hide_style .= "label[for='editor-post-taxonomies-hierarchical-term-" . $my_cat_id . "']{display:none}";
	endforeach; ?>
	
	<style type="text/css">
		<?php echo $hide_style; ?>
	</style>
	<?php
}
*/

/* =============================================================== *\ 

 	 Customize Editor 

\* =============================================================== */ 

/* =============================================================== *\ 
 	 Admin-Columns anpassen 
	 !! Achtung: Werte müssen als Meta-Keys vorhanden sein !!
	 //https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
\* =============================================================== */ 
/*
add_filter( 'manage_touren_posts_columns', 'touren_columns' );
function touren_columns( $columns ) {
	$columns = array(
		'cb'          => 'cb',
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
*/  

/* =============================================================== *\ 
 	 Custom Post Status 
\* =============================================================== */ 
/*function ud_custom_post_status(){
    register_post_status( 'archiv', array(
        'label'                     => 'Archiv',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Archiv <span class="count">(%s)</span>', 'Archiv <span class="count">(%s)</span>' ),
		'show_in_metabox_dropdown'  => true,
                    'show_in_inline_dropdown'   => true,
                    'dashicon'                  => 'dashicons-businessman',
    ) );
}
add_action( 'init', 'ud_custom_post_status' );


add_filter( 'display_post_states', function( $statuses ) {
    global $post;
	if($post!=NULL):
    	if( $post->post_type == 'touren') {
        	if ( get_query_var( 'post_status' ) != 'archiv' ) { // not for pages with all posts of this status
            	if ( $post->post_status == 'archiv' ) {
                	return array( 'Archiv' );
            	}
        	}
		}
    	return $statuses;
	endif;
});

function ud_add_custom_status_in_quick_edit() {
	echo "<script>
	jQuery(document).ready( function() {
	jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"archiv\">Archiv</option>' );      
	}); 
	</script>";
}
add_action('admin_footer-edit.php','ud_add_custom_status_in_quick_edit');

function ud_add_custom_status_in_post_page() {
	echo "<script>
	jQuery(document).ready( function() {        
	jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"archiv\">Archiv</option>' );
	});
	</script>";
	}
add_action('admin_footer-post.php', 'ud_add_custom_status_in_post_page');
add_action('admin_footer-post-new.php', 'ud_add_custom_status_in_post_page');
*/



/* =============================================================== *\ 

 	 Frontend 

\* =============================================================== */ 
  
/* =============================================================== *\
   Frontend JavaScripts + CSS
\* =============================================================== */

function ud_enqueue_frontend_scripts() {
	//wp_register_style( $handle, $src, $deps, $ver, $media );
	wp_enqueue_style( 'main', get_stylesheet_directory_uri() . "/style.css", [], filemtime( get_stylesheet_directory() . "/style.css" ) );
	wp_enqueue_style('font_awesome', get_stylesheet_directory_uri() . '/webfonts/all.css', [], filemtime( get_stylesheet_directory() . "/style.css" ) );

	/*
	wp_enqueue_style('adobe_fonts', 'https://use.typekit.net/owr0crc.css', [], '1.0', 'all');	
	wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/js/slick.css', [], filemtime( get_stylesheet_directory() . "/js/slick.css" ) );
	wp_enqueue_style('slick-lightbox', get_stylesheet_directory_uri() . '/js/slick-lightbox.css', [], filemtime( get_stylesheet_directory() . "/js/slick-lightbox.css" ) );
    */
	
    //wp_enqueue_script( 'eigener_Name', pfad_zum_js, abhaengigkeit (zb jquery zuerst laden), versionsnummer, bool (true=erst im footer laden) );
	wp_enqueue_script( 'jquery-ui', get_stylesheet_directory_uri().'/js/jquery-ui.min.js', array('jquery'),  filemtime( get_stylesheet_directory() . "/js/jquery-ui.min.js" ), false );
	wp_enqueue_script( 'isotope', get_stylesheet_directory_uri().'/js/isotope.pkgd.min.js', array('jquery'),  filemtime( get_stylesheet_directory() . "/js/isotope.pkgd.min.js" ), true );
	wp_enqueue_script( 'ulrich_digital', get_stylesheet_directory_uri().'/js/ulrich.js', array('jquery'),  filemtime( get_stylesheet_directory() . "/js/ulrich.js" ), true );
}
add_action( 'wp_enqueue_scripts', 'ud_enqueue_frontend_scripts' );


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
function ud_remove_tinymce_emoji($plugins){
	if (!is_array($plugins)){
		return array();
	}
	return array_diff($plugins, array('wpemoji'));
}

function ud_remove_emoji(){
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'ud_remove_tinymce_emoji');
}
add_action('init', 'ud_remove_emoji');  


/* =============================================================== *\
   add schema links
   > header.php
\* =============================================================== */

function ud_schema_type(){
    $schema = 'https://schema.org/';
    if (is_single()) {
        $type = "Article";
    } elseif (is_author()) {
        $type = 'ProfilePage';
    } elseif (is_search()) {
        $type = 'SearchResultsPage';
    } else {
        $type = 'WebPage';
    }
    echo 'itemscope itemtype="' . $schema . $type . '"';
}


/* =============================================================== *\ 
   Add itemprop to menu links 
\* =============================================================== */ 
 
function ud_schema_url($atts){
    $atts['itemprop'] = 'url';
    return $atts;
}
add_filter('nav_menu_link_attributes', 'ud_schema_url', 10);


/* =============================================================== *\ 
   Core Styles unterbinden 
\* =============================================================== */ 
  
remove_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );
remove_filter( 'render_block', 'gutenberg_render_layout_support_flag', 10, 2 );

add_filter( 'render_block', function( $block_content, $block ) {
	return $block_content;
}, 10, 2 );


/* =============================================================== *\
   Add Title-Separator
\* =============================================================== */

function ud_document_title_separator( $sep ) {
    $sep = '|';
    return $sep;
}
add_filter( 'document_title_separator', 'ud_document_title_separator' );


/* =============================================================== *\
   Add ... to title, if necessary
\* =============================================================== */

function ud_title( $title ) {
    if ( $title == '' ) {
        return '...';
    } else {
        return $title;
    }
}
add_filter( 'the_title', 'ud_title' );


/* =============================================================== *\
   Remove automatically P-Tags
\* =============================================================== */

$priority = has_filter( 'the_content', 'wpautop' );
if ( false !== $priority ) {
	remove_filter( 'the_content', 'wpautop', $priority );
}


/* =============================================================== *\
   Add Browser to Body-Class
\* =============================================================== */ 

function browser_body_class($classes) {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
    if($is_lynx) $classes[] = 'lynx';
    elseif($is_gecko) $classes[] = 'gecko';
    elseif($is_opera) $classes[] = 'opera';
    elseif($is_NS4) $classes[] = 'ns4';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE) $classes[] = 'ie';
    else $classes[] = 'unknown';
    if($is_iphone) $classes[] = 'iphone';
    return $classes;
}  
add_filter('body_class','browser_body_class');


/* =============================================================== *\
   Keep the Links in the excerpt
\* =============================================================== */

function ud_keep_the_links_in_excerpt($text) {
	global $post;
	if ( '' == $text ) {
    	$text = get_the_content('');
    	$text = apply_filters('the_content', $text);
    	$text = str_replace('\]\]\>', ']]&gt;', $text);
    	$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
    	$text = strip_tags($text, '<a>');
  	}
  	return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'ud_keep_the_links_in_excerpt');

/* =============================================================== *\ 
	 Breadcrumb - Menu
	 @template archive.php
	 //https://kulturbanause.de/blog/wordpress-breadcrumb-navigation-ohne-plugin/
\* =============================================================== */ 

/* =============================================================== *\ 
 	 
   Comments
    
\* =============================================================== */ 
  
// Load Comment-Reply-Script
function ud_enqueue_comment_reply_script() {
	if ( get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'comment_form_before', 'ud_enqueue_comment_reply_script' );


// Placeholder for Comments 
function ud_placeholder_author_email_url_form_fields($fields) {
    $replace_author = "Name*";
    $replace_email = "Email*";
    $replace_url = "Website";
    
    $fields['author'] = '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'yourdomain' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
	'<input id="author" name="author" type="text" placeholder="'.$replace_author.'" value="' . esc_attr( $commenter['comment_author'] ) . '" size="20"' . $aria_req . ' /></p>';
                    
    $fields['email'] = '<p class="comment-form-email"><label for="email">' . __( 'Email', 'yourdomain' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
	'<input id="email" name="email" type="text" placeholder="'.$replace_email.'" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label for="url">' . __( 'Website', 'yourdomain' ) . '</label>' .
    '<input id="url" name="url" type="text" placeholder="'.$replace_url.'" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';
    
    return $fields;
}
add_filter('comment_form_default_fields','ud_placeholder_author_email_url_form_fields');  


/* =============================================================== *\
   Custom Login-Logo
\* =============================================================== */

function ud_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/BG_logo_rgb.svg);
            padding-bottom: 60px;
            width:320px;
            background-repeat: no-repeat;
 			background-size: 250px auto;
        }
    </style><?php 
}
add_action( 'login_enqueue_scripts', 'ud_login_logo' );

  

/* =============================================================== *\ 

   Plugins

\* =============================================================== */ 
  
  
/* =============================================================== *\ 
   ACF: Add Options Page 
\* =============================================================== */ 
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}


/* =============================================================== *\ 
   Woocommerce: Add Theme Support
   Woocommerce Unterstützung Backend > WooCommerce > Status deklarieren
\* =============================================================== */ 
/*
function setup_woocommerce_support() { 
	add_theme_support('woocommerce'); 
} 
*/
//add_action( 'after_setup_theme', 'setup_woocommerce_support' ); 


