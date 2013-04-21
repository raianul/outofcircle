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
 *  NC_Utility Class
 *  utility functions like : controller router , others utility function
 *  for plugin
 */

require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');

class NC_Utility{
    
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
    * Constructor
    *
    * Default constructor - application initialization
    **/
    private function __construct() {}

    /**
     * Loads a Controller object
     * and outputs the display action wise
     * @static
     * @param bool $controller
     * @param string $action
     * @return bool
     */

    public static function loadController( $controller = false, $action = '' ) {

        if( is_array( $action ) ) {

            if( isset( $action['args']['controller'] ) )
                $controller = $action['args']['controller'];
            if( isset( $action['args']['action'] ) ) {
                $temp_action = $action['args']['action'];
                $action = null;
                $action = $temp_action;
            }
            else
                $action = null;
        }


        if ( false === ( $obj = self::getController( $controller ) ) ) {
            return false;
        }
       
        /* Capture custom exceptions to provide friendly error messages */
        try
        {
            /* Call the controller, gather output */
            if( $action != "" )
                $obj->$action();
            else
                $obj->load();

        }
        catch ( NC_Exception $e )
        {
            $e->display();
        }
        
        return true;
    }

    /**
     * Instantiates a controller object based on its name.
     * @static
     * @param bool $controller
     * @param array $params
     * @return bool
     */
    public static function getController( $controller = false, array $params = array() ) {
        
        /* Build file name for the controller based on name and make sure it exists */
        $controller_file = strtr( strtolower( $controller ), '_', '-' );
        $file = sprintf( NC_CONTROLLER_PATH . '/class-nc-%s.php', $controller_file );
        if ( !file_exists( $file ) )
        {
            return false;
        }

        /* Include the controller class file */
        include_once( $file );
        
        /* Build the class name, make sure it exists */
        if ( !class_exists( $className = sprintf( 'NC_%s', $controller ) ) )
        {
            return false;
        }

        /* Instantiate the class object */
        $obj = new $className( $params );

        return $obj;
    }


    /**
     * get request parameters
     * @param $options
     * @return string
     */
    public  function getRequestParams( $options ) {
        if ( !is_array( $options ) || empty( $options ) )
            return;

        $request_params = '';

        foreach ( $options as $key => $value ) {
            if ( is_array( $value ) && ( $key === 'sources' || $key === 'source_countries' || $key === 'topics' ) ) {
                //sources and source_countries params are to be joined by space
                //and don't need to be parameterized
                $request_params .= '&' . $key . '=' . urlencode( join( ' ', $value ) );
            } elseif (is_bool( $value ) ) {
                //PHP turns boolean true/false into 1/0 when casted to string.
                //Need to pass true/false as is.
                $request_params .= ( $value === True) ? '&' . $key . '=' . 'true' :
                    '&' . $key . '=' . 'false';
            } elseif ( $key === 'from_date' || $key === 'to_date' ) {
                $request_params .= '&' . $key . '=' . urlencode($value);
            } elseif ( is_array( $value ) ) {

                $request_params .= '&' . $key . '=' . urlencode( NewsCred_Api_Base::parameterize( $value ) );
            } else {
                $request_params .= '&' . $key . '=' . urlencode( $value );
            }
        }

        return $request_params;
    }

    /**
     * gets content from a URL via curl
     * @param $url
     * @return mixed
     */
    public static  function get_url( $url ) {

        if (function_exists('curl_init')) {

            try{

                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_TIMEOUT, 120);
                $response = curl_exec($curl);

                $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if ($http_status_code !== 200)
                    return false;



            }catch(NC_Exception $e){
                return false;
            }

        }else{

            if (ini_get('allow_url_fopen') === '')
                return false;

            $response = file_get_contents($url);

        }

        if($response)
            return $response;
        else
            return false;

    }

    /**
     * elapsed_time
     * @param $timestamp
     * @param int $precision
     * @return string
     */

    function elapsed_time( $timestamp, $precision = 2 ) {
        $time = time() - $timestamp;
        $a = array('decade' => 315576000, 'year' => 31557600, 'month' => 2629800, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'min' => 60, 'sec' => 1);
        $i = 0;
        foreach( $a as $k => $v ) {
            $$k = floor( $time/$v );
            if ( $$k ) $i++;
            $time = $i >= $precision ? 0 : $time - $$k * $v;
            $s = $$k > 1 ? 's' : '';
            $$k = $$k ? $$k.' '.$k.$s.' ' : '';
            @$result .= $$k;
        }
        return $result ? $result . 'ago' : '1 sec to go';
    }

    /**
     * nc_upload_image
     * @param $file
     * @param $post_id
     * @param null $desc
     * @return int|object|string
     */

    function nc_upload_image($file, $post_id, $desc = null) {

        if ( ! empty($file) ) {
            // Download file to temp location

            $tmp = download_url( $file );

            $size = getimagesize($file);

            $file_type = "";
            switch ($size['mime']) {
                case "image/gif":
                    $file_type = "gif";
                    break;
                case "image/jpeg":
                    $file_type = "jpeg";
                    break;
                case "image/jpg":
                    $file_type = "jpg";
                    break;
                case "image/png":
                    $file_type = "png";
                    break;
                case "image/bmp":
                    $file_type = "bmp";
                    break;
            }

            // Set variables for storage
            // fix file filename for query strings
            $parsed_url = parse_url($file);
            $file_array['name'] = basename($parsed_url['path']) . '.' . $file_type;
            $file_array['tmp_name'] = $tmp;

            // If error storing temporarily, unlink
            if ( is_wp_error( $tmp ) ) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
            }

            // do the validation and storage stuff
            $id = media_handle_sideload( $file_array, $post_id, $desc );
            // If error storing permanently, unlink
            if ( is_wp_error($id) ) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            $src = wp_get_attachment_url( $id );

        }

        // Finally check to make sure the file has been saved, then return the html
        if ( ! empty($src) ) {
            $alt = isset($desc) ? esc_attr($desc) : '';
            $html = "<img src='$src' alt='$alt' />";
            return $html;
        }


    }
    

}

?>