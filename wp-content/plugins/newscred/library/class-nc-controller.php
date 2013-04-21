<?php
/**
 *  @package nc-plugin
 *  @author  Md Imranur Rahman <imranur@newscred.com>
 *
 *
 *  Copyright 2012 NewsCred, Inc.  (email : sales@newscred.com)
 *
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

/**
 * NC_Controller Class
 * this class control all the actions
 *  of this plugin
 */

require_once( NC_LIBRARY_PATH . "/class-nc-exception.php" );
require_once( ABSPATH . 'wp-includes/pluggable.php' );


class NC_Controller{

    protected $_template;       // Necessary to generate output
    protected $_params;         // Parameters
    protected $_nc_utility ;

    /**
    * _instance class variable
    *
    * Class instance
    *
    * @var null | object
    **/   
    private static $_instance = NULL;
    
    
    /**
    * get_instance function
    *
    * Return singleton instance
    *
    * @return object
    **/
    static function get_instance() {
        if( self::$_instance === NULL ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
    /**
     *  Constructs the controller and assigns protected variables to be
     * @param array $params
     */
    public function __construct( array $params = array() )
    {   
        global $nc_utility, $nc_category, $nc_author;
        
        $this->_nc_utility = $nc_utility;
        $this->_params = $params;
        $this->_template = new NC_Template();

        /**
        * plugin actions
        */

        /**
         * admin actions
         */

        global $pagenow;


        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        /* Define the custom box */
        add_action( 'add_meta_boxes', array( &$this, 'nc_editors_picks_meta_box' ) );

        /*  admin footer action */

        if( $pagenow == "post.php" || $pagenow == "post-new.php")
            add_action( "admin_footer", array( &$this, "nc_post_footer_actions" ) );

        if( isset($_GET['page']) && $_GET['page'] == "nc-myfeeds-settings-page" )
            add_action( "admin_footer", array( &$this, "nc_myfeeds_footer_actions" ) );


        /*  admin notice action  */
        add_action('admin_notices', array( &$this, 'showNcAdminMessages') );

        add_action( 'http_api_curl', array( &$this, 'nc_curl' ) );

        /**
                 * ajax request actions
                * */

        // search source suggestion
        add_action( 'wp_ajax_ncajax-source-submit', array( &$this, "ncajax_get_sources_suggestion" ) );

        // search topics suggestion
        add_action( 'wp_ajax_ncajax-topic-submit', array( &$this, 'ncajax_get_topics_suggestion' ) );
        // search articles
        add_action( 'wp_ajax_ncajax-metabox-search', array( &$this, 'ncajax_metabox_search' ) );

        // add feature image
        add_action( 'wp_ajax_ncajax-add-image', array( &$this, 'ncajax_add_feature_image' ) );

        // create api call
        add_action( 'wp_ajax_ncajax-create-api-call', array( &$this, 'ncajax_create_api_call' ) );
        // add wp category for my feed
        add_action( 'wp_ajax_ncajax-add-myfeed-category', array( &$this, 'ncajax_create_myfeed_wp_category' ) );

        /*
        // get sources auto suggestions
        add_action( 'wp_ajax_ncajax-get-auto-suggestion-source', array( &$this, 'ncajax_get_auto_suggestion_source' ) );
        // get  topics auto suggestions
        add_action( 'wp_ajax_ncajax-get-auto-suggestion-topic', array( &$this, 'ncajax_get_auto_suggestion_topic' ) );
        */

        // get sources and topics auto suggestions
        add_action( 'wp_ajax_ncajax-get-topics-sources', array( &$this, 'ncajax_get_topics_sources' ) );

        // get article image sets for auto publish
        add_action( 'wp_ajax_ncajax-add-article-image-set', array( &$this, 'ncajax_get_article_image_set' ) );
        // remove the feature image
        add_action( 'wp_ajax_ncajax-remove-feature-image', array( &$this, 'ncajax_remove_feature_image' ) );


//        /**
//         *  save post category
//         */
//        //add_action( 'save_post',  array( &$nc_category, 'add_newscred_category' ) );

        /**
         *  save post author
         */
        add_action( 'save_post',  array( &$nc_author, 'nc_add_post_author' ) );




        /**
         * nc plugin
         * activating actions
         */
        register_activation_hook( NC_PATH . "/newscred-wp.php" , array( &$this, 'nc_plugin_install' ) );

        /**
         *  active when new blog install
         */
        add_action( 'wpmu_new_blog', array( &$this, 'nc_plugin_new_blog_install' ), 10, 6);

    }

    /***
     * load plugin
     */
    public function load() {
        $this->init();
    }

    /**
     * show admin notice message
     */
    function showNcAdminMessages(){
        if(!function_exists('curl_init') && !ini_get('allow_url_fopen') ){
            echo '<div   class="update-nag" style="margin-top: 5px">
                    Please enable the <strong><a href="http://php.net/manual/en/book.curl.php" target="_blank">cURL</a></strong> or <strong><a href="http://php.net/manual/en/filesystem.configuration.php" target="_blank"> allow_url_fopen</a></strong> to work NewsCred plugin properly.
                 </div>';
        }

        if(!extension_loaded("openssl")){
            echo '<div   class="update-nag" style="margin-top: 5px">
                    Please enable the <strong><a href="http://php.net/manual/en/book.openssl.php" target="_blank">OpenSSL</a></strong>  to work NewsCred plugin properly.
                 </div>';
        }
    }

    /**
     * nc_curl
     * @param $handle
     */
    function nc_curl( &$handle ){
        curl_setopt ($handle, CURLOPT_SSL_VERIFYPEER, false);
    }
    /**
     * admin menu functions
     */
    function admin_menu () {

        add_menu_page(
            'Newscred Settings',
            'NewsCred',
            'add_users',
            'nc-settings-page',
            array( $this, 'settings_page' ),
            NC_IMAGES_URL . "/nc-icon.png" , 6
        );
        add_submenu_page(
              'nc-settings-page'
            , 'Articles Settings'
            , 'Articles'
            , 'add_users'
            , 'nc-article-settings-page'
            , array( $this, 'article_settings_page' )
        );

        add_submenu_page(
            'nc-settings-page'
            , 'Images Settings'
            , 'Images'
            , 'add_users'
            , 'nc-image-settings-page'
            , array( $this, 'image_settings_page' )
        );

        add_submenu_page(
            'nc-settings-page'
            , 'MyFeeds Settings'
            , 'MyFeeds'
            , 'add_users'
            , 'nc-myfeeds-settings-page'
            , array( $this, 'myfeeds_settings_page' )
        );

        $this->nc_style();

	}

    /**
     * settings page
     */
	function  settings_page () {
        $this->_nc_utility->loadController( "Settings", "newscred" );
	}
    

    /**
     * article settings page
     */

    function article_settings_page(){
        $this->_nc_utility->loadController( "Settings", "article" );
    }

    /**
     * image settings page
     */

    function image_settings_page(){
        $this->_nc_utility->loadController( "Settings", "image" );
    }

    /**
     * myfeeds_settings_page
     */
    function myfeeds_settings_page(){
        $this->_nc_utility->loadController( "Myfeeds" );
    }

    /* Adds a box to the main column on the Post and Page edit screens */
    function nc_editors_picks_meta_box() {
        $article_settings = get_option( "nc_plugin_article_settings" );
        add_meta_box(
            'ncmeta_sectionid',
            'NewsCred',
            array( $this->_nc_utility, 'loadController' ),
            'post',
            'side',
            'high',
            array( 'controller' => "Metabox" )
        );

        if( isset( $article_settings['custom-post-type'] ) && $article_settings['custom-post-type']){
            $custom_posts = explode(",", $article_settings['custom-post-type']);
            if($custom_posts){
                foreach($custom_posts as $custom_post){
                    add_meta_box(
                        'ncmeta_sectionid',
                        'NewsCred',
                        array( $this->_nc_utility, 'loadController' ),
                        $custom_post,
                        'side',
                        'high',
                        array( 'controller' => "Metabox" )
                    );
                }
            }
        }
    }

    /**
     * load the styles of this plugin
     */
    function nc_style() {

        /**
         *  nc style 
         * */
         
        $id = 'nc_style';
        $file = sprintf( '%s/style.css', NC_CSS_URL );
        wp_register_style( $id, $file );
        wp_enqueue_style( $id );

        
        /**
         *  color box
         * */
         
        $id = 'nc_colorbox';
        $file = sprintf( '%s/colorbox.css', NC_CSS_URL );
        wp_register_style( $id, $file );
        wp_enqueue_style( $id );
        
        
        /**
        *  chosen style
        * */
         
        $id = 'nc_chosen';
        $file = sprintf( '%s/chosen.css', NC_CSS_URL );
        wp_register_style( $id, $file );
        wp_enqueue_style( $id );

        /**
         * gldatepicker style
         */

        /*
        $id = 'nc_gldatepicker';
        $file = sprintf( '%s/gldatepicker.css', NC_CSS_URL );
        wp_register_style( $id, $file );
        wp_enqueue_style( $id );
        */
      

        /**
         * nc-plugin-settings style
         */
//        $id = 'nc_gldatepicker-rasel';
//        $file = sprintf( '%s/nc-plugin-settings.css', NC_CSS_URL );
//        wp_register_style( $id, $file );
//        wp_enqueue_style( $id );


        /**
         * nc-plugin-settings style for  =< wp 3.5
         */
        if(get_bloginfo("version") >= "3.5"){

            $id = 'nc_style.v3.5';
            $file = sprintf( '%s/style.v3.5.css', NC_CSS_URL );
            wp_register_style( $id, $file );
            wp_enqueue_style( $id );
        }



        /**
         * tipped  style
         */
        $id = 'nc-tipped';
        $file = sprintf( '%s/tipped.css', NC_CSS_URL );
        wp_register_style( $id, $file );
        wp_enqueue_style( $id );


        /**
         * ie  style
         */
        wp_enqueue_style(
            'nc-ie',
            NC_CSS_URL . '/nc-ie.css'
        );

        global $wp_styles;
        $wp_styles->add_data( 'nc-ie', 'conditional', 'gte IE 6' );

        if(get_bloginfo("version") >= "3.5"){
            /**
             * ie  style for =< wp 3.5
             */
            wp_enqueue_style(
                'nc-ie.v3.5',
                NC_CSS_URL . '/nc-ie.v3.5.css'
            );

            global $wp_styles;
            $wp_styles->add_data( 'nc-ie.v3.5', 'conditional', 'gte IE 6' );

        }

    }

    /**
     * nc_post_footer_actions
     */


    function nc_post_footer_actions() {

        /**
         * colorbox js
         * */

        wp_enqueue_script(
            'nc-colorbox-js',
            NC_JS_URL. '/colorbox/jquery.colorbox.js',
            array('jquery'), false, true
        );

        /**
         * chosen  script
         * */

        wp_enqueue_script(
            'nc-chosen',
            NC_JS_URL . '/chosen/chosen.jquery.js',
            array('jquery'), false, true
        );

        /**
         * ajax chosen  script
         * */

        wp_enqueue_script(
            'nc-ajax-chosen',
            NC_JS_URL . '/chosen/ajax-chosen.js',
            array('jquery'), false, true
        );


        wp_enqueue_script(
            'nc-excanvas',
            NC_JS_URL . '/excanvas.js',
            array('jquery'), false, true
        );


        wp_enqueue_script(
            'nc-tools',
            NC_JS_URL . '/jquery.tools.min.js',
            array('jquery'), false, true
        );
        wp_enqueue_script(
            'nc-tipped',
            NC_JS_URL . '/tipped.min.js',
            array('jquery'), false, true
        );

        /**
         * nc js  script
         * */

        wp_enqueue_script(
            'nc-script',
            NC_JS_URL . '/post-script.js',
            array(
                'jquery',
                'nc-colorbox-js',
                'nc-chosen',
                'nc-ajax-chosen',
                'nc-excanvas',
                'nc-tools',
                'nc-tipped'
            ), false, true
        );

        wp_localize_script(
            'nc-script',
            'NcAjax',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            )
        );

    }

    /**
     * nc_myfeeds_footer_actions
     */

    function nc_myfeeds_footer_actions(){
        /**
         * colorbox js
         * */

        wp_enqueue_script(
            'nc-colorbox-js',
            NC_JS_URL. '/colorbox/jquery.colorbox.js',
            array('jquery'), false, true
        );
        /**
         * chosen  script
         * */

        wp_enqueue_script(
            'nc-chosen',
            NC_JS_URL . '/chosen/chosen.jquery.js',
            array('jquery'), false, true
        );

        /**
         * ajax chosen  script
         * */

        wp_enqueue_script(
            'nc-ajax-chosen',
            NC_JS_URL . '/chosen/ajax-chosen.js',
            array('jquery'), false, true
        );

        /**
         * nc js  script
         * */

        wp_enqueue_script(
            'nc-script',
            NC_JS_URL . '/myfeeds-script.js',
            array(
                'jquery',
                'nc-colorbox-js',
                'nc-chosen',
                'nc-ajax-chosen'
            ), false, true
        );

        wp_localize_script(
            'nc-script',
            'NcAjax',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            )
        );

    }

