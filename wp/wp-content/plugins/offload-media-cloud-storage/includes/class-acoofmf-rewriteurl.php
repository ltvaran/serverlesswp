<?php

/**
 * Manage URL Rewrite Compatibility here
 *
 * @class   ACOOFMF_REWRITEURL
 * 
 * 
 */

if (!defined('ABSPATH')) {
    exit;
}

//

class ACOOFMF_REWRITEURL {

     /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $instance = null;
    
    /**
     * The version number.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $version;

    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $token;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $file;

    /**
     * The main plugin directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $dir;

    /**
     * The plugin assets directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public static $assets_dir;

    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */

    public static $assets_url;
 
    /**
     * The plugin hook suffix.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public static $hook_suffix = array();

    /**
     * The meta stored urls
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public static $stored_items = [];

    /**
     * Service
     *
     * @var     string
     * @access  protected
     * @since   1.0.0
     */
    protected static $service='';


    /**
     * The plugin Settings.
     *
     * @var     array
     * @access  protected
     * @since   1.0.0
     */
    protected static $settings;
  

    /**
     * Id of the page
     *
     * @var     int
     * @access  public
     * @since   1.0.0
     */
    public static $pageID = 0;

    /**
     * Timestamp That need to reset cache data
     *
     * @var     int
     * @access  public
     * @since   1.0.0
     */
    public static $dataExpiryVersion = 0;
  

    /**
     * Constructor function.
     *
     * @access  public
     * @param string $file plugin start file path.
     * @since   1.0.0
     */
    public function __construct($file = ''){
        self::$version              = ACOOFM_VERSION;
        self::$token                = ACOOFM_TOKEN;
        self::$file                 = $file;
        self::$dir                  = dirname(self::$file);
        self::$assets_dir           = trailingslashit(self::$dir) . 'assets';
        self::$assets_url           = esc_url(trailingslashit(plugins_url('/assets/', self::$file)));
        self::$settings             = acoofm_get_option('settings', []);
        self::$service              = acoofm_get_service('slug');
        self::$dataExpiryVersion    = get_option( ACOOFM_STORED_DATA_VERSION, 0 );

        if ( 
            isset(self::$settings['rewrite_url']) && 
            self::$settings['rewrite_url']  && 
            acoofm_is_service_enabled()
        ) {
            self::start_buffering();
        }
    }

    /**
     * start output buffering
     *
     * @since  1.0.0
     * 
     */

    private static function start_buffering() {
        ob_start( 'self::end_buffering' );
    }


    /**
     * end output buffering and rewrite contents if applicable
     *
     * @since   1.0.0
     *
     * @param   string   $contents - contents from the output buffer
     * @param   integer  $phase - bitmask of PHP_OUTPUT_HANDLER_* constants
     * @return  string   $contents|$rewritten_contents  rewritten contents from the output buffer else unchanged
     */

    private static function end_buffering( $contents, $phase ) {

        if ( $phase & PHP_OUTPUT_HANDLER_FINAL || $phase & PHP_OUTPUT_HANDLER_END ) {
            if ( ! self::bypass_rewrite() ) {

                wp_reset_query();
                global $post;
                self::$pageID = ($post) ? $post->ID : 0; 

                $rewritten_contents = self::rewriter( $contents );

                return $rewritten_contents;
            }
        }

        return $contents;
    }


    /**
     * Rewrite contents
     *
     * @since   1.0.0
     *
     * @param   string  $contents - contents to parse
     * @return  string  $contents|$rewritten_contents  rewritten contents if applicable else unchange
     */

