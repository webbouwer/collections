<?php
/*
Plugin Name: Collections Plugin
Plugin URI: https://github.com/webbouwer
Description: Collections plugin for Wordpress

Suggestion to use with
https://wordpress.org/plugins/taxonomy-terms-order
https://wordpress.org/plugins/admin-taxonomy-filter/
https://weichie.com/blog/ajax-load-more-posts-wordpress/
https://github.com/webbouwer/imagazine/blob/development/functions.php
https://github.com/webbouwer/treasure/blob/setup03_markupoverlays/html/content-overview.php
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
		add_filter('template_include', 'taxonomy_template' );

		// artifact default single template
		function single_post_template($single_template) {
			 	global $post;
			 	if ( $post->post_type == 'artifact' ) {
			 		$single_template = dirname( __FILE__ ) . '/template/posttype-artifact.php';
			 	}
			 	return $single_template;
		}
		add_filter( 'single_template', 'single_post_template' ) ;


//Localize the AJAX URL and Nonce
add_action('wp_enqueue_scripts', 'example_localize_ajax');
function example_localize_ajax(){
    wp_localize_script('jquery', 'ajax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('example_ajax_nonce'),
    ));
}
// Enqueue javascript script on the front end.
add_action( 'wp_enqueue_scripts', 'enqueue_ajax_script' );
// Enqueue the script on the back end (wp-admin)
add_action( 'admin_enqueue_scripts', 'enqueue_ajax_script' );

function enqueue_ajax_script() {
    wp_enqueue_script( 'ajax-script', plugins_url( 'js/ajax_page_collection.js', __FILE__ ), array( 'jquery' ), null, true );
    wp_localize_script( 'ajax-script', 'ajax_data', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ) );
		wp_enqueue_script( 'imagesloaded-package', plugins_url( 'js/imagesloaded.js', __FILE__ ), false );
		wp_enqueue_script( 'isotope-package', plugins_url( 'js/isotope.pkgd.min.js', __FILE__ ), false );
		wp_enqueue_style( 'collection-page-style', plugins_url( 'css/ajax_page_collection.css', __FILE__ ) );
}



	// Example AJAX Function
	add_action('wp_ajax_getCollectionData', 'getCollectionData');
	add_action('wp_ajax_nopriv_getCollectionData', 'getCollectionData');

	function getCollectionData(){

			if ( !wp_verify_nonce($_POST['nonce'], 'example_ajax_nonce') ){
	        die('Permission Denied.');
	    }

				    $posts_data = array();

						$paged  = $_POST['data']['page'];

						$posttype  = $_POST['data']['posttype'];
						$taxname = $_POST['data']['taxname'];
						$term  = $_POST['data']['slug'];

						$amount  = $_POST['data']['ppp'];


				    $paged = (isset($paged) || !(empty($paged))) ? $paged : 1;

						$get_post_args = array(
				      'post_type'       => $posttype,
				      'status'          => 'published',
				      'posts_per_page'  => $amount,
				      'orderby'         => 'post_date',
				      'order'           => 'DESC',
				      'paged'           => $paged,
				      'tax_query' => array(
				        array(
				          'taxonomy' => $taxname, // $taxname
				          'field' => 'slug',
				          'terms' => $term // $term_slug
				        )
				      )
				    );

					$collection_posts = new WP_Query($get_post_args);


					if($collection_posts->have_posts()) :

					  // media type taxonmies for each post artifact id
					  $artifacts = array();
					  $types_used = array();
						$output ='';

					  while($collection_posts->have_posts()) : $collection_posts->the_post();

					      $type_classes = array();
					      $type_count = array();
					      $classes = '';

					      $artID = get_the_ID();
								$html = '';
					      $media = get_attached_media( '', $artID );

					      foreach($media as $element) {

					        $terms = wp_get_post_terms( $element->ID, array( 'types' ) );

					        foreach ( $terms as $term ) :
					          $type_count[$term->slug]++;
					          if( !in_array( $term->slug, $type_classes ) ){
					            $type_classes[$term->slug] = $term->slug;
					          }
					          if( !in_array( $term->slug, $types_used ) ){
					            $types_used[$term->slug] = $term->name;
					          }

					        endforeach;
					      }
					      $classes = implode(" ", $type_classes);

					      $thumb_orientation = 'portrait';
					      $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), '');
					      $image_w = $image[1];
					      $image_h = $image[2];

					      if ($image_w > (2.3 * $image_h) ) {
					        $thumb_orientation = 'panorama';
					      }else if ($image_w > $image_h) {
					        $thumb_orientation = 'landscape';
					      }else if ($image_w == $image_h) {
					        $thumb_orientation = 'square';
					      }else {
					        $thumb_orientation = 'portrait';
					      }

					      $html = '<div class="post-artifact '.$thumb_orientation.' '.$classes.'" data-id="'.$artID .'"><div class="innerpadding">';

					      if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
					        $html .= '<img src="'.get_the_post_thumbnail_url().'" class="attachment-normal size-normal wp-post-image" alt="" loading="lazy" />';
					      }
					      $html .= '<div class="overlay">';
					      $html .= '<h2 class="entry-title" itemprop="headline"><a href="'.get_the_permalink().'" class="entry-title-link">'.get_the_title().'</a></h2>';
					      $html .= '<div class="item-icons"><ul>';
					      foreach ( $types_used as $slug => $type ) :
					        if( $type_count[$slug] != '' ){
					          $html .= '<li data-type="'.$slug.'" class="icon-button but-'.$slug.'"><span>'.$type.'('.$type_count[$slug].')</span></li>';
					        }
					      endforeach;
					      $html .= '</ul></div>';
					      $html .= '<div class="item-excerpt">'.get_the_excerpt().'</div>';
					      $html .=  '</div></div></div>';

								$artifacts[] = array(
									'id' => $artID,
									'html' => $html
								);

					    endwhile;

					endif;

					header('Content-Type: application/json');
					print json_encode($artifacts);



					wp_reset_query();
					wp_die();

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
						$image = wp_get_attachment_image_src( get_post_thumbnail_id($id), '');
						$json['postdata'] = array(
							'title'=>$post->post_title,
							'excerpt'=>$post->post_excerpt,
							'content'=>$post->post_content,
							'image'=>$image[0],
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



		/*
			// Example AJAX Function
			add_action('wp_ajax_example_function', 'example_function');
			add_action('wp_ajax_nopriv_example_function', 'example_function');

			function example_function(){
			    if ( !wp_verify_nonce($_POST['nonce'], 'example_ajax_nonce') ){
			        die('Permission Denied.');
			    }

			    $firstname = sanitize_text_field($_POST['data']['taxname']);
			    $lastname = sanitize_text_field($_POST['data']['page']);

			    //Do something with data here
			    echo $firstname . ' ' . $lastname; //Echo for response
			    wp_die(); // this is required to terminate immediately and return a proper response:- https://codex.wordpress.org/AJAX_in_Plugins
			}
		*/


