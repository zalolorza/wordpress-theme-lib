<?php

namespace Lib;

require  __DIR__ . '/vendor/autoload.php';
require  __DIR__ . '/Helpers.php';

/**
 * We're going to configure our theme inside of a subclass of \Timber\Site
 */

class Bootstrap extends \Timber\Site {

	/** Add timber support. */
	public function __construct() {

		/**
		 * Sets globals
		 */

    $this->set_globals();
        
    /**
		* Sets admin
		*/

    if(is_admin() || $GLOBALS['pagenow'] === 'wp-login.php'){
        $this->lib_ui_admin();
		}
		
		/**
		 * Sets the directories (inside your theme) to find .twig files
		 */
		if ( NODE_ENV == 'development'  ) {
			$modules_dirs = glob(get_stylesheet_directory().'/src/modules/*', GLOB_ONLYDIR);
			foreach($modules_dirs as &$dir){
				$dir = 'src/'.explode("/src/", $dir)[1];
			}
			\Timber\Timber::$dirname = array_merge( array('src/templates'), $modules_dirs);
		} else {
			\Timber\Timber::$dirname = array( 'dist');
		}

		/**
		 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
		 * No prob! Just set this value to true
		 */
		\Timber\Timber::$autoescape = false;

		add_filter('show_admin_bar', '__return_false');

		parent::__construct();

		add_filter( 'body_class', array( $this,'add_slug_body_class') );
		add_filter( 'timber_context', array( $this, 'add_site_to_context' ) );
	}


	/** Sets globals and page templates from config/*.ini files */
		
	private function set_globals(){
		$init_files = array_merge(glob(realpath(dirname(__DIR__))."/dist/*.ini"), glob(realpath(dirname(__DIR__))."/**/config/*.ini"));
		foreach ($init_files as $key => $filename) {
			$FILE = strtoupper(preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($filename)));
			
			if($FILE == 'PAGES' || $FILE == 'PAGE_TEMPLATES'){
				$GLOB = parse_ini_file($filename);
				PageTemplater::init($GLOB);
			} else {
				$GLOB = parse_ini_file($filename, true);
				foreach($GLOB as $KEY => $GLOBAL){
					define($KEY,$GLOBAL);
				}
			}
		}

		define( 'THEME_DIRECTORY', get_template_directory() );

    }

    /** Set Admin */
		
    private function lib_ui_admin(){

		if(ADMIN_UI){

			if(ADMIN_UI['footer_message']){
				add_filter('admin_footer_text', function() {
 
					echo ADMIN_UI['footer_message'];
					 
				});
			}

		}
	
		require __DIR__  . '/fancy-admin-ui/fancy-admin-ui.php';
        
	}

	/** 
	 * 
	 * Add post/page slug to body class
	 * 
	 */

	public function add_slug_body_class( $classes ) {
		global $post;
		if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
		$classes[] = $post->post_name;
		}
		return $classes;
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_site_to_context( $context ) {

		$context['site'] = $this;

		if ( NODE_ENV == 'development'  ) {
			$context['theme']->images = $context['theme']->link.'/src/assets/images';
		} else {
			$context['theme']->images = $context['theme']->link.'/dist';
		}

		return $context;
	}

}