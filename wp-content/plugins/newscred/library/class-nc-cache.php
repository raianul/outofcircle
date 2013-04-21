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
* NC_Cache Class
* its a local file cache for newscred source and topics
*
*/


class NC_Cache {
    
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


    }

    /**
     * get cache file content
     * @param $file
     * @param $url
     * @param int $hours
     * @return string
     */
    public function get_content( $file, $url, $hours = 24 ) {
        
        global $nc_utility;
    	$current_time = time(); 
        $expire_time = $hours * 60 * 60;
        
        $file_time = 0;
        if ( file_exists( $file ) )
            $file_time = filemtime( $file );

    	//decisions, decisions


//        if( file_exists( $file ) && ( $current_time - $expire_time < $file_time ) ) {
//            $content = file_get_contents( $file );
//            if( empty( $content ) ) {
//                $content = $nc_utility->get_url( $url );
//                file_put_contents( $file, $content );
//            }
//            return $content;
//    	}
//    	else {

    		$content = $nc_utility->get_url( $url );
          	//file_put_contents( $file, $content );
    		return $content;
    	//}
    }
    
    
   
    /**
     *  get nc source list from cache
     *  if its not expire
     * @return array
     */
    public function get_nc_sources() {

        $source_file_name = NC_CACHE_PATH . '/nc-sources.txt';
        $url  = 'http://api.newscred.com/api/user/source_filters?access_key=' . NC_ACCESS_KEY;

        try{
    	    $sources = json_decode( self::get_content( $source_file_name, $url, 24 ) );

        }catch( Exception $e ) {
            throw new NC_Exception( 'Class:: ' . __CLASS__ . ' Line:: ' . __LINE__ .
                ' ' . $e->getMessage() );
        }

        $sources_list = array();
        if( $sources ) {
            foreach( $sources as $source ){
                $sources_list[$source->name] = $source->name;
            }
        }
        return $sources_list;
    }
    
    /**
     * get nc source list from cache if its not
     * expire
     * @return array
     */
    public function get_nc_topics() {
        
        $topic_file_name = NC_CACHE_PATH . '/nc-topics.txt';
        $url  = 'http://api.newscred.com/api/user/topic_filters?access_key=' . NC_ACCESS_KEY;
   
    	$topics = json_decode( self::get_content( $topic_file_name, $url, 24 ) );
        $topics_list = array();
        if( $topics ){
            foreach( $topics as $topic ){
                $topics_list[$topic->name] = $topic->name;
            }
        }
        return $topics_list;
    }   
   
}
?>