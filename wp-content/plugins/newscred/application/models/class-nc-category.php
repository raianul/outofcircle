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
 * NC_Category model Class
 */

class NC_Category{

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
     * add newscred api category
     * to wordpress
     *
     * @static
     * @param $post_id
     * @return mixed
     */
    public static function add_newscred_category( $post_id ){
        //echo $post_id;die;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;



        // Check permissions
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] )
        {
            if ( !current_user_can( 'edit_page', $post_id ) )
                return;
        }
        else
        {
            if ( !current_user_can( 'edit_post', $post_id ) )
                return;
        }

        // OK, we're authenticated: we need to find and save the data

        if(  !wp_is_post_revision($post_id) && $post_id && isset($_POST['nc-selected-category'])){

            remove_action('save_post', array( &$this, 'add_newscred_category' ) );

            $category = $_POST['nc-selected-category'];

            if( $category != ""){
                $category_list = explode(",", $category);

                $category_array = array();

                foreach($category_list as $cat){

                    $cat_id = get_cat_ID( $cat );

                    if( $cat_id == 0 && !is_int($cat)){
                        $slug = strtolower($cat);
                        $new_cat = array('cat_name' => $cat,
                            'category_nicename' => $slug
                        );

                        $my_cat_id = wp_insert_category($new_cat);

                        $category_array[]= (int)$my_cat_id;
                    }

                    $category_array[]= (int)$cat_id;

                }


                if($category_array)
                    wp_set_object_terms( $post_id, $category_array, 'category');


            }

            // re-hook this function
            add_action('save_post', array( &$this, 'add_newscred_category' ) );

        }
    }

}

?>