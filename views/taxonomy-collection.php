<?php
global $wp;
$current_url = home_url( add_query_arg( array(), $wp->request ) );

/* Display collection objects */
$posttype = 'object';
$pagetermslug = get_query_var( 'term' );
$taxname = get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', $slug, $taxname );
$ppp = get_option( 'posts_per_page' ); // default

/* plugin display settings */
$viewtype = 'basic';
$display_option = get_option( 'dropdown_option_setting_option_name' ); // Array
$viewtype =  $display_option['dropdown_option_0']; // Option value

$collection_title = get_term($term->term_id)->name;
$collection_desc = get_term($term->term_id)->description;

get_header(); // theme default header

if ($term->parent == 0) { // (top collection
  echo '<div id="categorymenu"><div class="taxonomy-'.$pagetermslug.' category">';
  echo '<div class="innerpadding">';
  wp_list_categories('taxonomy=collection&depth=1&show_count=1&title_li=&child_of=' . $term->term_id);
  echo '</div></div>';
}

echo '<div id="collection-info"><h1>'.$collection_title.'</h1>'.$collection_desc.'</div>';



if( $viewtype == 'grid'){

// typeMenuHTML collections.php
typeMenuHTML();

orderMenuHTML();

echo '<div id="loopcontainer" class="grid-view isotope" data-homeurl="'.$current_url.'" data-posttype="'.$posttype.'"  data-taxname="'.$taxname.'" data-term="'.$pagetermslug.'" data-ppp="'.$ppp.'">';

}else{ // $viewtype == 'basic'

echo '<div id="loopcontainer">';

if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
elseif ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
else { $paged = 1; }

$get_post_args = array(
  'post_type' => 'object', // Your Post type Name that You Registered
  'posts_per_page' => $ppp,
  'paged' => $paged,
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

          echo '<div class="post-object post '.$thumb_orientation.' '.$classes.'" data-id="'.$ID .'"><div class="innerpadding">';

            if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
    						the_post_thumbnail( 'full' );
    				}

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

      echo '</div></div>';

    endwhile;

    // posts_nav_link( ' · ', 'previous page', 'next page' );
    echo '<div class="pagination"><div class="innerpadding">';
    $big = 999999999; // need an unlikely integer
    echo paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $collection_posts->max_num_pages
    ) );
    echo '</div></div>';

endif;

wp_reset_query();

echo '</div>'; // end loopcontainer

} // end basic list pages

get_footer();  // theme default footer
