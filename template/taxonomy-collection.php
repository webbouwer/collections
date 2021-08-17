<?php

get_header(); // theme default header

echo '<div id="loopcontainer">';

$slug = get_query_var( 'term' );
$taxname = get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', $slug, $taxname );
if ($term->parent == 0) {

wp_list_categories('taxonomy=collection&depth=1&show_count=1&title_li=&child_of=' . $term->term_id);

} else {

$get_post_args = array(
  'post_type' => 'artifact', // Your Post type Name that You Registered
  'posts_per_page' => 999,
  'order' => 'ASC',
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

    while($collection_posts->have_posts()) : $collection_posts->the_post();
    if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
        the_post_thumbnail( 'thumb' );
    }
    echo '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
    echo apply_filters('the_excerpt', get_the_excerpt()); // the_excerpt_length( 32 );
    endwhile;

endif;

wp_link_pages();

wp_reset_query();

}

echo '</div>'; // end loopcontainer

get_footer();  // theme default footer


/* https://bryantwebdesign.com/code/wordpress-query-taxonomy-for-child-and-parent/
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); // get current term
$parent = get_term($term->parent, get_query_var('taxonomy') ); // get parent term
$children = get_term_children($term->term_id, get_query_var('taxonomy')); // get children
if(($parent->term_id!="" && sizeof($children)>0)) {
	// has parent and child
}elseif(($parent->term_id!="") && (sizeof($children)==0)) {
	// has parent, no child
}elseif(($parent->term_id=="") && (sizeof($children)>0)) {
	// no parent, has child
}
*/

/*
$collection_slug = get_queried_object()->slug;
$collection_name = get_queried_object()->name;

echo $collection_name .' (collection) tax template';

$get_post_args = array(
  'post_type' => 'artifact', // Your Post type Name that You Registered
  'posts_per_page' => 999,
  'order' => 'ASC',
  'tax_query' => array(
    array(
      'taxonomy' => 'collection',
      'field' => 'slug',
      'terms' => $collection_slug
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
      $classes = '';
      $artID = get_the_ID();

      //$artifact_media_types[ get_the_ID() ] = array();
      $media = get_attached_media( '', $artID ); //get_attached_media('image', the_ID() );

      foreach($media as $element) {
        //echo '<img src="'.wp_get_attachment_image_src($image->ID,'full').'" />';
        //print( '<pre>' . print_r($element) .'</pre>');
        $terms = wp_get_post_terms( $element->ID, array( 'types' ) );

        foreach ( $terms as $term ) :
          //echo '<p>'.$term->taxonomy . ': ';
          //echo $term->slug .'</p>';
          if( !in_array( $term->slug, $type_classes ) ){
            $type_classes[$term->slug] = $term->slug;
          }
          if( !in_array( $term->slug, $types_used ) ){
            $types_used[$term->slug] = $term->name;
          }
          //$artifact_media_types[ get_the_ID() ][ $term->slug ] = $term->name;
          //print( '<pre>' . print_r($term) .'</pre>');

        endforeach;
      }
      $classes = implode(" ", $type_classes);
      // https://wordpress.stackexchange.com/questions/152335/orientation-of-featured-image-in-post
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

      $artifacts[$artID] .= '<div class="post-artifact '.$thumb_orientation.' '.$classes.'"><div class="innerpadding">';

      if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.

        $artifacts[$artID] .= '<img src="'.get_the_post_thumbnail_url().'" class="attachment-normal size-normal wp-post-image" alt="" loading="lazy" />';
        // ? get_the_post_thumbnail( $post->ID, 'thumbnail' ); //
        //the_post_thumbnail( 'normal' ); // 1180 px width..
      }

      $artifacts[$artID] .= '<div class="overlay">';
      $artifacts[$artID] .= '<h2 class="entry-title" itemprop="headline"><a href="'.get_the_permalink().'" class="entry-title-link">'.get_the_title().'</a></h2>';

      $mediatype = '';
      //echo '<div class="entry-excerpt">'.get_the_excerpt().'</div>';
      foreach ( $type_classes as $type ) :
        //echo '<p>'.$term->taxonomy . ': ';
        $mediatype .= $type.' ';

      endforeach;

      $artifacts[$artID] .= $mediatype;
      $artifacts[$artID] .= '</div>';

      $artifacts[$artID] .= '</div></div>';

    endwhile;



endif;

wp_link_pages();

wp_reset_query();

// mainbar menu
echo '<div id="mainmenubox"><div id="mainmenu" class="pos-default"><nav><div class="innerpadding">';
  wp_nav_menu( array( 'theme_location' => 'mainmenu' ) );
echo '</div></nav></div></div>';


$logofilterclasses = '';
echo '<div id="postcontent"><div class="outermargin">';

echo '<div id="typemenu"><ul>';
foreach ( $types_used as $slug => $type ) :
  //echo '<p>'.$term->taxonomy . ': ';
  echo '<li data-type="'.$slug.'">'.$type.'</li>';
  $logofilterclasses .= $slug.' ';
endforeach;
echo '</ul></div>';


echo '<div id="loopcontainer">';


if ( get_theme_mod( 'treasure_logo_image' ) ){
  echo '<div id="logobox" class="post-artifact logobox '.$logofilterclasses.'"><div class="innerpadding">';
  echo '<a href="'.esc_url( home_url( '/' ) ).'" class="site-logo" ';
  echo 'title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" ';
  echo 'rel="home"><img src="'.get_theme_mod( 'treasure_logo_image' ).'" ';
  echo 'alt="'.esc_attr( get_bloginfo( 'name', 'display' ) ).' - '.get_bloginfo( 'description' ).'"></a>';
  echo '</div></div>';
}else{
  echo '<div id="logobox" class="post-artifact logobox '.$logofilterclasses.'"><div class="innerpadding"><hgroup><h1 class="site-title">';
  echo '<a href="'.esc_url( home_url( '/' ) ).'" id="site-logo" ';
  echo 'title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" ';
  echo 'rel="home">'.esc_attr( get_bloginfo( 'name', 'display' ) ).'</a>';
  echo '</h1>';
  echo '<h2 class="site-description">'.get_bloginfo( 'description' ).'</h2>';
  echo '</hgroup></div></div>';
}






  foreach ( $artifacts as $id => $content ) :
    echo $content;
  endforeach;

echo '</div>'; // end loopcontainer



echo '</div></div>'; // end postcontent
*/
