<?php

show_admin_bar(false);

function my_acf_load_field($field) {


    $field['choices'] = array(
        '1' => 'My Custom Choice1',
        '2' => 'My Custom Choice2',
        '3' => 'My Custom Choice3',
    );

    return $field;
}

// all
// add_filter('acf/load_field', 'my_acf_load_field');
// type
//add_filter('acf/load_field/key=field_58dce36248e7c', 'my_acf_load_field');


add_filter('acf/fields/relationship/query/name=course', 'my_relationship_query', 10, 3);

function my_relationship_query($args, $field, $post) {

    $service_slug = 'grade-2';  // make this DYNAMIC!!!

    $args['tax_query'] = array(
        array(
            'taxonomy' => 'course-categories',
            'field' => 'slug',
            'terms' => $service_slug,
        ),
    );
    return $args;
}

function my_post_object_query($args, $field, $post_id) {
    $service_slug = '4';  // make this DYNAMIC!!!

    $args['tax_query'] = [
        [
            'taxonomy' => 'course-categories',
            'field' => 'id',
            'terms' => $service_slug,
        ]
    ];
    return $args;
}

//add_filter('acf/fields/post_object/query/key=field_58ca55ac3a890', 'my_post_object_query');
// filter for every field



function filterByTaxonomy() {
    $getPost = get_posts(['post_type' => $_POST['postType'], 'post_status' => 'publish', 'tax_query' => [[
        'taxonomy' => $_POST['categoryType'],
        'field' => 'id',
        'terms' => $_POST['categories'],
    ]]]);
    if (!empty($_POST['postId'])):
        if ($_POST['currentPostType'] == 'chapters'):
            $getSelectedField = get_field('field_58e2234a8acdf', $_POST['postId']);
        elseif ($_POST['currentPostType'] == 'topics'):
            $getSelectedField = get_field('field_58dce36248e7c', $_POST['postId']);
        elseif ($_POST['currentPostType'] == 'questions'):
            $getSelectedField = get_field('field_58e344e0968ee', $_POST['postId']);
        elseif ($_POST['currentPostType'] == 'tests'):
            $getSelectedField = get_field('field_58e3a4fb33139', $_POST['postId']);
        else:
             $getSelectedField = get_field('course_field', $_POST['postId']);
        endif;
    endif;
    $field.="<option value='0'>Please Select Course</option>";
    foreach ($getPost as $getVal):
        $selectVal = $getSelectedField == $getVal->ID ? 'selected' : '';
        $field.="<option $selectVal value='$getVal->ID'>$getVal->post_title</option>";
    endforeach;
    print_r($field);
    exit;
}

add_action('wp_ajax_filterByTaxonomy', 'filterByTaxonomy');         // executed when logged in
add_action('wp_ajax_nopriv_filterByTaxonomy', 'filterByTaxonomy'); // executed when logged out

function chaptersFilter() {
    $args = ['post_type' => 'chapters', 'post_status' => 'publish', 'meta_query' => [[
        'key' => 'course_field',
        'value' => $_POST['courseId'],
        'compare' => '='
    ]]];
    $getPost = get_posts($args);
    if ($_POST['currentPostType'] == 'tests'):
        $getSelectedVal = get_field('field_58e3a5343313a', $_POST['postId']);
    elseif($_POST['currentPostType'] == 'topics'):
        $getSelectedVal = get_field('field_58de17c79b4cf', $_POST['postId']);
    else:
        $getSelectedVal = get_field('chapter_field', $_POST['postId']);
    endif;
    $field.="<option value='0'>Please Select Chapter</option>";
    foreach ($getPost as $getVal):
        $selectVal = $getSelectedVal == $getVal->ID ? 'selected' : '';
        $field.="<option $selectVal value='$getVal->ID'>$getVal->post_title</option>";
    endforeach;
    print_r($field);
    exit;
}

add_action('wp_ajax_chaptersFilter', 'chaptersFilter');

function topicsFilter() {
    $args = ['post_type' => 'topics', 'post_status' => 'publish', 'meta_query' => ['relation' => 'AND', [
                'key' => 'chapter_field',
                'value' => $_POST['chapterId'],
                'compare' => '='
            ], [
                'key' => 'course_field',
                'value' => $_POST['courseId'],
                'compare' => '='
    ]]];
    $getPost = get_posts($args);
    $getSelectedVal = get_field('field_58e3472d3dd15', $_POST['postId']);
    $field.="<option value='0'>Please Select Topic</option>";
    foreach ($getPost as $getVal):
        $selectVal = $getSelectedVal == $getVal->ID ? 'selected' : '';
        $field.="<option $selectVal value='$getVal->ID'>$getVal->post_title</option>";
    endforeach;
    print_r($field);
    exit;
}

