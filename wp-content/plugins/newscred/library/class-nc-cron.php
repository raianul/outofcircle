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
 * NC_Cron Class
 * its a local file cache for newscred source and topics
 *
 */


class NC_Cron {

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
     * Constructs the controller and assigns protected variables to be
     * used by extenders of the abstract class.
     */
    public function __construct() {

        // add new schedule interval
        add_action( "cron_schedules", array( &$this, "add_nc_scheduled_interval" ) );

        /**
         *  check the schedule
         */

        if ( !wp_next_scheduled( 'nc_hourly_plugin_hook' ) ) {
            wp_schedule_event( time(), 'nc_minutes_60', 'nc_hourly_plugin_hook' );

        }

        if ( !wp_next_scheduled( 'nc_mins_plugin_hook' ) ) {
            wp_schedule_event( time(), 'nc_minutes_5', 'nc_mins_plugin_hook' );

        }

        /**
         *  cron actions
         */

        // add my hourly schedule hook
        add_action( 'nc_hourly_plugin_hook', array( &$this, "nc_plugin_hourly_cron_action" ) );

        // add my 5 mins schedule hook
        // post articles for myFeeds
        add_action( 'nc_mins_plugin_hook', array( &$this, "nc_plugin_mins_cron_action" ) );

        // add feature image for myFeeds
        add_action( 'nc_mins_plugin_hook', array( &$this, "nc_plugin_mins_myfeeds_feature_image_action" ) );

    }

    /**
     * add new schedule time for nc plugin
     * @param $schedules
     * @return array
     */
    public  function add_nc_scheduled_interval( $schedules ) {

        $schedules['nc_minutes_60'] = array( 'interval'=>3600, 'display'=>'Once 60 minutes' );
        $schedules['nc_minutes_5'] = array( 'interval'=>300, 'display'=>'Once 5 minutes' );

        return $schedules;

    }

    /**
     * nc_plugin_hourly_cron_action
     * in every hour it check
     * the time interval of myFeeds
     * if the time interval valid then it will insert
     * article guids in the wp_nc_autopublish table
     */

    public  function nc_plugin_hourly_cron_action() {

        set_time_limit( 5000 );

        global $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        $sql = "select id, apicall, publish_interval, publish_time from $myfeeds_table where autopublish = 1";
        $results = $wpdb->get_results( $sql );

        if( $results ) {
            foreach( $results as $myfeed ){

                /**
                 *  update myfeeds for first time
                 */
                if( $myfeed->publish_time == "0000-00-00 00:00:00" ){

                    if( $myfeed->apicall && filter_var( $myfeed->apicall, FILTER_VALIDATE_URL ) ) {

                        $parse_url = parse_url( $myfeed->apicall );

                        parse_str( html_entity_decode( $parse_url['query']), $querys );

                        $querys['fields'] = "article.guid";

                        $query_str = http_build_query( $querys );

                        $url = $parse_url['scheme'] . "://" . $parse_url['host'] . $parse_url['path'] . "?" . $query_str;

                        self::insertArticleGuid( $url, $myfeed->id );

                    }

                }else{

                    $current_time = date( "Y-m-d H:i:s", time() );
                    $publish_time = $myfeed->publish_time;

                    $difference =  self::timeDiff( $current_time, $publish_time );

                    if( $difference > $myfeed->publish_interval ) {

                        $from_date = date( 'Y-m-d H:i:s', strtotime( " -$difference minute" ) );

                        $parse_url = parse_url( $myfeed->apicall );

                        parse_str( html_entity_decode( $parse_url['query'] ), $querys );

                        $querys['fields'] = "article.guid";
                        $querys['from_date'] = $from_date;

                        $query_str = http_build_query( $querys );

                        $url = $parse_url['scheme'] . "://" . $parse_url['host'] . $parse_url['path'] . "?" . $query_str;

                        self::insertArticleGuid( $url, $myfeed->id );

                    }
                }
            }
        }

    }

