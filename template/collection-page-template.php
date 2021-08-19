<?php
/**
 * Template Name: Collection Overview
 * Description: Page Theme custom taxonomy and post file
 */

 get_header();


$posttype = 'artifact';
$taxname = 'collection';

$values = get_post_custom( get_the_ID() );

$slug = isset( $values['page_collection_selectbox'] ) ? $values['page_collection_selectbox'][0] : 'Uncategorized';
$load_amount = isset( $values['page_collection_loadamount'] ) ? $values['page_collection_loadamount'][0] : 12;

// echo $slug;

// AJAX https://weichie.com/blog/ajax-load-more-posts-wordpress/
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$get_post_args = array(
  'post_type' => $posttype, // Your Post type Name that You Registered
  'status'         => 'published',
	'posts_per_page' => $load_amount,
	'orderby'	       => 'post_date',
	'order'          => 'DESC',
	'paged'          => $paged,
  'tax_query' => array(
    array(
      'taxonomy' => $taxname,
      'field' => 'slug',
      'terms' => $slug
    )
  )
);

$collection_posts = new WP_Query($get_post_args);

if($collection_posts->have_posts()) :
    echo '<div id="artifact-list" data-collection="'.$slug.'" data-loadamount="'.$load_amount.'">';

    /*
    while($collection_posts->have_posts()) : $collection_posts->the_post();
    if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
        the_post_thumbnail( 'thumb' );
    }
    echo '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
    echo apply_filters('the_excerpt', get_the_excerpt()); // the_excerpt_length( 32 );
    endwhile;
    */
    echo '</div>';
endif;

if($collection_posts->post_count < $load_amount){
 	echo '<div class="load-more" class="btn secondary-button">no more artifacts</a></div>';
}else{
 	echo '<div id="artifact-loader" class="loading-banner"><a class="btn" href="#!">Loading</a></div>';
}



 get_footer();