    //----------------------------------------
    // ajax calls methods start
    //----------------------------------------

    /**
     * source auto suggestion ajax call
     * ncajax_get_sources_suggestion
     * */
     
    function ncajax_get_sources_suggestion() {
        global $nc_source;
        $nc_source->get_sources_suggestion();
    }
    
    
    /**
     * topic auto suggestion ajax call
     * ncajax_get_topics_suggestion
     * */
    function  ncajax_get_topics_suggestion() {
        global $nc_topic;
        $nc_topic->get_topics_suggestion();
    } 
    
    /**
     * artcile search 
     * */
     
    function ncajax_metabox_search() {
        $this->_nc_utility->loadController( "Metabox", "search" );
    }

    /**
     * ncajax_add_feature_image
     */

    function ncajax_add_feature_image() {
        $this->_nc_utility->loadController( "Metabox", "add_feature_image" );
    }

    /**
     * ncajax_create_api_call
     */
    function ncajax_create_api_call() {
        $this->_nc_utility->loadController( 'Myfeeds', 'createApiCall' );
    }

    /**
     * ncajax_get_article_image_set
     */
    function ncajax_get_article_image_set() {
        $this->_nc_utility->loadController( 'Myfeeds', 'get_image_sets' );
    }

    /**
     * ncajax_remove_feature_image
     */
    function ncajax_remove_feature_image() {
        $this->_nc_utility->loadController( "Metabox", "remove_feature_image" );
    }