    /**
     * nc_plugin_mins_cron_action
     * published articles in every 5 mins interval
     * if wp_nc_autopublish table has article guids
     */
    public function nc_plugin_mins_cron_action() {

        set_time_limit( 300 );
        global $wpdb;
        $nc_autopublish_table = $wpdb->prefix . "nc_autopublish";
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        $image_settings = get_option( "nc_plugin_image_settings" );

        $myfeeds_feature_image = $image_settings['myfeeds_feature_image'];


        $sql = "select
                    $myfeeds_table.publish_status,
                    $myfeeds_table.myfeed_category,
                    $myfeeds_table.feed_tag,
                    $nc_autopublish_table.id,
                    $nc_autopublish_table.guid

                from
                $myfeeds_table,$nc_autopublish_table
                where
                $myfeeds_table.id=$nc_autopublish_table.myfeeds_id and $myfeeds_table.autopublish = 1 ";


        $results = $wpdb->get_results( $sql );

        if( $results ) {
            foreach( $results as $row ) {
                if( $row->guid ) {
                    // publish status
                    $publish_status = "draft";
                    if( $row->publish_status == 1 )
                        $publish_status = "publish";

                    // get the article from API
                    $url = "http://api.newscred.com/article/$row->guid?access_key=" . NC_ACCESS_KEY;

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

                    $url .= "&fields=" . implode( "%20" , $fields );




                    $article = NCpluginArticle::searchByUrl( NC_ACCESS_KEY, $url );
                    $article = $article[0];




                    // add author

                    $article_settings = get_option( "nc_plugin_article_settings" );

                    $author_type = $article_settings['article-author'];

                    if( $author_type == "author" )
                        $author = $article->author;
                    else
                        $author = $article->source->name;


                    $author_id = username_exists( $author );

                    if ( !$author_id ) {

                        $random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
                        $author_id = wp_create_user( $author, $random_password, false );

                        // set author role
                        $article_settings = get_option( "nc_plugin_article_settings" );
                        $wp_user_object = new WP_User( $author_id );
                        $wp_user_object->set_role( $article_settings['article-author-role'] );

                    }

                    // add category

                    $categories = unserialize( $row->myfeed_category );


                    // add tags

                    $tags = "";
                    if( $row->feed_tag == 1 ) {
                        if( $article->topics ) {
                            foreach( $article->topics as $topic )
                                $tags .=  $topic->name . ",";
                            $tags = rtrim( $tags, "," );
                        }
                    }

                    // set publish time as  system time zone

                    $hours= get_option('gmt_offset') ;
                    $time_old = $article->published_at;
                    $time_new = strtotime($time_old);
                    $time_new = $time_new + (60 * 60 * $hours);
                    $publish_time =  date("Y-m-d H:i:s", $time_new);


                    // published the article
                    $post = array(
                              'post_author'    => $author_id ,  //The user ID number of the author.
                              'post_category'  => $categories , //post_category no longer exists, try wp_set_post_terms() for setting a post's categories
                              'post_content'   => $article->description , //The full text of the post.
                              'post_date'      => $publish_time , //The time post was made.
                              'post_date_gmt'  => $publish_time , //The time post was made, in GMT.

                              'post_status'    => $publish_status , //Set the status of the new post.
                              'post_title'     => $article->title , //The title of your post.
                              'post_type'      => 'post' ,   //You may want to insert a regular post, page, link, a menu item or some custom post type
                              'tags_input'     => $tags //For tags.

                            );
                    $post_id = wp_insert_post( $post );
                    // delete article guid from wp_nc_autopublish table
                    if( $post_id ) {

                        // add image set post meta

                        if( $article->image_set ) {
                            $article_image_set = array();
                            foreach( $article->image_set as $image ) {
                                $article_image_set[] = array(
                                    "caption" => $image->caption,
                                    "url"=> $image->image_large,
                                    "published_at" => $image->published_at,
                                    "source" => $image->source->name,
                                    "height" => $image->height,
                                    "width" => $image->width
                                );
                            }
                            add_post_meta( $post_id, "nc_image_set", serialize( $article_image_set ) );

                            if($myfeeds_feature_image && $publish_status == "publish")
                                add_post_meta( $post_id, "nc_feature_image_publish", 0 );
                        }

                        // delete post which is inserted
                        $sql = "delete from $nc_autopublish_table where id=" . $row->id;
                        $wpdb->query( $sql );
                    }

                }
            }
        }

    }

