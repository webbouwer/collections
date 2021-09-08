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
	protected $getdata;

	public function __construct() {

		add_filter( 'template_include', array( $this, 'taxonomy_template' ) );
		add_filter( 'single_template', array( $this, 'single_post_template' ) );


    add_action( 'wp_enqueue_scripts', array( $this, 'artifact_ajax_script' ) );

		include(plugin_dir_path(__FILE__) . 'functions/settings.php');

		$this->settings = new CollectionsSettings();

		$this->taxonomy_includes();

	}

	public function taxonomy_includes(){
/*
			global $post;
			if( is_page() ){
				$template = get_page_template_slug( $post->ID );
			}
			$display_option = get_option( 'dropdown_option_setting_option_name' ); // Array
			$viewtype =  $display_option['dropdown_option_0'];

			if( $viewtype == 'grid' || $template == '../views/collection-page.php' ){*/
				include(plugin_dir_path(__FILE__) . 'functions/collection_ajax.php');
				$this->getdata = new CollectionsAjaxGrid();
			//}

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

	public function artifact_ajax_script() {
		if ( get_query_var( 'post_type' ) == 'artifact' ) {
			wp_enqueue_script( 'artifact-view', plugins_url( 'js/artifact_view.js', __FILE__ ), array( 'jquery' ), null, true  );
		}
	}


}


// We add the action twice, once for logged in users and once for non logged in users.
add_action( 'wp_ajax_artifact_view', 'artifact_view_callback' );
add_action( 'wp_ajax_nopriv_artifact_view', 'artifact_view_callback' );

// Enqueue the script on the front end.
add_action( 'wp_enqueue_scripts', 'enqueue_artifact_view_script' );
// Enqueue the script on the back end (wp-admin)
add_action( 'admin_enqueue_scripts', 'enqueue_artifact_view_script' );

function artifact_view_callback() {
		$json = array();

		if ( isset( $_REQUEST['id'] ) ) {
			 $id = $_REQUEST['id'];
			 $post = get_post( $id );
			 //$json['postdata'] = $post;
			 $image_orientation = 'portrait';
			 $image = wp_get_attachment_image_src( get_post_thumbnail_id($id), '');
			 $image_w = $image[1];
			 $image_h = $image[2];

			 if ($image_w > (2.3 * $image_h) ) {
				 $image_orientation = 'panorama';
			 }else if ($image_w > $image_h) {
				 $image_orientation = 'landscape';
			 }else if ($image_w == $image_h) {
				 $image_orientation = 'square';
			 }else {
				 $image_orientation = 'portrait';
			 }

			 $json['postdata'] = array(
				 'title'=>$post->post_title,
				 'slug'=>$post->post_name,
				 'excerpt'=>$post->post_excerpt,
				 'content'=>$post->post_content,
				 'image'=>$image[0],
				 'orientation'=>$image_orientation,
				 'link'=>$post->guid,
			 );



			 // list in array per type
			 $json['postmedia'] = array();
			 $media = get_attached_media( '', $id );
			 foreach($media as $element) {
				 $terms = wp_get_post_terms( $element->ID, array( 'types' ) );
				 $json['postmedia'][$element->ID] = array(
					 'title'=>$element->post_title,
					 'excerpt'=>$element->post_excerpt,
					 'src'=>$element->guid,
					 'type_parent'=> $terms[0]->parent,
					 'type_slug'=> $terms[0]->slug,
					 'type_name'=> $terms[0]->name,
				 );
			 }
			 //$json['postmedia'] = json_encode( $media );


			 wp_send_json_success( $json );
		}
}
