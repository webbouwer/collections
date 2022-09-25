<?php
$posttype = 'object';
$taxname = 'collection';
$pid = '';
$selectedtype = 'foto';
$ppp = get_option( 'posts_per_page' ); // default

if( isset($_GET['type']) && $_GET['type'] != '' ){
  $selectedtype = $_GET['type'];
}

get_header(); // theme default header


echo '<div id="show-recent">Bekijk laatst toegevoegde objecten</div><div id="helpinfo">Help</div>';
echo '<div id="optionbar">';
typeMenuHTML();
orderMenuHTML();
echo '</div>';

if ( have_posts() ) :

  while( have_posts() ) : the_post();

    if ( is_super_admin() && ( is_single() || is_page() ) ) {
      edit_post_link( __( 'Edit' , 'treasure' ), '<span class="edit-link">', '</span>' );
    }
    if ( is_single() || is_page() ){
      $pid = get_the_ID();
      $postmedia = array();
      $media = get_attached_media( '', $pid );
      $type_classes = array();
      $type_count = array();
  		$types_used = array();
      $classes = '';
      $html = '';
    }

  endwhile;

  echo '<div id="objectcontainer" data-id="'.$pid.'" data-type="'.$selectedtype.'"></div>';
  echo '<div id="loopcontainer" class="nav-view isotope" data-homeurl="'.get_home_url().'" data-posttype="'.$posttype.'"  data-taxname="'.$taxname.'" data-term="chateau-du-lac" data-orderby="menu_order" data-order="ASC" data-ppp="'.$ppp.'"></div>';
  /*

?>
  <div class="pagelinkbox">
    <div class="previous-post-link">
      <?php previous_post_link('%link', '<< Previous Post', $in_same_term = true, $excluded_terms = '', $taxonomy = 'collection'); ?>
    </div>
    <div class="next-post-link">
      <?php next_post_link('%link', 'Next Post >>', $in_same_term = true, $excluded_terms = '', $taxonomy = 'collection'); ?>
    </div>
  </div>


  <?php

  */

endif;

wp_link_pages();
wp_reset_query();
get_footer();  // theme default footer
