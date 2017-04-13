<?php

// Questions custom field  ------------------------------------- Bhavana
add_filter('manage_edit-questions_columns', 'my_edit_questions_columns');

function my_edit_questions_columns($columns) {


    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Question'),
        'topic_field' => __('Topic'),
        'chapter_field' => __('Chapter'),
        'course_field' => __('Course'),
        'course_category' => __('Category'),
    );

    return $columns;
}

add_action('manage_questions_posts_custom_column', 'my_manage_questions_columns', 10, 2);

function my_manage_questions_columns($column, $post_id) {

    global $post;
    switch ($column) {
        case 'topic_field':
            $topic_field = get_post_meta($post_id, 'topic_field', true);
            echo get_the_title($topic_field);
            break;
        case 'course_field':
            $course_field = get_post_meta($post_id, 'course_field', true);
            echo get_the_title($course_field);
            //return 1;
            break;
        case 'chapter_field':
            $chapter_field = get_post_meta($post_id, 'chapter_field', true);
            echo get_the_title($chapter_field);
            // return 1;
            break;

        case 'course_category':
            $course_cat = get_the_terms($course_field, 'course-categories');
            echo $course_cat[0]->name;
            // return 1;
            break;

        default :
            break;
    }
}

// Course custom field  ------------------------------------- Bhavana

add_filter('manage_edit-courses_columns', 'my_edit_courses_columns');

function my_edit_courses_columns($columns) {


    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'course_category' => __('Category'),
        'author' => __('Author'),
        'date' => __('Date'),
    );

    return $columns;
}

add_action('manage_courses_posts_custom_column', 'my_manage_courses_columns', 10, 2);

function my_manage_courses_columns($column, $post_id) {
    global $post;
    switch ($column) {


        case 'course_category':
            $course_cat = get_the_terms($course_field, 'course-categories');
            echo $course_cat[0]->name;
            // return 1;
            break;

        default :
            break;
    }
}

// Chapters custom field  ------------------------------------- Bhavana

add_filter('manage_edit-chapters_columns', 'my_edit_chapters_columns');

function my_edit_chapters_columns($columns) {


    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'course_field' => __('Course'),
        'course_category' => __('Category'),
        'author' => __('Author'),
        'date' => __('Date'),
    );

    return $columns;
}

add_action('manage_chapters_posts_custom_column', 'my_manage_chapters_columns', 10, 2);

function my_manage_chapters_columns($column, $post_id) {
    global $post;
    switch ($column) {

        case 'course_field':
            $course_field = get_post_meta($post_id, 'course_field', true);
            echo get_the_title($course_field);
            //return 1;
            break;
        case 'course_category':
            $course_cat = get_the_terms($course_field, 'course-categories');
            echo $course_cat[0]->name;
            // return 1;
            break;

        default :
            break;
    }
}



// Topics custom field  ------------------------------------- Bhavana

add_filter('manage_edit-topics_columns', 'my_edit_topics_columns');

function my_edit_topics_columns($columns) {


    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
         'chapter_field' => __('Chapter'),
        'course_field' => __('Course'),
        'course_category' => __('Category'),
        'author' => __('Author'),
        'date' => __('Date'),
    );

    return $columns;
}

add_action('manage_topics_posts_custom_column', 'my_manage_topics_columns', 10, 2);

function my_manage_topics_columns($column, $post_id) {
    global $post;
    switch ($column) {
        case 'chapter_field':
            $chapter_field = get_post_meta($post_id, 'chapter_field', true);
            echo get_the_title($chapter_field);
            // return 1;
            break;
        case 'course_field':
            $course_field = get_post_meta($post_id, 'course_field', true);
            echo get_the_title($course_field);
            //return 1;
            break;
        case 'course_category':
            $course_cat = get_the_terms($course_field, 'course-categories');
            echo $course_cat[0]->name;
            // return 1;
            break;

        default :
            break;
    }
}