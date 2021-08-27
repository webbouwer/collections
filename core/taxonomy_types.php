<?php

/*
 * TODO type icons/images ?  https://github.com/daggerhart/taxonomy-term-image/blob/master/taxonomy-term-image.php
 */

// register new taxonomy which applies to attachments (+ artifacts and posts)
function add_types_taxonomy() {
    $labels = array(
        'name'              => 'Types',
        'singular_name'     => 'type',
        'search_items'      => 'Search Type',
        'all_items'         => 'All Types',
        'parent_item'       => 'Parent Type',
        'parent_item_colon' => 'Parent Type:',
        'edit_item'         => 'Edit Type',
        'update_item'       => 'Update Type',
        'add_new_item'      => 'Add New Type',
        'new_item_name'     => 'New Type Name',
        'menu_name'         => 'Types',
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => true,
        'query_var' => true,
        'rewrite' => true,
        'show_in_rest' => true, // important for metabox display!
    );

    register_taxonomy( 'types', array( 'attachment' ), $args );
}
add_action( 'init', 'add_types_taxonomy' );

// apply categories to media attachments
function add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'types', 'attachment' );
}
add_action( 'init' , 'add_categories_to_attachments' );

// source: https://code.tutsplus.com/articles/applying-categories-tags-and-custom-taxonomies-to-media-attachments--wp-32319
