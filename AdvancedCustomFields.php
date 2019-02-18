<?php

namespace Lib;

/**
 * Register ACF fields, Options Page,...
 */

class AdvancedCustomFields {

    protected $counterSketchButtons = 0;
    
    public function __construct() {


        // 1. customize ACF path
        add_filter('acf/settings/path', array($this,'my_acf_settings_path'));

        // 2. customize ACF dir
        add_filter('acf/settings/dir', array($this,'my_acf_settings_dir'));

        // 3. Hide ACF field group menu item
        if(NODE_ENV === 'production'){
            add_filter('acf/settings/show_admin', '__return_false');
        }

        // 4. Include ACF
        include_once( get_stylesheet_directory() . '/lib/acf/acf.php' );

        add_filter('acf/settings/load_json', array($this,'json_load_point'));
        add_filter('acf/settings/save_json', array($this,'json_save_point'));
        add_action('acf/init', array($this,'set_google_map_key'));

     
    }

    public function my_acf_settings_path( $path ) {
 
        // update path
        $path = get_stylesheet_directory() . '/lib/acf/';
        
        // return
        return $path;
        
    }

    public function my_acf_settings_dir( $dir ) {
 
        // update path
        $dir = get_stylesheet_directory_uri() .'/lib/acf/';
        
        // return
        return $dir;
        
    }

    public function set_google_map_key( ) {

        if(defined('GOOGLE_MAPS_API_KEY')){
            acf_update_setting('google_api_key', GOOGLE_MAPS_API_KEY);
        }

    }

    public function add_sketch_button($url, $location){

        if( ! function_exists('acf_add_local_field_group') ) return;

        $this->counterSketchButtons++;

        acf_add_local_field_group(array(
            'key' => 'group_5ba0c03716ea'.$this->counterSketchButtons,
            'title' => 'Sketch prototype '.$this->counterSketchButtons,
            'fields' => array(
                array(
                    'key' => 'field_5ba0c0371ac4'.$this->counterSketchButtons,
                    'label' => '',
                    'name' => '',
                    'type' => 'message',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '<div class="button-sketch" style="padding:0px">
        <a style="padding:5px 5px 5px 30px; display:inline-block; width:100%; background:rgb(253,179,1); font-size:1.2em; line-height:2em;	text-decoration:none; color:white; text-transform:uppercase; height:auto; text-align:center; font-family \'Fabrik\'" target="_blank" href="'.$url.'"><svg viewBox="0 0 27 25" style="position:absolute; top:9px; left:15px" class="Logo-kCrwua gcOzbb" width="27px" height="25px" style="margin: auto;"><g fill="none" fill-rule="evenodd"><path d="M5.72 9l7.78 15.313L.422 9h5.297zm15.56 0L13.5 24.313 26.578 9h-5.297z" fill="#EA6C00"></path><path fill="#FDB300" d="M5.72 9h15.56L13.5 24.313z"></path><path d="M13.5.688l-7.371.777L5.719 9 13.5.687zm0 0l7.371.777.41 7.535L13.5.687z" fill="#FDD231"></path><path d="M26.578 9l-5.707-7.535L21.28 9h5.3zM.422 9l5.707-7.535L5.719 9H.422z" fill="#FDA700"></path><path fill="#FEEEB7" d="M13.5.688L5.72 9h15.56z"></path></g></svg>Launch prototype</a>
        </div>',
                    'new_lines' => 'wpautop',
                    'esc_html' => 0,
                ),
            ),
            'location' => array(
                array(
                    $location
                ),
            ),
            'menu_order' => 30,
            'position' => 'side',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ));
    }
}