add_action('wp_ajax_topicsFilter', 'topicsFilter');

function notesFilter(){
        $args = ['post_type' => 'notes', 'post_status' => 'publish', 'meta_query' => ['relation' => 'AND', [
                'key' => 'topic_field',
                'value' => $_POST['topicId'],
                'compare' => '='
            ]]];
    $getPost = get_posts($args);
    $getSelectedVal = get_field('note_field', $_POST['postId']);
    $field.="<option value='0'>Please Select Note</option>";
    foreach ($getPost as $getVal):
        $selectVal = $getSelectedVal == $getVal->ID ? 'selected' : '';
        $field.="<option $selectVal value='$getVal->ID'>$getVal->post_title</option>";
    endforeach;
    print_r($field);
    exit;
}
add_action('wp_ajax_notesFilter', 'notesFilter');
function questionsFilter() {
    $args = ['post_type' => 'questions', 'post_status' => 'publish', 'posts_per_page' => -1, 'meta_query' => ['relation' => 'AND', [
                'key' => 'chapter_field',
                'value' => $_POST['chapterId'],
                'compare' => '='
            ], [
                'key' => 'course_field',
                'value' => $_POST['courseId'],
                'compare' => '='
    ]]];
    $getPost = get_posts($args);
    $getSelectedVal = array_values(get_field('field_58e4a4400baf8', $_POST['postId']));

    foreach ($getPost as $key => $getVal):
        //return  in_array($getSelectedVal,$getVal->ID);
        $selectVal = in_array($getVal->ID, $getSelectedVal) ? 'selected' : '';
        $getQuestionName = strip_tags(get_field('question', $getVal->ID));
        $getTopic = get_the_title(get_field('topic_field', $getVal->ID));
        $collectArray[] = [$getTopic => [ $getVal->ID => $getQuestionName]];

        $field.="<option $selectVal value='$getVal->ID'>$getTopic-- $getQuestionName   </option>";
    endforeach;
    //print_r($field);
    sort($collectArray);
    //print_r(array_unique($collectArray));
    print_r(createOptionGroup($collectArray, $getSelectedVal));


    exit;
}

function createOptionGroup($collectArray, $getSelectedVal) {
    $getArray = combineValuesWithSameKey($collectArray);
    foreach ($getArray as $getGroupItem):
        $getGroupKey = key($getGroupItem[0]);
        $createOption.="<optgroup label='$getGroupKey'>";

        foreach ($getGroupItem as $getMainItems):
            $getItemValue = array_values($getMainItems[$getGroupKey])[0];
            $getItemKey = key($getMainItems[$getGroupKey]);
            $selectVal = in_array($getItemKey, $getSelectedVal) ? 'selected' : '';
            $createOption.="<option $selectVal value='$getItemKey'>$getItemValue</option>";
        endforeach;
        $createOption.="</optgroup>";
    endforeach;
    return $createOption;
}

function combineValuesWithSameKey($collectArray) {
    foreach ($collectArray as $key => $value):
        $collectKey = key($value);
        $collectItem[$collectKey][] = $collectArray[$key];
    endforeach;
    return $collectItem;
}

function getTopicName() {
    $args = ['post_type' => 'topics', 'post_status' => 'publish', 'posts_per_page' => -1, 'meta_query' => ['relation' => 'AND', [
                'key' => 'chapter_field',
                'value' => $_POST['chapterId'],
                'compare' => '='
            ], [
                'key' => 'course_field',
                'value' => $_POST['courseId'],
                'compare' => '='
    ]]];
    $getPost = get_posts($args);
}

add_action('wp_ajax_questionsFilter', 'questionsFilter');


add_action('save_post', 'updateQuestionTitle');

function updateQuestionTitle($postId) {
global $wpdb;
    if (get_post_type($postId) == 'questions' && $_SERVER['REQUEST_METHOD'] == 'POST'):
        $newTitle=  strip_tags($_POST['acf']['field_58ca56734e401']);
        $wpdb->get_results("UPDATE wp_posts SET post_title='$newTitle' WHERE ID= $postId ");
    endif;
}
