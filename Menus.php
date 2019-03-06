<?php

namespace Lib;

/**
* Menus Lib Class
*/

class Menus {


    function __construct($menus) {

        $this->menus = $menus;

        $this->register();

    }

    public function show_in_rest(){
        add_action( 'init', array($this,'wp_rest_menus') );
    }


    private function register(){

        if ( function_exists( 'register_nav_menus' ) ) {

            register_nav_menus(
                $this->menus
            );
        
        };

        return $this;
        
    }

    public function expose_js($script, $var_name){

        add_action( 'wp_enqueue_scripts', function() use ($script, $var_name){

            wp_localize_script( $script, $var_name, $this->get_all_js());

        } );

    }


    public function get_all_js(){

        $menus = \Lib\VueCache::getCache('menus');

       
	    if(!$menus) {
            $menus = array();
            foreach($this->menus as $key => $menu){
                $menus[$key] = $this->get_menu($key);
            }
            \Lib\VueCache::saveCache('menus', $menus, array('flush_on_save'=>array('nav_menu_item')));
        }
        

        return $menus;

    }

    

    public static function get_menu($menu = 'main'){
        

            if(is_object($menu)){

                $menu_name=$menu->get_param( 'name' );

            } else {

                $menu_name = $menu;
            }


            $locations = get_nav_menu_locations();

            if(!isset($locations[ $menu_name ])) return;
                $menu_id = $locations[ $menu_name ];

                $items = wp_get_nav_menu_items($menu_id);

                if(!$items) return false;

                $itemsId = array_column($items, 'ID');
                
                foreach($items as $key => &$item){

                    $newItem = (array) $item;

                    $newItem['ID'] = $item->ID;
                    $newItem['id'] = $item->ID;
                    $newItem['title'] = $item->title;
                    $newItem['url'] = $item->url;
                    $classes = '';

                    if($item->classes){
                        foreach($item->classes as $class){
                            $classes = $class.' ';
                        }
                    }

                    $newItem['classes'] = $classes;
                    $newItem['target'] = $item->target;
                    if(!$newItem['target']){
                        $newItem['target'] ='_self';
                    }
                    
                }
            
            return $items;

    }


	/**
	 * Init JSON REST API Menu routes.
	 *
	 * @since 1.0.0
	 */
	function wp_rest_menus() {

        if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {
			$class = new \Lib\WP_REST_Menus();
			add_filter( 'rest_api_init', array( $class, 'register_routes' ) );
		}
	}


}
