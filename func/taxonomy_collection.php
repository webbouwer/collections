<?php

// register new taxonomy which applies to posts and artifacts (or attachments/posts)
function add_collection_taxonomy() {
    $labels = array(
        'name'              => 'Collections',
        'singular_name'     => 'collection',
        'search_items'      => 'Search Collection',
        'all_items'         => 'All Collections',
        'parent_item'       => 'Parent Collection',
        'parent_item_colon' => 'Parent Collection:',
        'edit_item'         => 'Edit Collection',
        'update_item'       => 'Update Collection',
        'add_new_item'      => 'Add New Collection',
        'new_item_name'     => 'New Collection Name',
        'menu_name'         => 'Collection',
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
        'query_var' => true,
        'rewrite' => true,
        'show_in_rest' => true, // important for metabox display!
    );

    //register_taxonomy( 'collection', array( 'attachment', 'post', 'artifact' ), $args );
   register_taxonomy( 'collection', array( 'post', 'artifact' ), $args );
}
add_action( 'init', 'add_collection_taxonomy' );

// source: https://code.tutsplus.com/articles/applying-categories-tags-and-custom-taxonomies-to-media-attachments--wp-32319