    /**
     *  add feature image for myFeeds
     *  auto publish post in 5 mins
     */
    public function nc_plugin_mins_myfeeds_feature_image_action(){

        $myfeeds_posts =get_posts(
            array( 'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'nc_feature_image_publish',
                        'value' => 0,
                        'compare' => '=',
                    )
                )));

        if( $myfeeds_posts ){
            foreach( $myfeeds_posts as $myfeeds_post ){

                $nc_image_set_meta = get_post_meta($myfeeds_post->ID, "nc_image_set");

                if($nc_image_set_meta){
                    $nc_image_set = unserialize($nc_image_set_meta[0]);
                    if($nc_image_set){
                        set_time_limit( 5000 );
                        $result = $this->add_feature_image($myfeeds_post->ID, $nc_image_set[0]['url'], $nc_image_set[0]['caption']);
                        if($result)
                            update_post_meta($myfeeds_post->ID, "nc_feature_image_publish", 1);

                    }
                }
            }
        }

    }
    /**
     * add feature for myFeeds auto publish
     * @param $post_id
     * @param $image_thumb_url
     * @param $image_caption
     * @return int
     */
    public function add_feature_image($post_id, $image_thumb_url, $image_caption){

        $nc_image_option = get_option( "nc_plugin_image_settings" );
        $image_thumb_url .= "?width=" . $nc_image_option['post_img_width'] ."&amp;height=" . $nc_image_option['post_img_height'];
        global $nc_utility;

        try{
            $result = $nc_utility->nc_upload_image($image_thumb_url, $post_id, 'image_thumbnail');

        }catch(Exception $e){
            return 0;
            print_r($e->getMessage());
        }

        // then find the last image added to the post attachments
        $attachments = get_posts(array('numberposts' => '1', 'post_parent' =>     $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image'));

        $attach_data                    = array();
        $attach_data['ID']              = $attachments[0]->ID ;
        $attach_data['post_title']      = $image_caption;
        $attach_data['post_excerpt']    = $image_caption;

        // Update the post into the database
        wp_update_post( $attach_data );

        if(sizeof($attachments) > 0){
            // set image as the post thumbnail
            set_post_thumbnail($post_id, $attachments[0]->ID);
            return 1;

        }
        return 0;

    }

    /**
     * insertArticleGuid
     * insert myFeeds articles guid
     * into wp_nc_autopublish table
     *
     * @param $url
     * @param $id
     */
    public function insertArticleGuid( $url, $id ) {

        global $wpdb;
        $nc_autopublish_table = $wpdb->prefix . "nc_autopublish";
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";



        $myfeeds_guids = NCpluginArticle::searchByUrl( NC_ACCESS_KEY, $url );

        if( $myfeeds_guids ) {

            foreach( $myfeeds_guids as $guid ) {

                try{
                    $result = $wpdb->insert( $nc_autopublish_table,
                        array('myfeeds_id' => $id,
                            'guid' => $guid->guid
                        )
                    );

                } catch ( Exception $e ) {
                    echo $e->getMessage();
                }

            }

            $wpdb->update(
                $myfeeds_table,
                array(
                    'publish_time' => date( "Y-m-d H:i:s", time() )
                ),
                array( 'id' => $id ),
                array('%s'),
                array( '%d' )
            );

        }
    }
    // get time difference in hour
    public function timeDiff( $firstTime, $lastTime ) {

        $current_time = strtotime( $firstTime );
        $publish_time = strtotime( $lastTime );

        return floor( round( abs( $current_time - $publish_time ) / ( 60 ), 2 ) );
    }
}
?>