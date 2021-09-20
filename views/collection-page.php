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
$orderby = 'menu_order';
$order = 'ASC';
$ppp = isset( $values['page_collection_loadamount'] ) ? $values['page_collection_loadamount'][0] : 12;

$term = get_term_by( 'slug', $slug, $taxname );



get_header(); // theme default header


echo '<div id="loopcontainer" class="grid-view isotope" data-posttype="'.$posttype.'"  data-taxname="'.$taxname.'" data-term="'.$slug.'" data-orderby="'.$orderby.'" data-order="'.$order.'"  data-ppp="'.$ppp.'">';

echo '<div id="display-toggle"><a class="list"><span>list</span></a><a class="grid"><span>grid</span></a></div>';
echo '<div id="display-options"><ul class="orderby">';
echo '<li class="default selected" data-orderby="menu_order">Op volgorde</li><li class="title" data-orderby="title">titel</li><li class="date" data-orderby="date">datum</li>';
echo '</ul><ul class="order">';
echo '<li class="asc selected" data-order="asc">aflopend</li><li class="desc" data-order="desc">oplopend</li>';
echo '</ul></div>';


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

  echo '<div id="typemenu"><div class="innerpadding"><ul class="collection-types">';

    foreach ( $type_names as $slug => $type ) :

      echo '<li data-type="'.$slug.'" class="icon-button but-'.$slug.'"><span>'.$type.'</span></li>';

      $allfilterclasses .= $slug.' ';

    endforeach;

  echo '</ul></div></div>';

  echo '</div>'; // end loopcontainer.isotope




get_footer();  // theme default footer
