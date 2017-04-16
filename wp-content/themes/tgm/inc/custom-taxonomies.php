<?php

function categories() {
    // create a new taxonomy

    $labels = ['name' => _x('Category', 'post type general name', 'your-plugin-textdomain'),
        'singular_name' => _x('Category', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name' => _x('Categories', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar' => _x('Category', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new' => _x('Add New Category', 'opinion', 'your-plugin-textdomain'),
        'add_new_item' => __('Add New Category', 'your-plugin-textdomain'),
        'new_item' => __('New Category', 'your-plugin-textdomain'),
        'edit_item' => __('Edit Category', 'your-plugin-textdomain'),
        'view_item' => __('View Category', 'your-plugin-textdomain'),
        'all_items' => __('All Categories', 'your-plugin-textdomain'),
        'search_items' => __('Search Category', 'your-plugin-textdomain'),
        'parent_item_colon' => __('Parent Category:', 'your-plugin-textdomain'),
        'not_found' => __('No Category found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No Categories found in Trash.', 'your-plugin-textdomain')
    ];

    register_taxonomy(
            'categories', ['entities'], array(
        'labels' => $labels,
        'rewrite' => array('slug' => 'entity-category'),
        'hierarchical' => true
            )
    );
}

add_action('init', 'categories');


add_action( 'init', 'gp_register_taxonomy_for_object_type' );
function gp_register_taxonomy_for_object_type() {
    register_taxonomy_for_object_type( 'post_tag', 'entities' );
};