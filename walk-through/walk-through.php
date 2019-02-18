<?php

function load_walkthrough() {
    wp_enqueue_style( 'walk_through',   SCRIPTS_ROOT . '/dist/walkthrough.'.SCRIPTS_HASH.'.css', array(), null );
    wp_enqueue_script( 'walk_through', SCRIPTS_ROOT . '/dist/walkthrough.'.SCRIPTS_HASH.'.js', array(), null, true );
}

add_action('admin_enqueue_scripts', 'load_walkthrough');



function custom_register_admin_scripts() {

    $screen = get_current_screen();

    wp_localize_script( 'walk_through', 'WT_VIEW', $screen->id);
    
} 
add_action( 'admin_enqueue_scripts', 'custom_register_admin_scripts' );