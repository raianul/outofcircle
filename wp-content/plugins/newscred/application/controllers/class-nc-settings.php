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
 * NC_Settings Class
 */

class NC_Settings extends NC_Controller {

    /**
     *  Newdcred Api access key settings
     */
    public function newscred() {

        $message = array();

        if( isset( $_POST['nc-settings-submit'] ) && !empty( $_POST['access_key'] ) ) {

            $result = $this->checkAccessKey( $_POST['access_key'] );

            if( $result ) {
                update_option( "nc_plugin_access_key", $_POST['access_key'] );
                $message[] = "Your Access Key is successfully added";
            }
            else {
                $message[] = "Your Access Key is Invalid . Please add valid access key ";
            }

        }

        $this->_template->assign( 'message', $message );
        $this->_template->assign( 'access_key', get_option( "nc_plugin_access_key" ) );
        $this->_template->display( 'settings/newscred.php' );

        exit;
    }
    /***
     * check the newscred api access key
     * @static
     * @param $accesskey
     * @return bool
     */
    public static function checkAccessKey( $accesskey ){

        $url = sprintf("%s/%s?access_key=%s", NC_DOMAIN, "articles", $accesskey );

        if (function_exists('curl_init')) {

            try{

                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_TIMEOUT, 120);
                $response = curl_exec($curl);

                $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($http_status_code !== 200)
                    return false;
                else
                    return true;


            }catch(NC_Exception $e){
                return false;
            }

        }else{

            if (ini_get('allow_url_fopen') === '')
                return false;

            $response = file_get_contents($url);
            if($response)
                return true;
            else
                return false;
        }

    }


    /**
     * article settings  page
     */

    public function article() {

        global $nc_cache;

        $message = array();

        if( isset( $_POST['article_settings_submit'] ) ){

            $article_settings = array();


            if( isset( $_POST['has_images'] ) )
                $article_settings['has_images']         = $_POST['has_images'];
            else
                $article_settings['has_images']         = "";

            if( isset( $_POST['fulltext'] ) )
                $article_settings['fulltext']           = $_POST['fulltext'];
            else
                $article_settings['fulltext']           = "";


            if( isset( $_POST['publish_time'] ) )
                $article_settings['publish_time']         = $_POST['publish_time'];
            else
                $article_settings['publish_time']         = "";

            if( isset( $_POST['tags'] ) )
                $article_settings['tags']         = $_POST['tags'];
            else
                $article_settings['tags']         = "";

            if( isset( $_POST['categories'] ) )
                $article_settings['categories']         = $_POST['categories'];
            else
                $article_settings['categories']         = "";


            $article_settings['article-author']         = $_POST['article-author'];
            $article_settings['article-author-role']    = $_POST['article-author-role'];
            $article_settings['custom-post-type']    = $_POST['custom-post-type'];


            update_option( "nc_plugin_article_settings", $article_settings );
            $message[] = "Your Article Settings Updated Successfully .";
        }

        $this->_template->assign( 'message', $message );

        //  get all wp role list
        global $wp_roles;

        if ( ! isset( $wp_roles ) )
            $wp_roles = new WP_Roles();

        $roles = $wp_roles->get_names();

        $this->_template->assign( 'roles', $roles );
        $this->_template->assign( 'access_key', get_option( "nc_plugin_access_key" ) );
        $this->_template->assign( "article_settings", get_option( "nc_plugin_article_settings" ) );
        $this->_template->display( 'settings/article.php' );

    }

    /**
     * image settings page
     */

    public function image() {

        global $nc_cache;

        $message = array();

        if( isset( $_POST['image_settings_submit'] ) ) {
            $image_settings = array();

            $image_settings['minwidth']             = $_POST['minwidth'];
            $image_settings['minheigth']            = $_POST['minheigth'];

            $image_settings['post_img_width']       = $_POST['post_img_width'];
            $image_settings['post_img_height']      = $_POST['post_img_height'];


            if( isset( $_POST['safe_search'] ) )
                $image_settings['safe_search']      = $_POST['safe_search'];
            else
                $image_settings['safe_search']      = "";

            if( isset( $_POST['myfeeds_feature_image'] ) )
                $image_settings['myfeeds_feature_image']      = $_POST['myfeeds_feature_image'];
            else
                $image_settings['myfeeds_feature_image']      = "";


            update_option( "nc_plugin_image_settings", $image_settings );
            $message[] = "Your Image Settings Updated Successfully .";


        }

        $this->_template->assign( 'message', $message );

        $this->_template->assign( 'access_key', get_option( "nc_plugin_access_key" ) );

        $this->_template->assign( "image_settings", get_option( "nc_plugin_image_settings" ) );
        $this->_template->display( 'settings/image.php' );
    }
}

?>