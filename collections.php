<?php
/*
Plugin Name: Collections Plugin
Plugin URI: https://github.com/webbouwer
Description: Collections plugin for Wordpress

Suggestion to use with
https://wordpress.org/plugins/taxonomy-terms-order
https://wordpress.org/plugins/admin-taxonomy-filter/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path(__FILE__) . 'function/taxonomy_types.php');
require_once(plugin_dir_path(__FILE__) . 'function/taxonomy_collection.php');
require_once(plugin_dir_path(__FILE__) . 'function/posttype_artifact.php');

require_once(plugin_dir_path(__FILE__) . 'function/page_templates.php');
require_once(plugin_dir_path(__FILE__) . 'function/page_options.php');


// artifact collection default loop template
function taxonomy_template( $template ){
 	if( is_tax('collection')){
 	    $template = dirname( __FILE__ ) .'/template/taxonomy-collection.php';
 	}
 	return $template;
}
add_filter('template_include', 'taxonomy_template');

// artifact default single template
function single_post_template($single_template) {
 	global $post;
 	if ( $post->post_type == 'artifact' ) {
 		$single_template = dirname( __FILE__ ) . '/template/posttype-artifact.php';
 	}
 	return $single_template;
}
add_filter( 'single_template', 'single_post_template' ) ;


// setup plugin Class
function plugconstruct() {
	return new CollectionsPluginClass();
}
add_action( 'init', 'plugconstruct' );

class CollectionsPluginClass{

	function __construct() {
		add_action('rest_api_init', array( $this, 'register_rest_artifacts') );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_ajax_page_collection_load', array( $this, 'ajax_callback_page_collection_load_function' ) );
	}

	// load javascript files
	public function load_scripts() {
		  wp_register_script( 'page_collection_load',
				plugins_url( 'js/ajax_page_collection.js', __FILE__ ),
				array( 'jquery' )
			);

			wp_localize_script( 'page_collection_load', 'params', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			) );
			wp_enqueue_script( 'page_collection_load' );
	}


	// AJAX: add custom post type to rest (wp-json)
	// https://weichie.com/blog/ajax-load-more-posts-wordpress/
	public function register_rest_artifacts(){
	  register_rest_route( 'artifacts', 'all-posts', array(
	    'methods' => 'GET',
	    'callback' => array( $this, 'load_more_artifacts_callback'),
	    'permission_callback' => '__return_true'
	  ));
	}
	// AJAX: load collection artifacts function
	public function load_more_artifacts_callback($request){

	    $posts_data = array();
			$params = $request->get_query_params();
			$paged  = $params['page'];
			$amount  = $params['load_amount'];
			$term  = $params['collection'];

	    $paged = (isset($paged) || !(empty($paged))) ? $paged : 1;
	    $posts = get_posts( array(
	      'post_type'       => 'artifact', // $posttype
	      'status'          => 'published',
	      'posts_per_page'  => $amount,
	      'orderby'         => 'post_date',
	      'order'           => 'DESC',
	      'paged'           => $paged,
	      'tax_query' => array(
	        array(
	          'taxonomy' => 'collection', // $taxname
	          'field' => 'slug',
	          'terms' => $term // $term_slug
	        )
	      )
	    ));
	    foreach($posts as $post){

	      $id = $post->ID;

				// add post related data queries (types etc.)
				$post_url = get_the_permalink($id);
	      $post_thumbnail = (has_post_thumbnail($id)) ? get_the_post_thumbnail_url($id) : null;
	      $post_cat = get_the_category($id); // TODO: get collection(s)
	      //$featured = (get_field('project_featured', $id)) ? true : false;
	      $posts_data[] = (object)array(
	        'id' => $id,
	        'slug' => $post->post_name,
	        'url' => $post_url,
	        'type' => $post->post_type,
	        'title' => $post->post_title,
	        'featured_img_src' => $post_thumbnail,
	        //'featured' => $featured,
	        'category' => $post_cat[0]->cat_name
	      );

				
	    }
	    return $posts_data;

			wp_reset_query();
	}

}








/* https://pagely.com/blog/creating-custom-shortcodes/
add_shortcode( 'collection', 'display_collection');

function display_collection( $atts = array() ) {

    // set up default parameters
    extract(shortcode_atts(array(
     'id' => 0, // 0 = all, or specific id
		 'view' => 'full', // full = full list including filter and display menu's, simple = only a list
    ), $atts));

    return "<img src=\"http://dayoftheindie.com/wp-content/uploads/$rating-star.png\"
    alt=\"doti-rating\" width=\"130\" height=\"188\" class=\"left-align\" />";
}
*/
