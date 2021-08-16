<?php

// register new posttype
function add_artifact_post_type() {

    // Set UI labels for Custom Post Type
    $labels = array(
          'name'                => _x( 'Artifacts', 'Post Type General Name', 'treasure' ),
          'singular_name'       => _x( 'Artifact', 'Post Type Singular Name', 'treasure' ),
          'menu_name'           => __( 'Artifacts', 'treasure' ),
          'parent_item_colon'   => __( 'Parent Artifact', 'treasure' ),
          'all_items'           => __( 'All Artifacts', 'treasure' ),
          'view_item'           => __( 'View Artifact', 'treasure' ),
          'add_new_item'        => __( 'Add New Artifact', 'treasure' ),
          'add_new'             => __( 'Add New', 'protago' ),
          'edit_item'           => __( 'Edit Artifact', 'treasure' ),
          'update_item'         => __( 'Update Artifact', 'treasure' ),
          'search_items'        => __( 'Search Artifact', 'treasure' ),
          'not_found'           => __( 'Not Found', 'treasure' ),
          'not_found_in_trash'  => __( 'Not found in Trash', 'treasure' ),
    );

    $args = array(
        'label'               => __( 'artifacts', 'treasure' ),
        'description'         => __( 'Artifact info and media', 'treasure' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields','capabilities'),
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
    register_post_type( 'artifact',  $args );
}
add_action( 'init', 'add_artifact_post_type' );

/* apply collection to artifact post type */
// https://developer.wordpress.org/reference/functions/register_taxonomy/
// https://developer.wordpress.org/reference/functions/register_taxonomy_for_object_type/
// source https://code.tutsplus.com/articles/applying-categories-tags-and-custom-taxonomies-to-media-attachments--wp-32319

// apply collection to artifacts
function add_collections_to_artifacts() {
    register_taxonomy_for_object_type( 'collection', 'artifact' );
}
add_action( 'init' , 'add_collection_to_artifacts' );

// apply categories to artifacts
function add_categories_to_artifacts() {
    register_taxonomy_for_object_type( 'category', 'artifact' );
}
add_action( 'init' , 'add_categories_to_artifacts' );

// apply tags to artifacts
function add_tags_to_artifacts() {
    register_taxonomy_for_object_type( 'post_tag', 'artifact' );
}
add_action( 'init' , 'add_tags_to_artifacts' );
