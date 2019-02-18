<?php

/**
 * 
 * LIB spl autoload function
 *
 */

spl_autoload_register(function($class) {

    $lib = 'Lib\\';
    $length = strlen($lib);

    if (strncmp($lib, $class, $length) !== 0)  return;
    
    $base_directory = __DIR__.'/';
    $dir = str_replace('\\', '/',  substr($class, $length));
    $file = $base_directory . $dir . '.php';
    
    if(file_exists($file)) {
        require $file;
    }

});