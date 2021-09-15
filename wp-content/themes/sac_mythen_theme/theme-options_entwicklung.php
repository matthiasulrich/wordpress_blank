<?php
// Options mit Submenu
/*add_action('admin_menu', 'add_admin_menu');

function add_admin_menu() {
    add_options_page(__('Test Settings','menu-test'), __('Test Settings','menu-test'), 'manage_options', 'testsettings', 'mt_settings_page');
    add_management_page( __('Test Tools','menu-test'), __('Test Tools','menu-test'), 'manage_options', 'testtools', 'mt_tools_page');
    add_menu_page(__('Test Toplevel','menu-test'), __('Test Toplevel','menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );
    add_submenu_page('mt-top-level-handle', __('Test Sublevel','menu-test'), __('Test Sublevel','menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');
    add_submenu_page('mt-top-level-handle', __('Test Sublevel 2','menu-test'), __('Test Sublevel 2','menu-test'), 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}

function mt_settings_page() {
    echo "<h2>" . __( 'Test Settings', 'menu-test' ) . "</h2>";
}

function mt_tools_page() {
    echo "<h2>" . __( 'Test Tools', 'menu-test' ) . "</h2>";
}

function mt_toplevel_page() {
	$options_url =  'my_options.php';
echo $options_url;
//echo get_theme_root(); 
echo get_theme_root_uri();
	include($options_url);
    echo "<h2>" . __( 'Test Toplevel', 'menu-test' ) . "</h2>";
}

function mt_sublevel_page() {
    echo "<h2>" . __( 'Test Sublevel', 'menu-test' ) . "</h2>";
}

function mt_sublevel_page2() {
    echo "<h2>" . __( 'Test Sublevel2', 'menu-test' ) . "</h2>";
}
*/
/**
 * Plugin Name: 	Media Selector test plugin
 * Plugin URI:		http://jeroensormani.com/
 * Description:		Add a media selector to your settings page.
 * Version: 		0.0.1
 * Author: 			Jeroen Sormani
 * Author URI: 		http://www.jeroensormani.com/
 */

add_action( 'admin_menu', 'register_media_selector_settings_page' );

function register_media_selector_settings_page() {
	add_submenu_page( 
        'options-general.php', 
        'Media Selector', 
        'Media Selector', 
        'manage_options', 
        'media-selector', 
        'media_selector_settings_page_callback' );
}

function media_selector_settings_page_callback() {

	// Save attachment ID
	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
		update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
	endif;

	wp_enqueue_media();

	?><form method='post'>
		<div class='image-preview-wrapper'>
			<img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'media_selector_attachment_id' ) ); ?>' height='100'>
		</div>
		<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
		<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'>
		<input type="submit" name="submit_image_selector" value="Save" class="button-primary">
	</form><?php

}






 // add the admin options page
add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
add_options_page('Custom Plugin Page', 'Custom Plugin Menu', 'manage_options', 'plugin', 'plugin_options_page');
}

 // display the admin options page
function plugin_options_page() {
?>
<div>
<h2>My custom plugin</h2>
Options relating to the Custom Plugin.
<form action="options.php" method="post">
<?php settings_fields('plugin_options'); ?>
<?php do_settings_sections('plugin'); ?>
 
<input name="Submit" type="submit" value="Save Changes" />
</form></div>
 
<?php
}
?>
<?php // add the admin settings and such
add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){

register_setting( 
    'plugin_options', //option-group
    'plugin_options', // option-name
    'plugin_options_validate' //args
);

add_settings_section(
    'plugin_main', 
    'Main Settings', 
    'plugin_section_text', 
    'plugin');

add_settings_section(
    'plugin_second', 
    'Second Settings', 
    'plugin_section_image', 
    'plugin');

add_settings_field(
    'plugin_text_string', 
    'Plugin Text Input', 
    'plugin_setting_string', 
    'plugin', 
    'plugin_main'
);
add_settings_field(
    'my_options_field_image', //id
    'Logo', //title
    'field_image_cb2', //callback
    'plugin', //page
    'plugin_main', // section
    array( //args
        'label_for'         => 'my_options_field_image',
        'class'             => 'my_options_row',
    )
);
}?>



<?php function plugin_section_text() {
echo '<p>Main description of this section here.</p>';
} ?>

<?php function plugin_section_image() {
echo '<p>image here.</p>';
} ?>

<?php function plugin_setting_string() {
    $options = get_option('plugin_options');
    //var_dump($options);
    echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
} 
?>

