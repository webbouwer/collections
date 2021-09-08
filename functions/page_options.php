<?php
// https://github.com/webbouwer/imagazine/blob/development/assets/metaboxes.php

/* Add metabox */
function add_page_collection_meta()
{
    global $post;
    if(!empty($post))
    {
        // only if template is used
        $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
        if($pageTemplate == '../views/collection-page.php')
        {
           add_meta_box(
                 'page_collection_meta', // $id
                 'Collection settings', // $title
                 'page_collection_settings', // $callback
                 'page', // $page
                 'side', // $context
                 'high',
                 null ); // $priority
        }
    }
}
add_action('add_meta_boxes', 'add_page_collection_meta');

/* Metabox form content */
function page_collection_settings( $post ){

    $values = get_post_custom( $post->ID );
    $selected = isset( $values['page_collection_selectbox'] ) ? esc_attr( $values['page_collection_selectbox'][0] ) : '';
    $loadamount = isset( $values['page_collection_loadamount'] ) ? $values['page_collection_loadamount'][0] : 12;

    $taxonomy = 'collection';
    $args = array(
      'taxonomy' => $taxonomy,
      'hide_empty' => 0,
      'name' => "page_collection_selectbox",
      'selected' => $selected,
      'value_field'=> 'slug',
      'orderby' => 'name',
      'hierarchical' => 0,
      'show_option_none' => 'All'
    );
    wp_dropdown_categories( $args );

    echo '<p><label for="page_collection_loadamount">Amount of load</label>
    <input name="page_collection_loadamount" value="'.$loadamount.'" size="4" /></p>';
}

/* Metabox variables save */
function save_page_collection_settings( $post_id )
{
  if( isset( $_POST['page_collection_selectbox'] ) )
        update_post_meta( $post_id, 'page_collection_selectbox', esc_attr( $_POST['page_collection_selectbox'] ) );
  if( isset( $_POST['page_collection_loadamount'] ) )
        update_post_meta( $post_id, 'page_collection_loadamount', esc_attr( $_POST['page_collection_loadamount'] ) );
}
add_action( 'save_post', 'save_page_collection_settings' );
