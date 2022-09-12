<?php

// register new posttype
function add_object_post_type() {

    // Set UI labels for Custom Post Type
    $labels = array(
          'name'                => _x( 'objects', 'Post Type General Name', 'treasure' ),
          'singular_name'       => _x( 'object', 'Post Type Singular Name', 'treasure' ),
          'menu_name'           => __( 'Objects', 'treasure' ),
          'parent_item_colon'   => __( 'Parent object', 'treasure' ),
          'all_items'           => __( 'All objects', 'treasure' ),
          'view_item'           => __( 'View object', 'treasure' ),
          'add_new_item'        => __( 'Add New object', 'treasure' ),
          'add_new'             => __( 'Add New', 'protago' ),
          'edit_item'           => __( 'Edit object', 'treasure' ),
          'update_item'         => __( 'Update object', 'treasure' ),
          'search_items'        => __( 'Search object', 'treasure' ),
          'not_found'           => __( 'Not Found', 'treasure' ),
          'not_found_in_trash'  => __( 'Not found in Trash', 'treasure' ),
    );

    $args = array(
        'label'               => __( 'objects', 'treasure' ),
        'description'         => __( 'object info and media', 'treasure' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes','capabilities'),
        'taxonomies'          => array( 'collection', 'category', 'post_tag' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           =>'dashicons-portfolio',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
    );
    register_post_type( 'object',  $args );
}
add_action( 'init', 'add_object_post_type' );

/* apply collection to object post type */
// https://developer.wordpress.org/reference/functions/register_taxonomy/
// https://developer.wordpress.org/reference/functions/register_taxonomy_for_object_type/
// source https://code.tutsplus.com/articles/applying-categories-tags-and-custom-taxonomies-to-media-attachments--wp-32319

// apply collection to objects
function add_collections_to_objects() {
    register_taxonomy_for_object_type( 'collection', 'object' );
}
add_action( 'init' , 'add_collection_to_objects' );

// apply categories to objects
function add_categories_to_objects() {
    register_taxonomy_for_object_type( 'category', 'object' );
}
add_action( 'init' , 'add_categories_to_objects' );

// apply tags to objects
function add_tags_to_objects() {
    register_taxonomy_for_object_type( 'post_tag', 'object' );
}
add_action( 'init' , 'add_tags_to_objects' );

// rest route
function add_objects_rest_route( $route, $post ) {
    if ( $post->post_type === 'object' ) {
        $route = '/wp/v2/object/' . $post->ID;
    }
    return $route;
}
add_filter( 'rest_route_for_post', 'add_objects_rest_route', 10, 2 );
