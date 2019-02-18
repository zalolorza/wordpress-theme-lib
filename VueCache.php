<?php

namespace Lib;

class VueCache extends Singleton {

    /**
     * 
	 * Construct
	 *
	 */
    private $objects = array();
    private $defaultObject = array(
        'flush_on_save' => array(),
        'expiration_date' => false
    );

    private $storedObejcts = null;

    protected function __construct(){

        $this->accept_vue_dev_mode = true;
        $this->dev_mode = false;


        add_action( 'save_post', array($this,'flushOnSave'), 10, 3 );

        add_action( 'after_rocket_clean_cache_dir', array($this,'emptyCache'));

        
    }

    
    /**
     * 
	 * PUBLIC Getters
	 *
	 */
    public function getCache($key, $value = array()){

        if(self::skipCache()){
            return false;
        };

        $key = self::getKey($key);

        $cache = get_option($key);

        return $cache;

    }


    /**
     * 
	 * PUBLIC Setters
	 *
	 */
    
    public function saveCache($key, $content, $value = array()){

        if(self::skipCache()){
            return;
        };

        $key = self::getKey($key);
        
        if(!self::getObject($key)){
            self::newObject($key, $value);
        } 

        update_option($key,$content,false);

    }

    public function emptyCache($key = null){

        if($key != null){
            $key = self::getKey($key);
        }
       
        self::flushCache($key);

    }



    /**
     * 
	 * Private
	 *
	 */

    private function skipCache(){

        $self = self::getInstance();

        if($self->dev_mode || ($self->accept_vue_dev_mode && NODE_ENV == 'development')){
            return true;
        } else {
            return false;
        }
    }


    private function getObject($key){

        $self = self::getInstance(); 
        $objects =$self->objects;

        if(!isset($objects[$key])) return false;

        return $objects[$key];

    }

    private function getKey($key){

        $key = 'VTCache/'.$key;

        if(defined('ICL_LANGUAGE_CODE')){
            $key .= '/'.ICL_LANGUAGE_CODE;
        }
        
        return untrailingslashit($key);

    }

    private function getAllObjectsKey(){
        return untrailingslashit('VTCacheObjects/'.get_site_url());
    }

    private function getAllStoredObjects(){
       
        $self = self::getInstance();

       
        if(!$self->storedObjects){
            $self->storedObjects = get_option(self::getAllObjectsKey());
            if($self->storedObjects == false) {
                $self->updateStoredObjects(array());
            }
        }

       return $self->storedObjects;
        
    }



    private function updateStoredObjects($objects){

        $self = self::getInstance();

        update_option(self::getAllObjectsKey(),$objects, false);
       
        $self->storedObjects = $objects;
        

        return $self->storedObjects;
    }



    private function newObject($key, $value = array()){

        $self = self::getInstance();

        $value = array_merge($self->defaultObject, $value);

    
        if(!isset($self->objects[$key])){
            $self->objects[$key] = $value;
            $cacheStoredObjects = self::getAllStoredObjects();
            if(!isset($cacheStoredObjects[$key]) || $value != $cacheStoredObjects[$key]) {
                $cacheStoredObjects[$key] = $value;
                self::updateStoredObjects($cacheStoredObjects);
            }
        }
        
        return $self->objects[$key];

    }

    private function removeObject($key){

        $self = self::getInstance();

        $cacheStoredObjects = self::getAllStoredObjects();
        
        if(isset($cacheStoredObjects[$key])) {
                unset($cacheStoredObjects[$key]);
                self::updateStoredObjects($cacheStoredObjects);
            }

    }


    private function removeAllObjects(){

        $self = self::getInstance();

        self::updateStoredObjects(null);

    }



    private function flushCache($objectName = null){

        if($objectName == null){

            foreach(self::getAllStoredObjects() as $key => $value){

                self::flushObject($key);

            }

        } else {
            
            self::flushObject($objectName);
            

        }

    }


    private function flushObject($objectName){

        delete_option($objectName);
        self::removeObject($objectName);

    }


    public function flushOnSave($post_id, $post, $update){

        $post_type = get_post_type($post_id);

        foreach(self::getAllStoredObjects() as $key => $value){

            if(in_array($post_type,$value['flush_on_save'])){
                self::flushObject($key);
            }

        }

    }
    

}

VueCache::getInstance();