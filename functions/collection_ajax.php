<?php
/**/


class CollectionsAjaxGrid{

	public function __construct() {

    //Localize the AJAX URL and Nonce
    add_action('wp_enqueue_scripts', array( $this, 'collections_localize_ajax' ) );

    // Enqueue javascript script + CSS on the front end.
    add_action( 'wp_enqueue_scripts', array( $this, 'collections_ajax_script' ) );
    // Enqueue the script on the back end (wp-admin)
    add_action( 'admin_enqueue_scripts', array( $this, 'collections_ajax_script' ) );

    // assign php function for ajax request
    add_action('wp_ajax_getCollectionData', array( $this, 'getCollectionData' ) );
    add_action('wp_ajax_nopriv_getCollectionData', array( $this, 'getCollectionData' ) );

  }

  public function collections_localize_ajax(){
      wp_localize_script('jquery', 'ajax', array(
          'url' => admin_url('admin-ajax.php'),
          'nonce' => wp_create_nonce('example_ajax_nonce'),
      ));
  }

  public function collections_ajax_script() {

    // when time to add scripts check taxonomy
    $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

    //if ( get_query_var( 'taxonomy' ) == 'collection' ) {

      wp_enqueue_script( 'ajax-script', plugins_url( '../js/collection_ajax.js', __FILE__ ), array( 'jquery' ), null, true );
      wp_localize_script( 'ajax-script', 'ajax_data', array(
          'ajaxurl' => admin_url( 'admin-ajax.php' ),
      ) );
  		wp_enqueue_script( 'imagesloaded-package', plugins_url( '../lib/javascript/imagesloaded.js', __FILE__ ), false );
  		wp_enqueue_script( 'isotope-package', plugins_url( '../lib/javascript/isotope.pkgd.min.js', __FILE__ ), false );
  		wp_enqueue_style( 'collection-page-style', plugins_url( '../css/collection_ajax.css', __FILE__ ) );

    //}
  }

  public function getImageOrient( $media_id ){

    $thumb_orientation = 'portrait';
    $image = wp_get_attachment_image_src( $media_id , '');
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
    return $thumb_orientation;

  }


  public function getCollectionData(){

    if( !wp_verify_nonce($_POST['nonce'], 'example_ajax_nonce') ){
  	   die('Permission Denied.');
  	}

  	$posts_data = array();

  	$paged  = $_POST['data']['page'];
    $posttype  = $_POST['data']['posttype'];
  	$taxname = $_POST['data']['taxname'];
  	$term  = $_POST['data']['slug'];
  	$orderby  = $_POST['data']['orderby'];
  	$order = $_POST['data']['order'];
    $amount  = $_POST['data']['ppp'];
    $paged = (isset($paged) || !(empty($paged))) ? $paged : 1;

  	$get_post_args = array(
  	   'post_type'       => $posttype,
  		 'status'          => 'published',
       'posts_per_page'  => $amount,
       'orderby'         => $orderby, // 'menu_order', // date // menu_order slug
  		 'order'           => $order, //'ASC', // desc
			 'suppress_filters' => true, // remove plugin ordenings
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
  		$postlist = array();
  		$types_used = array();

      while($collection_posts->have_posts()) : $collection_posts->the_post();

        $ID = get_the_ID();
				$post = get_post($ID);
				$slug = $post->post_name;
  			$media = get_attached_media( '', $ID );
  		  $type_classes = array();
  			$type_count = array();
  			$classes = '';
  			$html = '';

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
        $thumb_orientation = $this->getImageOrient( get_post_thumbnail_id($ID) );

				// featured
				$featured = '';
				if( $ID == 30 ){
					$featured = 'featured '; 
				}

				$baseclasses = esc_attr( implode( ' ', get_post_class( '', $ID ) ) );

  			$html = '<div id="post-'.$ID.'" class="post-artifact post '.get_post_class().' '.$featured.''.$thumb_orientation.' '.$classes.' '.$baseclasses.'" data-id="'.$ID .'" data-slug="'.$slug.'"><div class="innerpadding">';

  			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
  			     $html .= '<div class="artifact-image"><img src="'.get_the_post_thumbnail_url().'" class="attachment-normal size-normal wp-post-image" alt="" loading="lazy" /></div>';
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
  			$html .= '</div>';
  			$html .= '</div>';

  			$postlist[] = array(
  			    'id' => $ID,
  					'html' => $html
        );
      endwhile;
  	endif;

    /* output */
    header('Content-Type: application/json');
  	print json_encode($postlist);

    wp_reset_query();
  	wp_die();

  }

}
new CollectionsAjaxGrid();
