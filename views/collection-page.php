<?php

/* Display collection artifacts */
$posttype = 'artifact';
$taxname = 'collection';

/*
$slug = get_query_var( 'term' );
$taxname = get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', $slug, $taxname );
$ppp = get_option( 'posts_per_page' ); // default
*/

$values = get_post_custom( get_the_ID() );

$slug = isset( $values['page_collection_selectbox'] ) ? $values['page_collection_selectbox'][0] : 'Uncategorized';
$ppp = isset( $values['page_collection_loadamount'] ) ? $values['page_collection_loadamount'][0] : 12;

$term = get_term_by( 'slug', $slug, $taxname );



get_header(); // theme default header




echo '<div id="loopcontainer" class="grid-view isotope" data-posttype="'.$posttype.'"  data-taxname="'.$taxname.'" data-term="'.$slug.'" data-ppp="'.$ppp.'">';


  if ($term->parent == 0) { // (top collection

    echo '<div id="categorymenu"><div class="taxonomy-'.$slug.' category">';
    echo '<div class="innerpadding">';
    wp_list_categories('taxonomy=collection&depth=1&show_count=1&title_li=&child_of=' . $term->term_id);
    echo '</div></div>';

  }

  // type menu
  $typeparent =get_terms( 'types', array('hide_empty' => 0, 'parent' => 4 ));
  $types = array();
  foreach ($typeparent as $child) {
    $types[$child->slug] = $child->slug;
    $type_names[$child->slug] = $child->name;
  }

  $allfilterclasses = '';

  echo '<div id="typemenu"><div class="innerpadding"><ul>';

    foreach ( $type_names as $slug => $type ) :

      echo '<li data-type="'.$slug.'" class="icon-button but-'.$slug.'"><span>'.$type.'</span></li>';

      $allfilterclasses .= $slug.' ';

    endforeach;

  echo '</ul></div></div>';

  echo '</div>'; // end loopcontainer.isotope




get_footer();  // theme default footer
