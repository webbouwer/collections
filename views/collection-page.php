<?php

/* Display collection objects */
$posttype = 'object';
$taxname = 'collection';

$values = get_post_custom( get_the_ID() );
$startslug = isset( $values['page_collection_selectbox'] ) ? $values['page_collection_selectbox'][0] : 'Uncategorized';
$orderby = 'menu_order';
$order = 'ASC';
$ppp = isset( $values['page_collection_loadamount'] ) ? $values['page_collection_loadamount'][0] : 12;

$term = get_term_by( 'slug', $slug, $taxname );

get_header(); // theme default header

if ($term->parent == 0 && !is_front_page() ) { // (top collection
    echo '<div id="categorymenu"><div class="taxonomy-'.$startslug.' category">';
    echo '<div class="innerpadding">';
    wp_list_categories('taxonomy=collection&depth=1&show_count=1&title_li=&child_of=' . $term->term_id);
    echo '</div></div></div>';
}


// typeMenuHTML collections.php
typeMenuHTML();

orderMenuHTML();

echo '<div id="loopcontainer" class="grid-view isotope" data-homeurl="'.get_home_url().'" data-posttype="'.$posttype.'"  data-taxname="'.$taxname.'" data-term="'.$startslug.'" data-orderby="'.$orderby.'" data-order="'.$order.'"  data-ppp="'.$ppp.'">';
echo '</div>'; // end loopcontainer.isotope

get_footer();  // theme default footer
