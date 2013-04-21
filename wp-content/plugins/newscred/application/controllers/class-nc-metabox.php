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
 *  NC_Metabox Class
 *  this controller help to genarate and controle the
 *  metabox module
 *  Feature list of meta box :
 *  - user can search articles, images from the wp post meta box
 *  - user can insert image in the post and can set image as feature image
 *
 */

class NC_Metabox extends NC_Controller {


    private $index;

    /**
     *  display the metabox in the admin post edit sidebar
     */

    public function init(){
        $this->_template->assign('access_key', get_option("nc_plugin_access_key") );
        $this->_template->assign( "article_settings", get_option( "nc_plugin_article_settings" ) );
        $this->_template->assign("myFeeds", $this->getAllMyFeeds() );
        $this->_template->display('metabox/index.php');
    }

    /**
     * search articles
     * from newscred api
     * by meta box
     */
    public function search(){

        global $nc_article, $nc_image;

        if($_POST){

            $this->index = 1;
            $articles = array();
            $images = array();
            $only_articles = "";
            $template = "search.php";


            // article pagiantion
            if( isset( $_POST['only_articles'] ) ){

                $pagesize = 10;
                $offset = 0;

                $page = $_POST['page'];
                $offset = ($page - 1) * $pagesize;
                $this->index = $offset + 1;

                $articles = $nc_article->get_metabox_articles($_POST['query'], $pagesize, $offset);
                $template = "includes/articles.php";

            }

            // image pagiantion
            if( isset( $_POST['only_images'] ) ){

                $pagesize = 36;
                $offset = 0;

                $page = $_POST['page'];
                $offset = ($page - 1) * $pagesize;
                $this->index = $offset + 1;

                $images = $nc_image->get_metabox_images($_POST['query'], $pagesize, $offset);
                $template = "includes/images.php";

            }
            // myFeeds

            if( isset( $_POST['only_myfeeds'] ) ){


                $pagesize = 10;
                $offset = 0;

                $page = $_POST['page'];
                $offset = ($page - 1) * $pagesize;
                $this->index = $offset + 1;

                $myfeeds = $this->myFeeds($_POST['query'], $pagesize, $offset);
                $this->_template->assign("myfeeds", $myfeeds);
                $template = "includes/myfeeds.php";

            }

            if( isset( $_POST['all'] ) ){

                $pagesize = 10;
                $articles = $nc_article->get_metabox_articles($_POST['query'], $pagesize, 0);

                //$pagesize = 36;
                //$images = $nc_image::get_metabox_images($_POST['query'], $pagesize, 0);
                $images = "1";
            }

            $this->_template->assign("myFeeds", $this->getAllMyFeeds() );

            $this->_template->assign("index", $this->index );
            $this->_template->assign("articles", $articles );
            $this->_template->assign("images", $images );
            $this->_template->display('metabox/'.$template);
        }
        exit;
    }


    /**
     * @param $query
     * @param $pagesize
     * @param $offset
     * @return array
     */
    function myFeeds($query, $pagesize, $offset){


        $sources =  $_POST['sources'];
        $source_str = "";
        if($sources)
            $source_str = "&sources=" . implode(" ",$sources);

        $topics =  $_POST['topics'];
        $topics_str = "";
        if($topics)
            $topics_str = "&topics=" . implode(" ",$topics);

        $sort_str = "";
        if (isset($_POST['sort'])) {
            $sort_str = '&sort=' . $_POST['sort'];
        }

        global $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        $sql = "select apicall  from $myfeeds_table where id=".$_POST['myfeed_id'];

        $apicall = $wpdb->get_var($sql);
        if( $apicall ){
            if( $query )
                $apicall .= "&query=" . $query . $source_str . $topics_str;

            $apicall .=  $source_str . $topics_str . $sort_str . "&pagesize=" . $pagesize ."&offset=" .$offset;

            $result = NCpluginArticle::searchByUrl(NC_ACCESS_KEY, $apicall);
            if( $result )
                return $result;
        }


        return ;

    }

    /**
     * getAllMyFeeds
     * @return mixed
     */
    function getAllMyFeeds(){
        global $wpdb;
        $myfeeds_table = $wpdb->prefix . "nc_myfeeds";

        $sql = "select *from $myfeeds_table";

        $result = $wpdb->get_results($sql);
        if($result)
            return $result;

        return ;

    }


    /**
     * add feature image from
     * search image list
     */

    public function add_feature_image(){

        global $nc_utility;

        $post_id            = $_POST['p_id'];
        $image_thumb_url    = $_POST['url'];

        //Display the image in the browser

        // load the image

        try{
            $result = $nc_utility->nc_upload_image($image_thumb_url, $post_id, 'image_thumbnail');

        }catch(Exception $e){
            print_r($e->getMessage());
        }

        // then find the last image added to the post attachments
        $attachments = get_posts(array('numberposts' => '1', 'post_parent' =>     $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image'));

        $attach_data                    = array();
        $attach_data['ID']              = $attachments[0]->ID ;
        $attach_data['post_title']      = $_POST['caption'];
        $attach_data['post_excerpt']    = $_POST['caption'];


        // Update the post into the database
        wp_update_post( $attach_data );



        if(sizeof($attachments) > 0){
            // set image as the post thumbnail
            set_post_thumbnail($post_id, $attachments[0]->ID);

        }

        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail_size' );

        $image_url = $thumb['0'];

        $this->_template->assign('post_id', $post_id );
        $this->_template->assign('image_url', $image_url );
        $this->_template->display('metabox/addimage.php');


        exit;
    }

    /**
     * remove feature image
     */
    public function remove_feature_image(){
        $p_id = $_POST['p_id'];
        delete_post_thumbnail( $p_id );
        $image_url = admin_url() . "media-upload.php?post_id=$p_id";
        $this->_template->assign( 'image_url', $image_url );
        $this->_template->display( 'metabox/remove-image.php' );
        exit;
    }


    /**
     * NOTE: not using since v 1.0.2
     *  get suggested topics and
     * source from keyword
     *
     * get_suggested_topics_source
     */
    public function get_suggested_topics_source(){

        $query = $_GET['query'];
        $result_array =  array();

        $source_fields = array(
            'source.guid',
            'source.name',
        );

        $options = array(
            "fields"        => $source_fields,
            "autosuggest"   => true,
            "pagesize"      => 5,
            'fulltext'      => true

        );
        // get the sources

        $sources = NCpluginSource::search(NC_ACCESS_KEY, $query, $options);

//        if(empty($sources))
//            $sources = NCpluginSource::searchRelated(NC_ACCESS_KEY, $query, $options);

        $sources_array =  array();
        if($sources){
            foreach($sources as $source){
                $sources_array[] = array("guid" =>(string)$source->guid,"name"=> (string)$source->name);
            }
        }

        $topics_fields = array(
            'topic.guid',
            'topic.name',
        );

        $topics_options = array(
            'fields'        => $topics_fields,
            "autosuggest"   => true,
            "pagesize"      => 5
        );

        $topics = NCpluginTopic::search(NC_ACCESS_KEY, $query, $topics_options);
        $topics_array = array();

        if($topics){
            foreach($topics as $topic)
                $topics_array[] = array("guid" =>(string)$topic->guid,"name"=> (string)$topic->name);
        }

        $result_array['sources'] = $sources_array;
        $result_array['topics'] = $topics_array;

        echo json_encode($result_array);

        exit;
    }
}

?>