    public static function rewriter( $contents ) {

        // check rewrite requirements
        if ( ! is_string( $contents ) || !(isset(self::$settings['rewrite_url']) && self::$settings['rewrite_url']) ) {
            return $contents;
        }

        global $acoofmItem;
        $filtered_urls     = array();
        $filtered_files    = array();
        $upload_dir        = wp_get_upload_dir();

        $contents = apply_filters( 'acoofm_contents_before_rewrite', $contents );

        self::$stored_items = self::get_pool();

        $urls_regex = '/(http|https)?:?\/\/[^"\'\s<>()\\\]*/';

        // Match all Media URLS that has extension
        preg_match_all($urls_regex, $contents, $matches);
        if(!empty($matches)) {
            $urls = $matches[0];
            if(isset($urls) && !empty($urls)) {
                foreach($urls as $file_url) {
                    $parsed_url = parse_url($file_url);
                    if(isset($parsed_url['path']) && !empty($parsed_url['path'])) {
                        $path_info = pathinfo($parsed_url['path']);
                        $new_url = acoofm_create_no_query_url($parsed_url);
                        if(
                            isset($path_info['extension']) && !empty($path_info['extension']) &&
                            isset($path_info['filename']) && !empty($path_info['extension'])
                         ) {
                            if(!(isset(self::$stored_items[$new_url]) && !empty(self::$stored_items[$new_url]))) {
                                $filtered_urls[]    = $new_url;
                                $path_info['url']   = $new_url;
                                $filtered_files[]   = $path_info;
                            } 
                        }
                    } 
                }
            }
        }

        //Get all database items That matches the Urls
        $matchings_items = $acoofmItem->get_images_by_files($filtered_files);

        $new_content = preg_replace_callback( $urls_regex, 
        function($pattern_match) use ($filtered_urls, $matchings_items, $upload_dir)  {
            global $acoofmItem;
            $found          = false;
            $size           = 'full';
            $item_id        = 0;
            $found_array    = array();
            
            $file_url   = $pattern_match[0]; // Get first URL From content that matches regex
            if(isset($file_url) && !empty($file_url)) {
                if(in_array($file_url, $filtered_urls)) { // Check the URL that exist in th e filtered URL
                    $parsed_url = parse_url($file_url);
                    if(isset($parsed_url['path']) && !empty($parsed_url['path'])) {
                        $path_info = pathinfo($parsed_url['path']);
                        $new_url = acoofm_create_no_query_url($parsed_url); // Create no query URL
                        if(isset($path_info['extension']) && !empty($path_info['extension'])) {
                            if(isset(self::$stored_items[$new_url]) && !empty(self::$stored_items[$new_url])){  // Check whether it is already saved in cache
                                $cached_item = self::$stored_items[$new_url];
                                return (
                                        $cached_item['found'] == true && 
                                        isset($cached_item['url']) && 
                                        !empty($cached_item['url'])
                                    ) 
                                        ? $cached_item['url'] 
                                        : $file_url;
                            } else if(isset($matchings_items) && !empty($matchings_items)) {
                                foreach($matchings_items as $id=>$item) {
                                    if(isset($item['url'])) {
                                        $source_url = $item['url'];
                                        if($source_url == $new_url) {
                                            $found      = true;
                                            $item_id    = $id;
                                        } 
                                        
                                        if(substr($source_url, strlen($source_url)-strlen($path_info['basename']), strlen($path_info['basename'])) == $path_info['basename']) {
                                            $found_array[] = array( 'item_id' => $id, 'matched' => $source_url, 'size' => 'full' );
                                        } 
                                        
                                        if(
                                            isset($item['url']) && 
                                            substr($item['url'], strlen($item['url'])-strlen($path_info['basename']), strlen($path_info['basename'])) == $path_info['basename']
                                        ) {
                                            $found_array[] = array( 'item_id' => $id, 'matched' => $item['url'], 'size' => 'full' );
                                        }
                                    }

                                    if(isset($item['sizes']) && !empty($item['sizes'])) {
                                        foreach($item['sizes'] as $s=>$sub_size) {
                                            if(isset($sub_size['url'])){
                                                $sub_source_url = $sub_size['url'];
                                                if($sub_source_url == $new_url) {
                                                    $found      = true;
                                                    $item_id    = $id;
                                                    $size       = $s;
                                                    break;
                                                } 
                                                
                                                if(substr($sub_source_url, strlen($sub_source_url)-strlen($path_info['basename']), strlen($path_info['basename'])) == $path_info['basename']) {
                                                    $found_array[] = array( 'item_id' => $id, 'matched' => $sub_source_url, 'size' => $s );
                                                }  
                                                
                                                if(
                                                    isset($sub_size['url']) && 
                                                    substr($sub_size['url'], strlen($sub_size['url'])-strlen($path_info['basename']), strlen($path_info['basename'])) == $path_info['basename']
                                                ) {
                                                    $found_array[] = array( 'item_id' => $id, 'matched' => $sub_size['url'], 'size' => $s );
                                                }
                                            }
                                        }
                                        if($found) {
                                            break;
                                        }
                                    }
                                }


                                if(isset($found_array) && !empty($found_array) && !$found) {
                                    foreach($found_array as $f_item) {
                                        // 7 Means length of "YYYY/MM" directory
                                        $possibly_date = substr($path_info['dirname'], strlen($path_info['dirname'])-7, 7);
                                        if(preg_match('/^([1-3][0-9]{3})\/(0[1-9]|1[0-2])$/', $possibly_date)) {
                                            $dated_file_name = $possibly_date.'/'.$path_info['basename'];
                                            if(
                                                substr($f_item['matched'], strlen($f_item['matched'])-strlen($dated_file_name), strlen($dated_file_name)) == $dated_file_name
                                            ) {
                                                $found      = true;
                                                $item_id    = $f_item['item_id'];
                                                $size       = $f_item['size'];
                                                break;
                                            }
                                        } else {
                                            $parsed_new_url         = parse_url($new_url);
                                            $parsed_f_item_url      = parse_url($f_item['matched']);
                                            $relative_path          = isset($parsed_new_url['path']) ? $parsed_new_url['path'] : '';
                                            $relative_path_f_item   = isset($parsed_f_item_url['path']) ? $parsed_f_item_url['path'] : '';
                                            if($relative_path == $relative_path_f_item) {
                                                $found      = true;
                                                $item_id    = $f_item['item_id'];
                                                $size       = $f_item['size'];
                                                break;
                                            }
                                        }
                                    }
                                }

                                if($found) {
                                    $url = ($size == 'full') 
                                                ? $acoofmItem->get_url((int)$item_id) 
                                                : $acoofmItem->get_thumbnail_url((int)$item_id, $size);
                                        
                                    if($url) {
                                        self::$stored_items[$new_url] = array('url'=>$url, 'found' => true);
                                    }
                                    return $url ? $url : $file_url;
                                } 
                            }
                        }
                        self::$stored_items[$new_url] = array('url'=>'', 'found' => false);
                    }
                }
            }
            return $file_url;
        }, $contents );

        self::set_pool(self::$stored_items);

        $rewritten_contents = apply_filters( 'acoofm_contents_after_rewrite', $new_content);
        return $rewritten_contents;
    }


