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
 * NC_Article Class
 */

class NC_Article{

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
     * search the articles list for metabax
     * get_metabox_articles
     * @static
     * @param $query
     * @param $pagesize
     * @param $offset
     * @return array
     */
    public static  function get_metabox_articles($query, $pagesize, $offset){


        $article_options = get_option("nc_plugin_article_settings");




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
            'fields' => $fields,
            'pagesize' => $pagesize,
            'offset'   => $offset
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


        if($article_options['has_images'])
            $options['has_images'] = "true";

        if($article_options['fulltext'])
            $options['fulltext'] = "true";



        $articles = array();

        try{
            $articles = NCpluginArticle::search(NC_ACCESS_KEY, $query, $options);

        }catch(NC_Exception $e){

        }

        return $articles;
    }

}

?>