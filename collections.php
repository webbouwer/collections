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



add_filter('template_include', 'taxonomy_template');
function taxonomy_template( $template ){

	if( is_tax('collection')){
	    $template = dirname( __FILE__ ) .'/template/taxonomy-collection.php';
	}
	return $template;

}

// https://stackoverflow.com/questions/19328475/adding-custom-page-template-from-plugin
add_filter( 'single_template', 'single_post_template' ) ;
function single_post_template($single_template) {

	global $post;
	if ( $post->post_type == 'artifact' ) {
		$single_template = dirname( __FILE__ ) . '/template/posttype-artifact.php';
	}
	return $single_template;
	
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