<?php function field_image_cb2(){
    $options = get_option('plugin_options');
    //var_dump($options);    



    // Save attachment ID
	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
    		update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
	endif;

	wp_enqueue_media();
/*
	?><form method='post'>
		<div class='image-preview-wrapper'>
			<img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'media_selector_attachment_id' ) ); ?>' height='100'>
		</div>
		<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
		<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'>
		<input type="submit" name="submit_image_selector" value="Save" class="button-primary">
	</form><?php

*/

}?>


<?php // validate our options
function plugin_options_validate($input) {
    $options = get_option('plugin_options');
    $options['text_string'] = trim($input['text_string']);
    if(!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
        //$options['text_string'] = '';
    }
    return $options;
}
?>

<?php 
/* =============================================================== *\ 
 	Init
    - add section
    - add fields 
\* =============================================================== */ 
function my_options_settings_init() {
    register_setting( 'my_options', //group, must be the same 
    'ulrich_digital_options' ); // Register a new setting for "my_options" page.



//The first argument is a group, which needs to be the same as what you used in the settings_fields function call. The second argument is the name of the options. If we were doing more than one, we’d have to call this over and over for each separate setting. The final arguement is a function name that will validate your options. Basically perform checking on them, to make sure they make sense.
    add_settings_section( // Register a new section in the "my_options" page.
        'section_bild', //id
        'Bild', //title
        'section_1_callback', //callback
        'my_options' //page
    );
    
    add_settings_section( // Register a new section in the "my_options" page.
        'section_texte', //id
        'Texte', //title
        'section_2_callback', //callback
        'my_options' //page
    );
 
//add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() )
    add_settings_field(
        'my_options_field_image', //id
        'Logo', //title
        'field_image_cb', //callback
        'my_options', //page
        'section_bild', //section
        array(
            'label_for'         => 'my_options_field_image',
            'class'             => 'my_options_row',
        )
    );
    
    add_settings_field( // Register a new field in the "section_1" section, inside the "my_options" page.
        'my_options_field_pill', 
        'Pill',
        'field_texte_cb',
        'my_options',
        'section_texte',
        array(
            'label_for'         => 'my_options_field_pill',
            'class'             => 'my_options_row',
            'my_options_custom_data' => 'custom',
        )
    );

    add_settings_field( // Register a new field in the "section_1" section, inside the "my_options" page.
        'my_options_field_pillb', 
        'Pillb',
        'field_texte_cb',
        'my_options',
        'section_texte',
        array(
            'label_for'         => 'my_options_field_pillB',
            'class'             => 'my_options_row',
            'my_options_custom_data' => 'custom',
        )
    );

    add_settings_field( // Register a new field in the "section_1" section, inside the "my_options" page.
        'my_options_field_pill3', 
        'Pill3',
        'field_texte_cb',
        'my_options',
        'section_texte',
        array(
            'label_for'         => 'my_options_field_pill3',
            'class'             => 'my_options_row',
            'my_options_custom_data' => 'custom',
        )
    );




}
add_action( 'admin_init', 'my_options_settings_init' );

function section_1_callback( $args ) { /*?>
   <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo 'Follow the white rabbit.'; ?></p>
   <?php
*/
}
function section_2_callback( $args ) { /*?>
   <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo 'Follow the white rabbit.'; ?></p>
   <?php
*/
}


function field_image_cb(){ //jedes einzelne Bild
    
    ?>
    <h1>
      Media Selector with Image and URL examples
    </h1>
    <?php
      
    	// Save attachment ID
    	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) && isset( $_POST['image_attachment_id'] ) ) :
    		update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
    		update_option( 'media_selector_url_attachment_id', absint( $_POST['url_link_id'] ) );
        echo '<div style="color:#ff0000; font-weight:bold;">Settings Updated</div>';
    	endif;
    	wp_enqueue_media();
    	?><form method='post'>
    		<div class='image-preview-wrapper'>
    			<img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'media_selector_attachment_id' ) ); ?>' height='200'>
    		</div>
    		<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
    		<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'><br>
      <br>
     <div class="url-preview">
    			<?php
    			echo wp_get_attachment_url( get_option( 'media_selector_url_attachment_id' ) );
    			?>
    		</div>
    		<input id="upload_url_button" type="button" class="button" value="<?php _e( 'Update url' ); ?>" />
    		<input type='hidden' name='url_link_id' id='url_link_id' value='<?php echo get_option( 'media_selector_url_attachment_id' ); ?>'>
    		<br><br> 
      
    		<input type="submit" name="submit_image_selector" value="Save" class="button-primary">
    	</form><?php
}



