<?php

function remove_menus() {

    if (get_current_user_id() != 1) {
        remove_menu_page('edit.php');                 //Media
        remove_menu_page('edit.php?post_type=page');                 //Media
        remove_menu_page('upload.php');
        remove_menu_page('themes.php');                 //Appearance
        remove_menu_page('plugins.php');                //Plugins
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('tools.php');
        remove_menu_page('update-core.php');                 //Media
        remove_submenu_page('index.php', 'update-core.php');
    }
}

add_action('admin_menu', 'remove_menus');

function remove_dashboard_widgets() {
    global $wp_meta_boxes;
    if (get_current_user_id() != 1) {

        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    }
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function remove_screen_options() {
    if (get_current_user_id() != 1) {
        return false;
    }
}

add_filter('screen_options_show_screen', 'remove_screen_options');

function reorder_admin_menu($__return_true) {
    return array(
        'index.php', // Dashboard
        'edit.php?post_type=entities', // Pages 
        'edit.php?post_type=gossips'
    );
}

add_filter('custom_menu_order', 'reorder_admin_menu');
add_filter('menu_order', 'reorder_admin_menu');
