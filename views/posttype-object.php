<?php
$pid = '';
$selectedtype = 'foto';

if( isset($_GET['type']) && $_GET['type'] != '' ){
  $selectedtype = $_GET['type'];
}

get_header(); // theme default header


if ( have_posts() ) :
  while( have_posts() ) : the_post();

    if ( is_super_admin() && ( is_single() || is_page() ) ) {
      edit_post_link( __( 'Edit' , 'treasure' ), '<span class="edit-link">', '</span>' );
    }


      $pid = get_the_ID();
      $postmedia = array();
      $media = get_attached_media( '', $pid );
      $type_classes = array();
      $type_count = array();
  		$types_used = array();
      $classes = '';
      $html = '';


      echo '<div id="loopcontainer" data-id="'.$pid.'" data-type="'.$selectedtype.'">';
/*
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

      echo '<div class="post-object post '.$thumb_orientation.' '.$classes.'" data-id="'.$pid .'"><div class="innerpadding">';
        echo '<div class="object-image">';
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
                    echo '<li data-type="'.$slug.'" class="icon-button but-'.$slug.'"><a href="'.get_the_permalink().'?type='.$slug.'">'.$type.'('.$type_count[$slug].')</a></li>';
             }
        endforeach;
        echo '</ul></div>';

        echo '<div class="entry-content">';
        echo apply_filters('the_content', get_the_content());
        echo '</div>';

        // list in array per type


        if( count($media) > 0){

        	foreach($media as $element) {
            $attachment = get_post( $element->ID );
            $terms = wp_get_post_terms( $element->ID, array( 'types' ) );
            $postmedia[$element->ID] = array(
        						 'title'=>$element->post_title,
        						 'excerpt'=>$element->post_excerpt,
        						 'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        						 'caption' => $attachment->post_excerpt,
        						 'desc' => $attachment->post_content,
        						 'order'=> $attachment->menu_order,
        						 'src'=>$element->guid,
        						 'type_parent'=> $terms[0]->parent,
        						 'type_slug'=> $terms[0]->slug,
        						 'type_name'=> $terms[0]->name,
        					 );
        	}
        }
        //print_r($postmedia);


                $countmedia = 0;
                $mediabox = '';

                //$option = '<div class="column">';
                //option += '<div class="media-icon but-' + $(el).data('type') + '" data-type="' + $(el).data('type') + '" >';
                //option += '<span>' + $(el).find('span').text();
                $mediabox .= '<div class="mediacontainer">'; // '+$(el).data('type')+'


                foreach( $postmedia as $i => $media) {

                  //if ($postmedia['type_slug']  === $(el).data('type')) {

                    $countmedia++;

                    $file = $media['src'];
                    $extension = substr(strrchr($file, '.'), 1);
                    $mediabox .= '<div class="mediaholder '.$media['type_name'].'" data-order="'.$media['order'].'">';

                    $mediaembed = '';
                    switch($extension) {
                      case 'jpg':
                      case 'jpeg':
                      case 'png':
                      case 'gif':
                        $mediaembed .= '<img class="embed zoom" src="'.$file.'" alt="'.$media['alt'].'" data-desc="'.$media['desc'].'" data-caption="'.$media['caption'].'" width="600" height="auto" />';
                        break;
                      case 'mp4':
                      case 'mp3':
                        $mediaembed .= '<video class="embed" src="'.$file.'" data-desc="'.$media['desc'].'" data-caption="'.$media['caption'].'" width="600" height="350" controls></video>';
                        break;
                      case 'pdf':
                        $mediaembed .= '<iframe class="embed" src="'.$file.'#toolbar=0" data-desc="'.$media['desc'].'" data-caption="'.$media['caption'].'" width="100%" height="640px">'+
                        '<p>It appears you do not have a PDF plugin for this browser.<a href="'.$file.'">click here to download the PDF file.</a></p>'+
                        '</iframe>';//'</object>';
                        break;
                      case 'doc':
                      case 'docx':
                        //mediabox += '<iframe src="'+media.src+'" width="600" height="350"></iframe>';
                        $mediaembed .= '<a class="media-link" href="'.$file.'" title="'.$file.'" data-desc="'.$media['desc'].'" data-caption="'.$media['caption'].'">'.$media['title'].'</a>';
                        break;
                      default:
                      $mediaembed .= '<a class="media-link" href="'.$file.'"" title="'.$media['title'].'" data-desc="'.$media['desc'].'" data-caption="'.$media['caption'].'">'.$media['title'].'</a>';

                    }
                    if( $mediaembed != 0 ){
                    $mediabox .= $mediaembed.'<div class="caption">'.$media['caption'].'</div>';
                    $mediabox .= '</div>';
                    // title,excerpt,src,type_parent,type_slug,type_name
                    }

                  }

                  $mediabox .= '</div>';

                  echo '<div class="object-media">'.$mediabox.'</div>';


    echo '</div></div>';

    */
    echo '</div>'; // end loopcontainer

  endwhile;

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


get_footer();  // theme default footer
