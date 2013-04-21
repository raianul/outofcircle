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
 *
 *  NC_Myfeeds
 *  controller class
 *  its handel the myFeeds CRUD opration
 *
 */

class NC_Myfeeds extends NC_Controller {

    private $submit_value;
    private  $message = array();
    private $data;

    /**
     *  init :
     *  its  shows the myfeeds existing list
     *  and also add new feed here
     */
    public function init(){

        global $nc_cache, $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";



        $submit_value = "Save";

        if( isset( $_POST['submit'] ) && $_POST['submit'] == "Save"){
            $this->data = $_POST;
            $this->addMyFeeds();
        }

        if( isset( $_POST['submit'] ) && $_POST['submit'] == "Update"){
            $this->data = $_POST;
            $this->updateMyFeeds();
        }

        if( isset( $_GET['action'] ) && $_GET['action'] == "delete" ){
            $this->deleteMyFeeds();
        }


        if( isset( $_POST['action' ])) {
            $this->bulkDeleteMyFeeds();
        }

        if( isset( $_GET['action'] ) && $_GET['action'] == "edit" && !$_POST['submit'] == "Update"){

            $sql = "select *from $myfeeds_table where id=".$_GET['id'];
            $result = $wpdb->get_row($sql, "ARRAY_A");

            if( $result )
                $this->data = $result;

            $submit_value = "Update";

        }

        $myFeedList = $this ->myFeedList();

        $this->_template->assign("submit_value", $submit_value );
        $this->_template->assign("message", $this->message );
        $this->_template->assign("data", $this->data );
        $this->_template->assign("myFeedList", $myFeedList );


        $this->_template->assign('access_key', get_option("nc_plugin_access_key") );
        $this->_template->assign("sources", $nc_cache->get_nc_sources());
        $this->_template->assign("topics", $nc_cache->get_nc_topics());


        $this->_template->assign('categories', get_categories("hide_empty=0") );
        $this->_template->display('myfeeds/index.php');


    }
    /**
     * createApiCall
     *
     * create a new api call for myfeeds
     */
    function createApiCall(){

        $pagesize = 10;
        $offset = 0;

        $fields = array(
            'article.guid',
            'article.description',
            'article.title',
            'article.published_at',
            'article.source.name',
            'article.tracking_pixel',
            'article.topic.name',
            'article.categories.dashed_name',
            'article.categories.name',
            'article.author.name',

            'article.image.guid',
            'article.image.caption',
            'article.image.description',
            'article.image.height',
            'article.image.width',
            'article.image.published_at',
            'article.image.source.name',
            'article.image.urls.large'

        );

        $options = array(
            'fields'    => $fields,
            'get_topics' => true
        );

        if(! empty($_POST['categories']))
            $options['categories'] = $_POST['categories'];


        if(! empty($_POST['source_guids']))
            $options['sources'] = $_POST['source_guids'];

        if( !empty($_POST['source_filter_name'])){
            $options['source_filter_name'] = $_POST['source_filter_name'];
            $options['source_filter_mode'] = "whitelist";
        }

        if(! empty($_POST['topic_guids']))
            $options['topics'] = $_POST['topic_guids'];

        if( !empty($_POST['topic_filter_name'])){
            $options['topic_filter_name'] = $_POST['topic_filter_name'];
            $options['topic_filter_mode'] = "whitelist";
        }

        if($_POST['has_images'])
            $options['has_images'] = "true";

        if($_POST['fulltext'])
            $options['fulltext'] = "true";



        echo NCpluginArticle::apiCall(NC_ACCESS_KEY, $options);

        exit;
    }
    /**
     * bulkDeleteMyFeeds
     *
     * used for myFeeds bulk delete
     *
     * @return string|void
     */
    function bulkDeleteMyFeeds(){
        global  $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";


        $sql = "DELETE FROM $myfeeds_table WHERE id IN (".implode(', ', $_POST['delete_feeds']).")";

        $result = $wpdb->query($sql);

        if($result = 1){
            $this->message[] = "MyFeeds Deleted Successfully";
            $this->data = "";
        }
        else{
            $this->message[] = "Problem Appear Please Try again later ";
        }

    }
    /**
     * deleteMyFeeds
     *
     * used for myFeeds delete
     *
     */
    function deleteMyFeeds(){

        global  $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        $sql = "Delete from $myfeeds_table where id=".$_GET['id'];

        $result = $wpdb->query( $sql );

        if($result = 1){
            $this->message[] = "MyFeeds Deleted Successfully";
            $this->data = "";
        }
        else{
            $this->message[] = "Problem Appear Please Try again later ";
        }

    }
    /**
     * retrive all myFeeds List
     * myFeedList
     * @return mixed
     */
    function myFeedList(){

        global $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";
        $per_page = 20;

        /**
         * paginations
         */



        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;

        if ( empty($pagenum) ) $pagenum = 1;

        if( ! isset( $per_page ) || $per_page < 0 ) $per_page = 10;

        $num_pages = ceil( $this->get_all_feeds() / $per_page);

        $app_pagin = paginate_links(array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $num_pages,
            'current' => $pagenum
        ));

