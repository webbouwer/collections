<?php
/**/


class ArtifactAjaxView{

	public function __construct() {


	// We add the action twice, once for logged in users and once for non logged in users.
	add_action( 'wp_ajax_artifact_view',  array( $this,'artifact_view_callback' ) );
	add_action( 'wp_ajax_nopriv_artifact_view',  array( $this,'artifact_view_callback') );

	// Enqueue the script on the front end.
	add_action( 'wp_enqueue_scripts',  array( $this,'enqueue_artifact_view_script' ) );
	// Enqueue the script on the back end (wp-admin)
	add_action( 'admin_enqueue_scripts',  array( $this,'enqueue_artifact_view_script' ) );

	}



	public function artifact_view_callback() {



			$json = array();

			if ( isset( $_REQUEST['id'] ) ) {
				 $id = $_REQUEST['id'];
				 $post = get_post( $id );
				 //$json['postdata'] = $post;

				 $image = wp_get_attachment_image_src( get_post_thumbnail_id($id), '');

				 $image_orientation = 'portrait';

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

}
new ArtifactAjaxView();
