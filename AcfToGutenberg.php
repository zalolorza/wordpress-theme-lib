<?php

namespace Lib;
use Timber\Timber;

/**
 * Register ACF Blocks for Gutenberg editor
 */

class AcfToGutenberg {


    /**
    * Hook init action
    */

    public function __construct() {

	   add_action('acf/init', array($this, 'register_blocks'));
	   
      
    }



	/**
    * Get block fields from gutenberg
	*/
	public static function get_block_fields($id,$post_type = 'post'){

		$fields = array();

		if($post_type == 'page'){
			$args = array(
				'page_id'         => $id
			);
		} else {
			$args = array(
				'p'         => $id
			);
			if($post_type != 'post'){
				$args['post_type'] = array( $post_type);
			}
		}
		
		$query = new \WP_Query( $args );
		$post = $query->posts[0];

	
		$blocks = gutenberg_parse_blocks($post->post_content);


		foreach($blocks as $key => $block){
			$block_fields = array();
			
			if(sizeof($block['attrs'])>0){
				$block_fields = self::get_block_acf_fields($block['attrs']['data']);

			
				$fields[] = array(
					'fields' => $block_fields,
					'block' => $block,
					'name' => $block['blockName']
				);
			}
			
		}
		
		return $fields;
	}

	/**
    * Get block fields 
	*/
	public static function get_block_acf_fields($fields){
	
	
		$block_fields = array();

		if(!$fields) return $fields;

		foreach( $fields as $key => $block_field){

			
			if(is_integer($key)){
				$key = $block_field['key'];
			}

			
			
			$field = get_field_object($key);

		
		
			if($field['sub_fields'] || gettype($block_field) == 'array'){

				if($field['name']){
					$block_fields[$field['name']] = self::get_block_acf_fields($block_field);
				} else {
					
					$block_fields[] = self::get_block_acf_fields($block_field);
				
				}
				
			} else {
				
				$block_fields[$field['name']] = $block_field;
			}

			


		}
		return $block_fields;
	}
	


}