function field_texte_cb( $args ) { // jedes einzelne Pulldown
   // Get the value of the setting we've registered with register_setting()
   $options = get_option( 'ulrich_digital_options' ); ?>
  <?php 
   var_dump($options);
?>
   <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
	   data-custom="<?php echo esc_attr( $args['my_options_custom_data'] ); ?>"
	   name="ulrich_digital_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
	   
	   <option value="red" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>>
		   <?php esc_html_e( 'red pill', 'my_options' ); ?>
	   </option>

	   <option value="blue" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'blue', false ) ) : ( '' ); ?>>
		   <?php esc_html_e( 'blue pill', 'my_options' ); ?>
	   </option>
   </select>
   
   
   
   
   <p class="description">
	   <?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'my_options' ); ?>
   </p>
   <p class="description">
	   <?php esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'my_options' ); ?>
   </p>
   <?php
}

/* =============================================================== *\ 
    Add the top level menu page. 
\* =============================================================== */ 
function ulrich_digital_options_page() {
   add_menu_page(
	   'Theme-Options', // Page-Title
	   'Theme-Options', // Menu-Title
	   'manage_options', //Berechtigung
	   'my_options',
	   'ulrich_digital_options_page_html'
   );
}
add_action( 'admin_menu', 'ulrich_digital_options_page' );

/* =============================================================== *\ 
 	Top level menu callback function 
\* =============================================================== */ 
function ulrich_digital_options_page_html() {
   // check user capabilities
   if ( ! current_user_can( 'manage_options' ) ) {
	   return;
   }

   // add error/update messages

   // check if the user have submitted the settings
   // WordPress will add the "settings-updated" $_GET parameter to the url
   if ( isset( $_GET['settings-updated'] ) ) {
	   // add settings saved message with the class of "updated"
	   add_settings_error( 'my_options_messages', 'my_options_message', __( 'Settings Saved', 'my_options' ), 'updated' );
   }

   // show error/update messages
   settings_errors( 'my_options_messages' );
   ?>
   <div class="wrap">
	   <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	   <form action="options.php" method="post">
		   <?php
		   // output security fields for the registered setting "my_options"
		   settings_fields( 'my_options' );
		   // output setting sections and their fields
		   // (sections are registered for "my_options", each field is registered to a specific section)
		   do_settings_sections( 'my_options' );
		   // output save settings button
		   submit_button( 'Save Settings' );
		   ?>
	   </form>
   </div>
   <?php
}




























/* =============================================================== *\ 
 	 Footer-Script 
\* =============================================================== */ 
  

add_action( 'admin_footer', 'media_selector_print_scripts' );

function media_selector_print_scripts() {

    $my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );
	$my_saved_url_attachment_post_id = get_option( 'media_selector_url_attachment_id', 0 );
    
	?><script type='text/javascript'>
    jQuery( document ).ready( function( $ ) {
        // Uploading files
    
  
  //media popup for an image
  var file_frame;
        var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
        var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
        jQuery('#upload_image_button').on('click', function( event ){
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                // Set the post ID to what we want
                file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                // Open frame
                file_frame.open();
                return;
            } else {
                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                wp.media.model.settings.post.id = set_to_post_id;
            }
            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select a image to upload',
                button: {
                    text: 'Use this image',
                },
                multiple: false	// Set to true to allow multiple files to be selected
            });
            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
                $( '#image_attachment_id' ).val( attachment.id );
                // Restore the main post ID
                wp.media.model.settings.post.id = wp_media_post_id;
            });
                // Finally, open the modal
                file_frame.open();
        });
    
  //Media popup for selecting just the URL
  var file_frame2;
        var url_set_to_post_id = <?php echo $my_saved_url_attachment_post_id; ?>; // Set this
    var wp_media_post_id2 = wp.media.model.settings.post.id; // Store the old id
  jQuery('#upload_url_button').on('click', function( event ){
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if ( file_frame2 ) {
                // Set the post ID to what we want
                file_frame2.uploader.uploader.param( 'post_id', url_set_to_post_id );
                // Open frame
                file_frame2.open();
                return;
            } else {
                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                wp.media.model.settings.post.id = url_set_to_post_id;
            }
            // Create the media frame.
            file_frame2 = wp.media.frames.file_frame = wp.media({
                title: 'Select a file to update url',
                button: {
                    text: 'Use this file',
                },
                multiple: false	// Set to true to allow multiple files to be selected
            });
            // When an image is selected, run a callback.
            file_frame2.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame2.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                $( '.url-preview' ).html( attachment.url ).css( 'width', 'auto' );
                $( '#url_link_id' ).val( attachment.id );
                // Restore the main post ID
                wp.media.model.settings.post.id = wp_media_post_id;
            });
                // Finally, open the modal
                file_frame2.open();
        });
        // Restore the main ID when the add media button is pressed
        jQuery( 'a.add_media' ).on( 'click', function() {
            wp.media.model.settings.post.id = wp_media_post_id;
        });
    });
</script><?php

}
?>
