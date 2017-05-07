<?php

function entities() {
    $labels = ['name' => _x('Entities', 'post type general name', 'your-plugin-textdomain'),
        'singular_name' => _x('Entity', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name' => _x('Entities', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar' => _x('Entity', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new' => _x('Add New Entity', 'opinion', 'your-plugin-textdomain'),
        'add_new_item' => __('Add New Entity', 'your-plugin-textdomain'),
        'new_item' => __('New Entity', 'your-plugin-textdomain'),
        'edit_item' => __('Edit Entity', 'your-plugin-textdomain'),
        'view_item' => __('View Entity', 'your-plugin-textdomain'),
        'all_items' => __('All Entities', 'your-plugin-textdomain'),
        'search_items' => __('Search Entity', 'your-plugin-textdomain'),
        'parent_item_colon' => __('Parent Entity:', 'your-plugin-textdomain'),
        'not_found' => __('No Entity found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No Entities found in Trash.', 'your-plugin-textdomain')
    ];

    $args = ['labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'entity'],
        'query_var' => true,
        'menu_icon' => 'dashicons-carrot',
        'show_ui' => true,
        'show_in_menu' => true, // This tells it to show up in your admin menu
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'capability_type' => 'post',
        'supports' => [
            'title', 
            'thumbnail',
            'author', 'page-attributes']
    ];
    register_post_type('entities', $args);
}

add_action('init', 'entities');

function gossips() {
    $labels = ['name' => _x('Gossips', 'post type general name', 'your-plugin-textdomain'),
        'singular_name' => _x('Gossip', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name' => _x('Gossips', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar' => _x('Gossip', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new' => _x('Add New Gossip', 'opinion', 'your-plugin-textdomain'),
        'add_new_item' => __('Add New Gossip', 'your-plugin-textdomain'),
        'new_item' => __('New Gossip', 'your-plugin-textdomain'),
        'edit_item' => __('Edit Gossip', 'your-plugin-textdomain'),
        'view_item' => __('View Gossip', 'your-plugin-textdomain'),
        'all_items' => __('All Gossips', 'your-plugin-textdomain'),
        'search_items' => __('Search Gossip', 'your-plugin-textdomain'),
        'parent_item_colon' => __('Parent Gossip:', 'your-plugin-textdomain'),
        'not_found' => __('No Gossip found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No Gossips found in Trash.', 'your-plugin-textdomain')
    ];

    $args = ['labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'gossip'],
        'query_var' => true,
        'menu_icon' => 'dashicons-palmtree',
        'show_ui' => true,
        'show_in_menu' => true, // This tells it to show up in your admin menu
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'capability_type' => 'post',
        'supports' => [
            'title', 
            'editor',
            'author']
    ];
    register_post_type('gossips', $args);
}

add_action('init', 'gossips');





