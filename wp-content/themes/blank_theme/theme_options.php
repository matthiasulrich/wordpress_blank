<?php

if(!class_exists("WpSingleImageUploads")) { // dont load class if other theme/plugin has already loaded it
    
    class WpSingleImageUploads {

        const TYPE_THEME = "theme"; 
        const TYPE_PLUGIN = "plugin";
        
        /**
         * Call this static function get an instance. Do not call the constructor directly. 
         * 
         * You must call this in your theme's functions.php file or plugin's main file. 
         * 
         * @param string If are using it in theme then use WpSingleImageUploads::TYPE_THEME If you are using it in plugin then use WpSingleImageUploads::TYPE_PLUGIN
         * @param string The base folder for your theme or plugin. For example if I were using it in Twenty Eleven theme I will provide "twentyeleven"
         * @return WpSingleImageUploads instance 
         */
        public static function getInstance($type, $code) {
            if(!isset(self::$instances[$type . "-" . $code])) {
                self::$instances[$type . "-" . $code] = new WpSingleImageUploads($type, $code);
            }
            return self::$instances[$type . "-" . $code];
        }
        /**
         * Renders the file upload field.
         * 
         * @param string $id The id/name attribute of the main form field. You will receive your url in $_POST[$id]
         * @param string $value Existing url (from saved option)
         * @param bool $showThumb shows thumb if set to true
         * @param bool $name Provide a value if your id is not equal to your name
         * @param bool $class Any class that you need to add to input tag
         */
        public function renderField($id, $value="", $showThumb=false, $name=false, $class=false) {
            if(!$name)
                $name = $id;
            ?>
            <div class="<?php echo $this->code; ?>siu-upload-file-unit" style="display: inline;">
                <input class="regular-text <?php if($class) echo $class; ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="text" value="<?php echo stripslashes(esc_attr($value)); ?>" />
                <input class="siu-upload-file-button" type="button" value="Upload" />
                <?php if($showThumb && $value): ?>
                <img src="<?php echo stripslashes(esc_attr($value)); ?>" alt="" style="width: 100px; height: auto; display: block; border: solid 2px #ccc;" />
                <?php endif; ?>
            </div>
            <?php
        }

        /******** For internal working. You may ignore everything below this line ***************/
        /*
         * NOTE: All the static variables and functions are implemented as static so that more than one theme/plugin can use it in one wordpress installation
         *
         */
        static $instances = array();
        static $dummyPostIds = array(); // we are storing all the dummyPostIds, your theme/plugin adds only one. But there might be other theme or plugin using same class
        var $dummyPostId = 0;
        var $code = false;
        var $type = false;
        var $pluginfile = false;
        public function __construct($type, $code) {
            if(!is_admin()) {
                return;
            }
            $this->type = $type;
            $this->code = $code;
            if($type == self::TYPE_PLUGIN) {
                $bt = debug_backtrace();
                if($bt && is_array($bt) && isset($bt[1])) {
                    $this->pluginfile = $bt[1]["file"];
                }
            }
            $this->init();
        }
        public function init() {

            // attach deactivation hook
            if($this->type == self::TYPE_THEME) {
                add_action("switch_theme", array($this, "_deactivate"));
            }
            else {
                if($this->pluginfile)
                    register_deactivation_hook($this->pluginfile , array($this, "_deactivate"));
            }

            $this->dummyPostId = get_option($this->code . '_siu_dummy_post_id');

            $this->_activate(); // activate is called every time. as it already checks dummyPostId and creates it if not present. so no need for activation hook here

            // render some js in admin head
            add_action("admin_head", array($this, "_adminHead"));
            
            if(count(self::$dummyPostIds) == 0) { // only once
                
                // enqueue necessary js
                add_action('admin_enqueue_scripts', array($this, "_loadJsCss"));
                
                // hide dummy post
                add_action('pre_get_posts', array("WpSingleImageUploads", '_preGetPostFilterToHideMediaHelperPost' ));
                
                // hide dummy post from media library
                if(is_admin()) {
                    $v= $_SERVER['PHP_SELF'];
                    if(stripos($v, 'wp-admin/upload.php') !== FALSE) {
                        add_filter('posts_where', create_function('$where', '
                            global $wpdb;
                            return $where .= " AND {$wpdb->posts}.post_parent NOT IN (" . join(",",WpSingleImageUploads::$dummyPostIds) . ") ";
                        '), 1000);
                        
                        
                    }
                }


            }
            
            self::$dummyPostIds[] = $this->dummyPostId;
        }
        public static function _loadJsCss() {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
        public function _adminHead() {
            ?>
            <script type="text/javascript">
                var <?php echo $this->code; ?>siu_window_send_to_editor_old=false;
                jQuery(document).ready(function($) {
                    if($(".<?php echo $this->code; ?>siu-upload-file-unit").length) {
                    $(".<?php echo $this->code; ?>siu-upload-file-unit").each(function() {
                        var $this = $(this);
                        var input = $this.find('input[type="text"]');
                        var button = $this.find(".siu-upload-file-button");
                        button.click(function() {
                            <?php echo $this->code; ?>siu_window_send_to_editor_old = window.send_to_editor;
                            window.send_to_editor = function(html) {
                                if(jQuery('img',html).length) { 
                                    var imgurl = jQuery('img',html).attr('src');
                                    input.val(imgurl);

                                    var img = $this.find("img");
                                    if(img.length) {
                                        img.attr("src" , imgurl);
                                    }

                                }
                                else if(jQuery(html).attr('href')) {
                                    var aurl = jQuery(html).attr('href');
                                    input.val(aurl);
                                }
                                else {
                                    input.val(html);
                                }
                                tb_remove();
                                window.send_to_editor = <?php echo $this->code; ?>siu_window_send_to_editor_old;
                            }
                            tb_show('Upload Media', 'media-upload.php?type=image&post_id=' + <?php echo $this->dummyPostId; ?> +'&amp;TB_iframe=true');
                        });
                    });
            }});
            </script>
            <?php
        }
        public function _activate() {
            global $wpdb;
            if($this->dummyPostId)
                return;

            $this->dummyPostId = wp_insert_post(array('post_content' => 'This an internal post. Do not publish or delete it.', 'post_title' => '--Internal Post - Do not delete or publish --'));
            update_option($this->code . '_siu_dummy_post_id', $this->dummyPostId);
        }
        public function _deactivate() {
            if(!$this->dummyPostId)
                return;
            wp_delete_post($this->dummyPostId, true);
            delete_option($this->code . '_siu_dummy_post_id');
        }
        public static function _preGetPostFilterToHideMediaHelperPost($wp_query) {
            if( is_admin()) {
               
                $wp_query->set( 'post__not_in', self::$dummyPostIds );
                add_filter('views_edit-post', array("WpSingleImageUploads", '_fixPostCountsToHideMediaHelperPost'));
                add_filter('views_upload', array("WpSingleImageUploads", '_fixPostCountsToHideMediaHelperPost2')); // hide dummy post from media library
            }
        }
        public static function _fixPostCountsToHideMediaHelperPost2($views) {
            global $wpdb, $post_mime_types, $avail_post_mime_types;
//var_dump($views);
            $type_links = array();

            $and = wp_post_mime_type_where( '' );
            $count = $wpdb->get_results( "SELECT post_mime_type, COUNT( * ) AS num_posts FROM $wpdb->posts WHERE post_parent NOT IN (" . join(",",WpSingleImageUploads::$dummyPostIds) . ") AND post_type = 'attachment' AND post_status != 'trash' $and GROUP BY post_mime_type", ARRAY_A );

            $stats = array( );
            foreach( (array) $count as $row ) {
                    $stats[$row['post_mime_type']] = $row['num_posts'];
            }
            $stats['trash'] = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_parent NOT IN (" . join(",",WpSingleImageUploads::$dummyPostIds) . ") AND post_type = 'attachment' AND post_status = 'trash' $and");

            $_num_posts = $stats;
            $_total_posts = array_sum($_num_posts) - $_num_posts['trash'];
            if ( !isset( $total_orphans ) )
                            $total_orphans = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1" );
            $matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
            foreach ( $matches as $type => $reals )
                    foreach ( $reals as $real )
                            $num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];

            $current = stripos($views["all"], "current") !== FALSE;
            $class = ($current) ? ' class="current"' : '';
            $type_links['all'] = "<a href='upload.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $_total_posts, 'uploaded files' ), number_format_i18n( $_total_posts ) ) . '</a>';
            foreach ( $post_mime_types as $mime_type => $label ) {
                    $class = '';

                    if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
                            continue;

                    if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
                            $class = ' class="current"';
                    if ( !empty( $num_posts[$mime_type] ) )
                            $type_links[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
            }
            $current = stripos($views["detached"], "current") !== FALSE;
            $class = ($current) ? ' class="current"' : '';
            $type_links['detached'] = '<a href="upload.php?detached=1"' . $class . '>' . sprintf( _nx( 'Unattached <span class="count">(%s)</span>', 'Unattached <span class="count">(%s)</span>', $total_orphans, 'detached files' ), number_format_i18n( $total_orphans ) ) . '</a>';

            if ( !empty($_num_posts['trash']) )
                    $type_links['trash'] = '<a href="upload.php?status=trash"' . ( (isset($_GET['status']) && $_GET['status'] == 'trash' ) ? ' class="current"' : '') . '>' . sprintf( _nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $_num_posts['trash'], 'uploaded files' ), number_format_i18n( $_num_posts['trash'] ) ) . '</a>';

            return $type_links;
        }
        public static function _fixPostCountsToHideMediaHelperPost($views) {
            unset($views['mine']);
            $types = array(
                array( 'status' =>  NULL ),
                array( 'status' => 'publish' ),
                array( 'status' => 'draft' ),
                array( 'status' => 'pending' ),
                array( 'status' => 'trash' )
            );
            foreach( $types as $type ) {
                $query = array(
                    'post_type'   => 'post',
                    'post_status' => $type['status'],
                    'post__not_in' => self::$dummyPostIds
                );
                $result = new WP_Query($query);
                if( $type['status'] == NULL ):
                    $current = stripos($views["all"], "current") !== FALSE;
                    $class = ($current) ? ' class="current"' : '';
                    $views['all'] = sprintf(__('<a href="%s"'. $class .'>All <span class="count">(%d)</span></a>', 'all'),
                        admin_url('edit.php?post_type=post'),
                        $result->found_posts );
                elseif( $type['status'] == 'draft' ):
                    $current = stripos($views["draft"], "current") !== FALSE;
                    $class = ($current) ? ' class="current"' : '';
                    $views['draft'] = sprintf(__('<a href="%s"'. $class .'>Draft'. ((sizeof($result->posts) > 1) ? "s" : "") .' <span class="count">(%d)</span></a>', 'draft'),
                        admin_url('edit.php?post_status=draft&post_type=post'),
                        $result->found_posts );
                endif;
            }

            return $views;
        }
    }
    
}
?>