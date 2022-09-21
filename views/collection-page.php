<?php

/* Display collection objects */
$posttype = 'object';
$taxname = 'collection';

$values = get_post_custom( get_the_ID() );
$startslug = isset( $values['page_collection_selectbox'] ) ? $values['page_collection_selectbox'][0] : 'Uncategorized';
$orderby = 'menu_order';
$order = 'ASC';
$ppp = 4;//isset( $values['page_collection_loadamount'] ) ? $values['page_collection_loadamount'][0] : 12;

$term = get_term_by( 'slug', $slug, $taxname );
$collection_title = get_term($term->term_id)->name;
$collection_desc = get_term($term->term_id)->description;

get_header(); // theme default header


echo '<div id="collection-info"><h1>'.$collection_title.'</h1>'.$collection_desc.'</div>';

if ($term->parent == 0) { // (top collection

    echo '<div id="categorymenu"><div class="taxonomy-'.$startslug.' category">';
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
  $type_desc[$child->slug] = $child->description;
}
$allfilterclasses = '';
echo '<div id="typemenu"><div id="helpinfo">Help</div><div class="innerpadding"><ul class="collection-types">';
  foreach ( $type_names as $slug => $type ) :
    echo '<li data-type="'.$slug.'" data-desc="'.$type_desc[$slug].'" class="icon-button but-'.$slug.'"><span>'.$type.'</span></li>';
    $allfilterclasses .= $slug.' ';
  endforeach;
echo '</ul><div class="menuinfo"></div></div></div>';

echo '<div id="show-recent">Bekijk laatst toegevoegde objecten</div>';

echo '<div id="display-toggle"><a class="list"><span>lijst</span></a><a class="grid"><span>tegels</span></a></div>';
echo '<div id="display-options"><span>Sorteer:</span><ul class="orderby">';
echo '<li class="default selected" data-orderby="menu_order">Selectie</li><li class="title" data-orderby="title">Titel</li>';
//echo '<li class="date" data-orderby="date">Datum</li>';
echo '</ul><ul class="order">';
echo '<li class="asc selected" data-order="asc">Oplopend</li><li class="desc" data-order="desc">Aflopend</li>';
echo '</ul></div>';

echo '<div id="loopcontainer" class="grid-view isotope" data-posttype="'.$posttype.'"  data-taxname="'.$taxname.'" data-term="'.$startslug.'" data-orderby="'.$orderby.'" data-order="'.$order.'"  data-ppp="'.$ppp.'">';
echo '</div>'; // end loopcontainer.isotope

get_footer();  // theme default footer


/*



*/
