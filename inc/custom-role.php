<?php

$user_info = get_userdata(get_current_user_id());
$checkRole = $user_info->roles[0];
if ($checkRole == 'teacher'):
    add_action('admin_menu', 'my_remove_menu_pages');
endif;

function my_remove_menu_pages() {
    remove_menu_page('bookmarks.php');
    remove_menu_page('wpfront-user-role-editor-all-roles');
    remove_menu_page('options-general.php');
    remove_menu_page('student-notes.php');
    remove_menu_page('profile.php');
}
