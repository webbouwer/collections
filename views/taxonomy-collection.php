<?php
/* Display collection artifacts */

/* plugin display settings */
$dropdown_option = get_option( 'dropdown_option_setting_option_name' ); // Array
$dropdown_value =  $dropdown_option ['dropdown_option_0']; // Option value

get_header(); // theme default header

echo '<div id="loopcontainer">';


$slug = get_query_var( 'term' );
$taxname = get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', $slug, $taxname );

if ($term->parent == 0) { // top collections

  echo '<div class="taxonomy-'.$slug.' category">';
  echo '<div class="innerpadding">';
  wp_list_categories('taxonomy=collection&depth=1&show_count=1&title_li=&child_of=' . $term->term_id);
  echo '</div></div>';


}// else{ // else (when category has parent)

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

      ?>
      <div id="post-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
      <div class="innerpadding">
      <?php

      if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
          the_post_thumbnail( 'thumb' );
      }

          echo '<header class="entry-header">';
          echo '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
          echo '</header>';
          echo '<div class="entry-excerpt">';
          echo apply_filters('the_excerpt', get_the_excerpt()); // the_excerpt_length( 32 );
          echo '</div>';

      echo '</div></div>';

    endwhile;

endif;

wp_link_pages();

wp_reset_query();

//} //end else (when category has parent)

echo '</div>'; // end loopcontainer

get_footer();  // theme default footer
