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

		//require_once(plugin_dir_path(__FILE__) . 'functions/settings.php');
		//new MySettingsPage();

function pluginconstruct() {
	return new collectionsMain();
}
add_action( 'init', 'pluginconstruct' );


class collectionsMain {

	protected $settings;

	public function __construct() {

		add_filter( 'template_include', array( $this, 'taxonomy_template' ) );
		add_filter( 'single_template', array( $this, 'single_post_template' ) );

		include(plugin_dir_path(__FILE__) . 'functions/settings.php');
		$this->settings = new CollectionsSettings();

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
