<?php

namespace Lib;
use \Timber\Timber;

/**
 * Renders the right twig file
 */

class Controller {

    /**
    *  Hierarchy of templates to render
    */

    protected $templates;


    /**
    *  Data to render
    */

    protected $context;



    /**
    *  WP Controlled view
    */

    public function __construct() {

       
        $this->context = Timber::get_context();


        if(is_archive()){

            $this->controller_archive();
        
        } else if(is_front_page() || is_home()){
            
            if(is_home() && !is_front_page()){
                $this->controller_page();
            } else {
                $this->controller_front_page();
            }
        
        } else if(is_404()){
        
            $this->controller_error404();
        
        } else if(is_author()){
           
            $this->controller_author();
        
        } else if(is_search()){

            $this->controller_search();

        } else if(is_page()){

            $this->controller_page();

        } else if(is_single()){

            $this->controller_single();

        } 

    }


     /**
    * Custom methods on extend
    *
    *
    * @package  WordPress
    * @subpackage  Timber
    * @since   Timber 0.1
    */

    public function custom_method($method){
        if( method_exists($this,$method) ){
            $this->{$method}();
        }
    }


    /**
    * Render the controlled view
    *
    * Methods for TimberHelper can be found in the /lib sub-directory
    *
    * @package  WordPress
    * @subpackage  Timber
    * @since   Timber 0.1
    */


    public function render(){

        \Timber\Timber::render( $this->templates, $this->context );

    }


    /**
    * The main template file
    * This is the most generic template file in a WordPress theme
    * and one of the two required files for a theme (the other being style.css).
    * It is used to display a page when nothing more specific matches a query.
    * E.g., it puts together the home page when no home.php file exists
    *
    * Methods for TimberHelper can be found in the /lib sub-directory
    *
    * @package  WordPress
    * @subpackage  Timber
    * @since   Timber 0.1
    */

    public function controller_front_page(){


        $templates = array( 'front-page.twig', 'home.twig',  'index.twig' );
        $this->templates = $templates;
        
        if ( !is_home() ) { // not blog home archive posts
            
            array_unshift( $templates, 'page-home.twig' );
            $this->controller_page($templates);
            $this->custom_method('page_home');

        }

        $this->custom_method('front_page');
        $this->custom_method('home');
        $this->custom_method('index');

    }



    /**
     * The template for displaying all pages.
     *
     * This is the template that displays all pages by default.
     * Please note that this is the WordPress construct of pages
     * and that other 'pages' on your WordPress site will use a
     * different template.
     *
     * To generate specific templates for your pages you can use:
     * /mytheme/views/page-mypage.twig
     * (which will still route through this PHP file)
     * OR
     * /mytheme/page-mypage.php
     * (in which case you'll want to duplicate this file and save to the above path)
     *
     * Methods for TimberHelper can be found in the /lib sub-directory
     *
     * @package  WordPress
     * @subpackage  Timber
     * @since    Timber 0.1
     */

    public function controller_page($templates = array()){

        $post = new \Timber\Post();

        array_unshift( $templates, 'page-' . $post->post_name . '.twig');

        $template_slug = get_page_template_slug();

        if( $template_slug &&  $template_slug != ''){
            array_push($templates,  'page-' . $template_slug . '.twig');
        }

        array_push($templates,  'page.twig');

        $this->context['post'] = $post;
        $this->context['page'] = $post;
        $this->templates = $templates;

        $method = str_replace('-','_',$template_slug);
        

        $this->custom_method('page');
        $this->custom_method('page_'. $method);

    }


    /**
    * The template for displaying Archive pages.
    *
    * Used to display archive-type pages if nothing more specific matches a query.
    * For example, puts together date-based pages if no date.php file exists.
    *
    * Learn more: http://codex.wordpress.org/Template_Hierarchy
    *
    * Methods for TimberHelper can be found in the /lib sub-directory
    *
    * @package  WordPress
    * @subpackage  Timber
    * @since   Timber 0.2
    */

	public function controller_archive(){


        $templates = array( 'archive.twig', 'index.twig' );

        $this->context['title'] = 'Archive';
        if ( is_day() ) {
            $this->context['title'] = 'Archive: ' . get_the_date( 'D M Y' );
        } else if ( is_month() ) {
            $this->context['title'] = 'Archive: ' . get_the_date( 'M Y' );
        } else if ( is_year() ) {
            $this->context['title'] = 'Archive: ' . get_the_date( 'Y' );
        } else if ( is_tag() ) {
            $this->context['title'] = single_tag_title( '', false );
        } else if ( is_category() ) {
            $this->context['title'] = single_cat_title( '', false );
            array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
        } else if ( is_post_type_archive() ) {
            $this->context['title'] = post_type_archive_title( '', false );
            array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
        }

        $this->context['is_archive'] = true;

        $this->context['posts'] = new \Timber\PostQuery();

        $this->templates = $templates;

        $this->custom_method('archive');

        if(is_tax()){

            $this->custom_method('archive_taxonomy');

        } if(is_post_type_archive()){
            $this->custom_method('archive_'.str_replace("-","_",get_post_type()));
        }

    }

    /**
     * The Template for displaying all single posts
     *
     * Methods for TimberHelper can be found in the /lib sub-directory
     *
     * @package  WordPress
     * @subpackage  Timber
     * @since    Timber 0.1
     */

    public function controller_single(){
        global $post;
        $this->context['post'] = \Timber\Timber::query_post();


        if ( post_password_required( $post->ID ) ) {
            $templates = 'single-password.twig';
        } else {
            $templates = array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' );
        }

        $this->templates = $templates;

        $this->custom_method('single');
        
        $this->custom_method('single_'. str_replace("-","_",$post->post_type));

    }


    /**
     * The template for displaying 404 pages (Not Found)
     *
     * Methods for TimberHelper can be found in the /functions sub-directory
     *
     * @package  WordPress
     * @subpackage  Timber
     * @since    Timber 0.1
     */

    public function controller_error404(){

        $this->templates = '404.twig';
        
        $this->custom_method('error404');
    }


     /**
     * The template for displaying Author Archive pages
     *
     * Methods for TimberHelper can be found in the /lib sub-directory
     *
     * @package  WordPress
     * @subpackage  Timber
     * @since    Timber 0.1
     */

    public function controller_author(){

        global $wp_query;
        $this->context['posts'] = new \Timber\PostQuery();
        if ( isset( $wp_query->query_vars['author'] ) ) {
            $author            = new \Timber\User( $wp_query->query_vars['author'] );
            $this->context['author'] = $author;
            $this->context['title']  = 'Author Archives: ' . $author->name();
        }
        $this->templates = array( 'author.twig', 'archive.twig' );

        $this->custom_method('author');

    }


    /**
     * Search results page
     *
     * Methods for TimberHelper can be found in the /lib sub-directory
     *
     * @package  WordPress
     * @subpackage  Timber
     * @since   Timber 0.1
     */


    public function controller_search(){

        $this->context = array( 'search.twig', 'archive.twig', 'index.twig' );
        $this->context['title'] = 'Search results for ' . get_search_query();
        $this->context['posts'] = new \Timber\PostQuery();

        $this->custom_method('search');

    }


}
