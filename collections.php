<?php
/*
Plugin Name: Collections Plugin
Plugin URI: https://webbouwer.org
Description:  Wordpress plugin for artifact collections
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path(__FILE__) . 'core/taxonomy_collection.php');
require_once(plugin_dir_path(__FILE__) . 'core/taxonomy_types.php');
require_once(plugin_dir_path(__FILE__) . 'core/wp_dropdown_posts.php');
require_once(plugin_dir_path(__FILE__) . 'core/posttype_artifact.php');

require_once(plugin_dir_path(__FILE__) . 'functions/page_templates.php');
require_once(plugin_dir_path(__FILE__) . 'functions/page_options.php');


// https://wordpress.stackexchange.com/questions/204779/how-can-i-add-an-author-filter-to-the-media-library
/*
function media_add_author_dropdown()
{
    $scr = get_current_screen();
    if ( $scr->base !== 'upload' ) return;

    $author   = filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING );
    $selected = (int)$author > 0 ? $author : '-1';
    $args = array(
        'show_option_none'   => 'All Authors',
        'name'               => 'author',
        'selected'           => $selected
    );
    wp_dropdown_users( $args );
}
add_action('restrict_manage_posts', 'media_add_author_dropdown');


function author_filter($query) {
    if ( is_admin() && $query->is_main_query() ) {
        if (isset($_GET['author']) && $_GET['author'] == -1) {
            $query->set('author', '');
        }
    }
}
add_action('pre_get_posts','author_filter');
*/



function media_belongs_to_post_dropdown()
{
    $scr = get_current_screen();
    if ( $scr->base !== 'upload' ) return;

    $post_parent   = filter_input(INPUT_GET, 'post_parent', FILTER_SANITIZE_STRING );
    $selected = (int)$post_parent > 0 ? $post_parent : '-1';
    $args = array(
        'show_option_all'   => 'All Artifacts',
				'echo' 							=> 1,
        'select_name'       => 'post_parent',
        'selected'          => $selected
    );

    wp_dropdown_posts( $args );
}
add_action('restrict_manage_posts', 'media_belongs_to_post_dropdown');


function parent_filter($query) {
    if ( is_admin() && $query->is_main_query() ) {
        if (isset($_GET['post_parent']) && $_GET['post_parent'] == -1) {
            $query->set('post_parent', '');
        }
    }
}
add_action('pre_get_posts','parent_filter');


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

	public function taxonomy_includes(){

			// TODO: make this load on template pages only..
			//global $post;
			//if ( is_page_template('collection-page.php') ) {
				include(plugin_dir_path(__FILE__) . 'functions/collection_ajax.php');
				include(plugin_dir_path(__FILE__) . 'functions/artifact_ajax.php');
			//}
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

}
