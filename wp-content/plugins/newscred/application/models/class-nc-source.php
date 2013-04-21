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
 * NC_Source Class
 */

class NC_Source{

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
     * get autosugested  source list
     * @static
     *
     */
    public static  function get_sources_suggestion(){

        $query = $_GET['term'];

        if(!empty($query)){

            $source_fields = array(
                'source.guid',
                'source.name',
            );

            $options = array(
                "fields"        => $source_fields,
                "autosuggest"   => true,
                'fulltext'      => true
            );
            if( isset( $_GET['pagesize'] ) )
                $options['pagesize'] = $_GET['pagesize'];

            $sources = NCpluginSource::search(NC_ACCESS_KEY, $query, $options);

            $sources_array =  array();

            foreach($sources as $source){
                $sources_array[(string)$source->guid ] = (string)$source->name;
            }
            echo json_encode($sources_array);

            exit;

        }
    }
}

?>