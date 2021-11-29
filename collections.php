<?php
/*
Plugin Name: Collections Plugin
Plugin URI: https://webbouwer.org
Description:  Wordpress plugin for artifact collections
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path(__FILE__) . 'core/taxonomy_types.php');
require_once(plugin_dir_path(__FILE__) . 'core/taxonomy_collection.php');
require_once(plugin_dir_path(__FILE__) . 'core/posttype_artifact.php');

require_once(plugin_dir_path(__FILE__) . 'functions/page_templates.php');
require_once(plugin_dir_path(__FILE__) . 'functions/page_options.php');

function pluginconstruct() {
	return new collectionsMain();
}
add_action( 'init', 'pluginconstruct' );

/*
add_action( 'pre_get_posts', function( $query) {
		if ( $query->is_tax( 'collection' ) ) { // Replace with the name of the taxonomy you want to target
				$query->set( 'posts_per_page', 3 ); // change '6' to the number of posts you want to appear
		}
} );
*/

class collectionsMain {

	protected $settings;

	public function __construct() {

		add_filter( 'template_include', array( $this, 'taxonomy_template' ) );
		add_filter( 'single_template', array( $this, 'single_post_template' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'artifact_ajax_script' ) );

		include(plugin_dir_path(__FILE__) . 'functions/settings.php');

		$this->settings = new CollectionsSettings();

		$this->taxonomy_includes();

	}

	public function taxonomy_includes(){

			// TODO: make this load on template pages only..
			include(plugin_dir_path(__FILE__) . 'functions/collection_ajax.php');
			include(plugin_dir_path(__FILE__) . 'functions/artifact_ajax.php');

			/*
			global $post;
			if( is_page() ){
				$template = get_page_template_slug( $post->ID );
			}
			$display_option = get_option( 'dropdown_option_setting_option_name' ); // Array
			$viewtype =  $display_option['dropdown_option_0'];

			if( $viewtype == 'grid' || $template == '../views/collection-page.php' ){
				include(plugin_dir_path(__FILE__) . 'functions/collection_ajax.php');
				include(plugin_dir_path(__FILE__) . 'functions/artifact_ajax.php');

			//}*/


	}

	public function taxonomy_template( $template ){
		if( is_tax('collection')){
			$template = dirname( __FILE__ ) .'/views/taxonomy-collection.php';
		}
		return $template;
	}

	public function single_post_template($single_template) {
		global $post;
		if ( $post->post_type == 'artifact' ) {
			$single_template = dirname( __FILE__ ) . '/views/posttype-artifact.php';
		}
		return $single_template;
	}

}
