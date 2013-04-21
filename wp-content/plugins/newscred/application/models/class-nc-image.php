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
 * NC_Image Class
 */

class NC_Image{

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
    public function __construct(array $params = array())
    {

    }

    /**
     * get_metabox_images
     * search the images for metabox
     * @param $query
     * @param $pagesize
     * @param $offset
     * @return array
     */
    public static function get_metabox_images($query ,$pagesize, $offset){

        $image_options = get_option("nc_plugin_image_settings");


        $fields = array(
            'image.guid',
            'image.caption',
            'image.description',
            'image.height',
            'image.width',
            'image.published_at',
            'image.source.name',
            'image.urls.large',
            'image.attribution_text'

        );

        $options = array(
            'fields' => $fields,
            'pagesize' => $pagesize,
            'offset'   => $offset,
            'licensed' => true
        );
        if (isset($_POST['sort'])) {
            $options['sort'] = $_POST['sort'];
        }
        if (isset($_POST['sources'])) {
            $options['sources'] = $_POST['sources'];
        }

        if (isset($_POST['topics'])) {
            $options['topics'] = $_POST['topics'];
        }
        if($image_options['minwidth'])
            $options['minwidth'] = $image_options['minwidth'];

        if($image_options['minheigth'])
            $options['minheigth'] = $image_options['minheigth'];

        if($image_options['safe_search'])
            $options['safe_search'] = "true";



        $images = array();

        try{

            $images = NCpluginImage::search(NC_ACCESS_KEY, $query, $options );

        }catch(NC_Exception $e){
            echo $e->getMessage();

        }

        return $images;
    }




}