    /**
     * Checking rewrite should be bypassed
     *
     * @since   1.0.0
     * @return  boolean  true if rewrite should be bypassed else false
     */

    private static function bypass_rewrite() {

        // bypass rewrite hook
        if ( apply_filters( 'acoofm_bypass_url_rewrite', false ) ) {
            return true;
        }

        // check request method
        if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
            return true;
        }

        $is_admin = apply_filters( 'acoofm_exclude_admin', is_admin() );

        // check conditional tags
        if ( $is_admin || is_trackback() || is_robots() || is_preview() ) {
            return true;
        }

        return false;
    }



    /**
     * Set Frontend Items in Cache
     * @since 1.0.0
     * @return boolean
     */
    private static function set_pool($data){
        if(!empty($data)){
            $updated_data = ['data' => $data, 'version' => self::$dataExpiryVersion];
            if(self::$pageID === 0) {
                update_option( ACOOFM_ITEM_POOL_OPTION_KEY, $updated_data );
            } else {
                update_post_meta( self::$pageID, ACOOFM_ITEM_POOL_META_KEY, $updated_data );
            }
        }
    }
    
    
    
    /**
     * Get Frontend Items from Cache
     * @since 1.0.0
     * @return boolean
     */
    private static function get_pool(){
        $expired    = false;
        $data       = [];
        if(self::$pageID === 0) {
            $data = get_option( ACOOFM_ITEM_POOL_OPTION_KEY, [] );
        } else {
            $meta_data =  acoofm_get_post_meta( self::$pageID, ACOOFM_ITEM_POOL_META_KEY, true );
            $data = $meta_data ? $meta_data : [];
        }

        if(!empty($data) && isset($data['version']) && isset($data['data'])) {
            return ( (int)self::$dataExpiryVersion != (int)$data['version'] ) 
                        ? false 
                        :  $data['data'];                        
        } else {
            return false;
        }
    }
    

    /**
     * Ensures only one instance of ACOOFMF_FRONTEND is loaded or can be loaded.
     *
     * @param string $file Plugin root file path.
     * @return Main ACOOFMF_FRONTEND instance
     * @since 1.0.0
     * @static
     */
    public static function instance($file = '')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }

}