        $sql = "select id,name,autopublish,publish_interval from $myfeeds_table ";

        if( $pagenum > 0 ) $sql .= " LIMIT ". (($pagenum-1)*$per_page) .", ". $per_page;



        $this->_template->assign( "app_pagin", $app_pagin );
        $this->_template->assign( "pagenum", $pagenum );
        $this->_template->assign( "per_page", $per_page );
        $this->_template->assign( "num_rows", $this->get_all_feeds() );

        $result = $wpdb->get_results($sql);
        if($result)
            return $result;
        else
            return  ;
    }


    /**
     * * get_all_feeds
     * get total number of the feeds
     * @return mixed
     */
    function get_all_feeds(){
        global $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        $sql = "select count(id) as total_row from $myfeeds_table";

        return $wpdb->get_var($sql);


    }

    /**
     * update myFeeds
     * updateMyFeeds
     */
    function updateMyFeeds(){

        global $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        if( !$_POST['name'] )
            $this->message[] = "Please Enter MyFeed Name";

        if( !$_POST['apicall'] )
            $this->message[] = "Please Enter Api Call ";

        if( !$this->message ){

            $id = $name               = htmlspecialchars(trim($_POST['id']));
            $name               = htmlspecialchars(trim($_POST['name']));
            $apicall              = htmlspecialchars(trim($_POST['apicall']));

            $autopublish = ( isset($_POST['autopublish']) ? 1 : 0 );
            $publish_status = $_POST['publish_status'];
            $publish_interval = $_POST['publish_interval'];

            $myfeed_category = "";
            if($_POST['myfeed_category'])
                $myfeed_category = serialize( $_POST['myfeed_category'] );

            $feed_tag = ( isset($_POST['feed_tag']) ? 1 : 0 );

            $result =  $wpdb->update(
                            $myfeeds_table,
                            array(
                                'name' => $name,
                                'apicall' => $apicall,
                                'autopublish' => $autopublish,
                                'publish_status' => $publish_status,
                                'publish_interval' => $publish_interval,
                                'myfeed_category' => $myfeed_category,
                                'feed_tag' => $feed_tag
                            ),
                            array( 'id' => $id ),
                            array(
                                '%s',
                                '%s',
                                '%d',
                                '%d',
                                '%s',
                                '%s',
                                '%d'
                            ),
                            array( '%d' )
                        );

            if($result = 1){
                $this->message[] = "MyFeeds Update Successfully";
                $this->data = "";

                if($autopublish ){
                    wp_clear_scheduled_hook("nc_hourly_plugin_hook");
                    wp_clear_scheduled_hook("nc_mins_plugin_hook");
                }
            }
            else{
                $this->message[] = "Problem Appear Please Try again later ";
            }

        }
    }
    /**
     * add myFeeds
     */
    function addMyFeeds(){

        global $wpdb, $nc_utility;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        if( !$_POST['name'] )
            $this->message[] = "Please Enter MyFeed Name";

        if( !$_POST['apicall'] )
            $this->message[] = "Please Enter Api Call ";

        if( $_POST['apicall'] ){
            $response = $nc_utility->get_url($_POST['apicall']);
            if(!$response)
                $this->message[] = "Please Enter Valid Api Call ";
        }

        if( !$this->message ){

            $name               = htmlspecialchars(trim($_POST['name']));
            $apicall            = html_entity_decode(htmlspecialchars(trim($_POST['apicall'])));

            $fields = array(
                'article.guid',
                'article.description',
                'article.title',
                'article.published_at',
                'article.source.name',
                'article.tracking_pixel',
                'article.topic.name',
                'article.categories.dashed_name',
                'article.categories.name',
                'article.author.name',

                'article.image.guid',
                'article.image.caption',
                'article.image.description',
                'article.image.height',
                'article.image.width',
                'article.image.published_at',
                'article.image.source.name',
                'article.image.urls.large'

            );

            $apicall_url_part = parse_url($apicall);

            parse_str($apicall_url_part['query'],$parameter);
            if($parameter['fields']){
                if($parameter['fields'] != implode(" ", $fields)){
                    $parameter['fields'] = implode(" ", $fields);
                    $new_query_url = http_build_query($parameter);
                    $apicall_url_part['query'] = $new_query_url;
                    $apicall= $this->join_url($apicall_url_part);
                }

            }else{
                $parameter['fields'] = implode(" ", $fields);
                $new_query_url = http_build_query($parameter);
                $apicall_url_part['query'] = $new_query_url;
                $apicall= $this->join_url($apicall_url_part);
            }


            $autopublish = ( isset($_POST['autopublish']) ? 1 : 0 );
            $publish_status = $_POST['publish_status'] ;
            $publish_interval = $_POST['publish_interval'];

            if(!$autopublish)
                $publish_interval = 0;

            $myfeed_category = "";

            if(isset($_POST['myfeed_category']) && $_POST['myfeed_category'] != "")
                $myfeed_category = serialize( $_POST['myfeed_category'] );

            $feed_tag = ( isset($_POST['feed_tag']) ? 1 : 0 );

            $result = $wpdb->insert($myfeeds_table,
                        array('name' => $name,
                            'apicall' => $apicall ,
                            'autopublish' => $autopublish ,
                            'publish_status' =>  $publish_status,
                            'publish_interval' => $publish_interval ,
                            'myfeed_category' => $myfeed_category ,
                            'feed_tag' =>  $feed_tag
                            )
                      );

            if($result){
                $this->message[] = "MyFeeds Add Successfully";
                $this->data = "";

                if($autopublish){
                    wp_clear_scheduled_hook("nc_hourly_plugin_hook");
                    wp_clear_scheduled_hook("nc_mins_plugin_hook");
                }
            }
            else{
                $this->message[] = "Problem Appear Please Try again later ";
            }

        }
    }


    /**
     * get image sets for auto published post
     */
    public function get_image_sets(){

        $post_id = $_POST['post_id'];

        $image_set = get_post_meta($post_id, "nc_image_set");

        if($image_set){

            $this->_template->assign('images', unserialize($image_set[0]) );
            $this->_template->display('myfeeds/image-set.php');

        }
        else
            echo "0";

        exit;


    }

    /**
     * create wp category
     */
    public function create_wp_category(){

        $cat = $_POST['cat'];

        $cat_id = get_cat_ID( $cat );

        if( $cat_id == 0 ){

            $slug = strtolower($cat);
            $new_cat = array('cat_name' => $cat,
                'category_nicename' => $slug
            );

            $my_cat_id = wp_insert_category($new_cat);

            echo $my_cat_id;
        }else
            echo "";

        exit;
    }

    /**
     * @param $parts
     * @param bool $encode
     * @return string
     */
    function join_url( $parts, $encode=TRUE ){
        if ( $encode )
        {
            if ( isset( $parts['user'] ) )
                $parts['user']     = rawurlencode( $parts['user'] );
            if ( isset( $parts['pass'] ) )
                $parts['pass']     = rawurlencode( $parts['pass'] );
            if ( isset( $parts['host'] ) &&
                !preg_match( '!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host'] ) )
                $parts['host']     = rawurlencode( $parts['host'] );
            if ( !empty( $parts['path'] ) )
                $parts['path']     = preg_replace( '!%2F!ui', '/',
                    rawurlencode( $parts['path'] ) );
            if ( isset( $parts['query'] ) )
                $parts['query']    = rawurlencode( $parts['query'] );
            if ( isset( $parts['fragment'] ) )
                $parts['fragment'] = rawurlencode( $parts['fragment'] );
        }

        $url = '';
        if ( !empty( $parts['scheme'] ) )
            $url .= $parts['scheme'] . ':';
        if ( isset( $parts['host'] ) )
        {
            $url .= '//';
            if ( isset( $parts['user'] ) )
            {
                $url .= $parts['user'];
                if ( isset( $parts['pass'] ) )
                    $url .= ':' . $parts['pass'];
                $url .= '@';
            }
            if ( preg_match( '!^[\da-f]*:[\da-f.:]+$!ui', $parts['host'] ) )
                $url .= '[' . $parts['host'] . ']'; // IPv6
            else
                $url .= $parts['host'];             // IPv4 or name
            if ( isset( $parts['port'] ) )
                $url .= ':' . $parts['port'];
            if ( !empty( $parts['path'] ) && $parts['path'][0] != '/' )
                $url .= '/';
        }
        if ( !empty( $parts['path'] ) )
            $url .= $parts['path'];
        if ( isset( $parts['query'] ) )
            $url .= '?' . $parts['query'];
        if ( isset( $parts['fragment'] ) )
            $url .= '#' . $parts['fragment'];
        return urldecode($url);
    }
}

?>