/*
// setup plugin Class
function plugconstruct() {
	return new CollectionsPluginClass();
}
add_action( 'init', 'plugconstruct' );

class CollectionsPluginClass{

	function __construct() {

		// register wp json route (rest)
		add_action('rest_api_init', array( $this, 'register_rest_artifacts') );
		// register taxonomy templates
		add_filter('template_include', array( $this, 'taxonomy_template' ) );
		// register post templates
		add_filter( 'single_template', array( $this, 'single_post_template' ) ) ;
		// add javascript files
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		// add ajax function
		add_action( 'wp_ajax_page_collection_load', array( $this, 'ajax_callback_page_collection_load_function' ) );

	}

	// add custom post type to rest (wp-json)
	public function register_rest_artifacts(){
		register_rest_route( 'artifacts', 'all-posts', array(
			'methods' => 'GET',
			'callback' => array( $this, 'load_more_artifacts_callback'),
			'permission_callback' => '__return_true'
		));
	}

	// artifact collection default loop template
	public function taxonomy_template( $template ){
	 	if( is_tax('collection')){
	 	    $template = dirname( __FILE__ ) .'/template/taxonomy-collection.php';
	 	}
	 	return $template;
	}

	// artifact default single template
	public function single_post_template($single_template) {
	 	global $post;
	 	if ( $post->post_type == 'artifact' ) {
	 		$single_template = dirname( __FILE__ ) . '/template/posttype-artifact.php';
	 	}
	 	return $single_template;
	}



	// AJAX: load collection artifacts function
	// https://weichie.com/blog/ajax-load-more-posts-wordpress/
	public function load_more_artifacts_callback($request){

	    $posts_data = array();
			$params = $request->get_query_params();
			$paged  = $params['page'];
			$amount  = $params['load_amount'];
			$term  = $params['collection'];

	    $paged = (isset($paged) || !(empty($paged))) ? $paged : 1;

			$get_post_args = array(
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
	    );

		$collection_posts = new WP_Query($get_post_args);

		if($collection_posts->have_posts()) :

		  // media type taxonmies for each post artifact id
		  $artifacts = array();
		  $types_used = array();

		  while($collection_posts->have_posts()) : $collection_posts->the_post();

		      $type_classes = array();
		      $type_count = array();
		      $classes = '';

		      $artID = get_the_ID();
					$html = '';
		      $media = get_attached_media( '', $artID );

		      foreach($media as $element) {

		        $terms = wp_get_post_terms( $element->ID, array( 'types' ) );

		        foreach ( $terms as $term ) :
		          $type_count[$term->slug]++;
		          if( !in_array( $term->slug, $type_classes ) ){
		            $type_classes[$term->slug] = $term->slug;
		          }
		          if( !in_array( $term->slug, $types_used ) ){
		            $types_used[$term->slug] = $term->name;
		          }

		        endforeach;
		      }
		      $classes = implode(" ", $type_classes);

		      $thumb_orientation = 'portrait';
		      $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), '');
		      $image_w = $image[1];
		      $image_h = $image[2];

		      if ($image_w > (2.3 * $image_h) ) {
		        $thumb_orientation = 'panorama';
		      }else if ($image_w > $image_h) {
		        $thumb_orientation = 'landscape';
		      }else if ($image_w == $image_h) {
		        $thumb_orientation = 'square';
		      }else {
		        $thumb_orientation = 'portrait';
		      }

		      $html = '<div class="post-artifact '.$thumb_orientation.' '.$classes.'" data-id="'.$artID .'"><div class="innerpadding">';

		      if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
		        $html .= '<img src="'.get_the_post_thumbnail_url().'" class="attachment-normal size-normal wp-post-image" alt="" loading="lazy" />';
		      }
		      $html .= '<div class="overlay">';
		      $html .= '<h2 class="entry-title" itemprop="headline"><a href="'.get_the_permalink().'" class="entry-title-link">'.get_the_title().'</a></h2>';
		      $html .= '<div class="item-icons"><ul>';
		      foreach ( $types_used as $slug => $type ) :
		        if( $type_count[$slug] != '' ){
		          $html .= '<li data-type="'.$slug.'" class="icon-button but-'.$slug.'"><span>'.$type.'('.$type_count[$slug].')</span></li>';
		        }
		      endforeach;
		      $html .= '</ul></div>';
		      $html .= '<div class="item-excerpt">'.get_the_excerpt().'</div>';
		      $html .=  '</div></div></div>';


					$artifacts[] = (object)array(
						'id' => $artID,
						'html' => $html
					);

		    endwhile;

		endif;

		return $artifacts;
		wp_reset_query();
	}

}
	*/
