<?php

function dump($dump,$die=true){
    echo '<pre>';
    var_dump($dump);
    echo '<pre>';
    if($die){
        die();
    }
}