    /**
     * ncajax_create_myfeed_wp_category
     */
    function ncajax_create_myfeed_wp_category() {
        $this->_nc_utility->loadController( "Myfeeds", "create_wp_category" );
    }

    /**
     * ncajax_get_topics_sources
     */
    function ncajax_get_topics_sources() {
        $this->_nc_utility->loadController( "Metabox", "get_suggested_topics_source" );
    }

//    /**
//     * ncajax_get_auto_suggestion_topic
//     */
//    function ncajax_get_auto_suggestion_topic() {
//        //$this->_nc_utility->loadController( "Metabox", "get_suggested_topics_source" );
//        global $nc_topic;
//        $nc_topic->get_topics_suggestion();
//    }
//
//    /**
//     * ncajax_get_auto_suggestion_source
//     */
//    function ncajax_get_auto_suggestion_source() {
//        //$this->_nc_utility->loadController( "Metabox", "get_suggested_topics_source" );
//        global $nc_source;
//        $nc_source->get_sources_suggestion();
//    }

    //----------------------------------------
    // ajax calls methods end
    //----------------------------------------


    /**
     * nc_plugin_install
     * install pre request things
     * when plugin activate
     * @param $networkwide
     * @return mixed
     */
    function nc_plugin_install($networkwide) {

        global $wpdb;

        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->create_table();
                }
                switch_to_blog($old_blog);
                return;
            }
        }
        $this->create_table();

    }

    /**
     * @param $blog_id
     * @param $user_id
     * @param $domain
     * @param $path
     * @param $site_id
     * @param $meta
     */

    function nc_plugin_new_blog_install($blog_id, $user_id, $domain, $path, $site_id, $meta){

        // Make sure the user can perform this
        // action and the request came from the correct page.

        switch_to_blog($blog_id);
        $this->create_table();
        restore_current_blog();

    }

    function create_table(){

        add_option( "nc_plugin_access_key", "" );

        // article settings

        $article_settings = array();


        $article_settings['has_images'] = "true";
        $article_settings['fulltext'] = "true";
        $article_settings['article-author'] = "source";
        $article_settings['publish_time'] = "true";
        $article_settings['tags'] = "true";
        $article_settings['categories'] = "true";
        $article_settings['article-author-role'] = "subscriber";
        $article_settings['custom-post-type'] = "";

        add_option( "nc_plugin_article_settings", $article_settings );


        // image settings

        $image_settings = array();

        $image_settings['minheigth'] = 200;
        $image_settings['minwidth'] = 300;

        $image_settings['safe_search'] = "true";
        $image_settings['pagesize'] = 36;


        $image_settings['post_img_width'] = 400;
        $image_settings['post_img_height'] = 300;

        $image_settings['myfeeds_feature_image'] = "true";

        add_option( "nc_plugin_image_settings", $image_settings );


        global $wpdb;

        /**
         * NC Myfeeds  table
         */

        if( !defined( 'DB_CHARSET' ) || !( $db_charset = DB_CHARSET ) )
            $db_charset = 'utf8';

        $db_charset = "CHARACTER SET " . $db_charset;
        if( defined( 'DB_COLLATE' ) && $db_collate = DB_COLLATE )
            $db_collate = "COLLATE " . $db_collate;


        // auto_publish table

        $autopublish_table = $wpdb->prefix . "nc_autopublish";

        if( $wpdb->get_var( "SHOW TABLES LIKE '$autopublish_table'" ) != $autopublish_table ){

            $sql = "CREATE TABLE " . $autopublish_table . " (
                id          bigint(11) NOT NULL AUTO_INCREMENT,
                myfeeds_id       bigint(11) NOT NULL,
                guid       varchar(32) NOT NULL,
                PRIMARY KEY   (id)

                ) {$db_charset} {$db_collate};";

            $results = $wpdb->query( $sql );
        }

        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        if( $wpdb->get_var( "SHOW TABLES LIKE '$myfeeds_table'" ) != $myfeeds_table ){

            $sql = "CREATE TABLE " . $myfeeds_table . " (

                id          bigint(11) NOT NULL AUTO_INCREMENT,
                name       varchar(100) NOT NULL,
                apicall       text,
                autopublish tinyint(1) default 0,
                publish_status tinyint(1),
                publish_interval       varchar(100) NOT NULL,
                myfeed_category text,
                feed_tag tinyint(1) default 1,
                update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                publish_time TIMESTAMP,
                PRIMARY KEY   (id)

                ) {$db_charset} {$db_collate};";

            $results = $wpdb->query( $sql );
        }
//        else{
//            $sql = "SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$myfeeds_table' AND column_name='custom_post_type'";
//            if( $wpdb->get_var( $sql ) == 0 ){
//                $sql = "ALTER TABLE $myfeeds_table ADD custom_post_type text;";
//                $wpdb->query($sql);
//            }
//
//        }
    }
}