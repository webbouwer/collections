<?php

get_header(); // theme default header

echo '<div id="loopcontainer">';

if ( have_posts() ) :

  while( have_posts() ) : the_post();

    if ( is_super_admin() && ( is_single() || is_page() ) ) {
      edit_post_link( __( 'Edit' , 'treasure' ), '<span class="edit-link">', '</span>' );
    }

      $ID = get_the_ID();
      $media = get_attached_media( '', $ID );
      $type_classes = array();
      $type_count = array();
  		$types_used = array();
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
      $thumb_orientation = 'portrait';
      $image = wp_get_attachment_image_src( get_post_thumbnail_id($ID) , '');
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

      echo '<div class="post-artifact post '.$thumb_orientation.' '.$classes.'" data-id="'.$ID .'"><div class="innerpadding">';

        echo '<div class="artifact-image">';
        if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
						the_post_thumbnail( 'full' );
				}
        echo '</div>';

        echo '<header class="entry-header">';
        echo '<h1><a href="'.get_the_permalink().'">'.get_the_title().'</a></h1>';
        echo '</header>';

        echo '<div class="item-icons"><ul>';
        foreach ( $types_used as $slug => $type ) :
             if( $type_count[$slug] != '' ){
                    echo '<li data-type="'.$slug.'" class="icon-button but-'.$slug.'"><span>'.$type.'('.$type_count[$slug].')</span></li>';
             }
        endforeach;
        echo '</ul></div>';

        echo '<div class="entry-content">';
        echo apply_filters('the_content', get_the_content());
        echo '</div>';

        /*
        if( is_single() && ( comments_open() || get_comments_number() ) ){
            comments_template( '/html/comments.php' );
        }
        */

    echo '</div></div>';

  endwhile;

  ?>
  <div class="previous-post-link">
    <?php previous_post_link('%link', '<< Previous Post', $in_same_term = true, $excluded_terms = '', $taxonomy = 'collection'); ?>
  </div>
  <div class="next-post-link">
    <?php next_post_link('%link', 'Next Post >>', $in_same_term = true, $excluded_terms = '', $taxonomy = 'collection'); ?>
  </div>
  <?php

endif;

wp_link_pages();

wp_reset_query();

echo '</div>'; // end loopcontainer

get_footer();  // theme default footer
