<?php

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'api') {
    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
    $output = [];

    if (method_exists($api, $method)) {
        $output = $api->$method();
    } else {
        $output = ['error' => 'Method Does Not Exist'];
    }

    header("Content-Type: application/json");
    echo json_encode($output);
    die();
}

class API {

    protected $userId;

    function __construct() {
        global $user_ID;

        if (!empty($user_ID))
            $this->userId = $user_ID;
        else if (!empty($_REQUEST['userId']))
            $this->userId = $_REQUEST['userId'];
        else
            $this->userId = NULL;
    }

    function getPage() {
        return $this->getResults(['post_type' => 'page', 'page_id' => $_REQUEST['id']], 1);
    }

    function getCourse($id = NULL) {
        $courseId = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['p' => $courseId, 'post_type' => 'courses'], 1);
    }

    function getCourseChapters($id = NULL) {
        $courseId = is_null($id) ? $_REQUEST['id'] : $id;
        $data = $this->getResults(['meta_query' => [['key' => 'course_field', 'value' => $courseId]], 'post_type' => 'chapters']);
        foreach ($data as $key => $value) {
            $args = (['meta_query' => [['key' => 'chapter_field', 'value' => $value['id']]], 'post_type' => 'questions']);
            $wp_query = new WP_Query($args);
            $count = $wp_query->post_count;
            $data[$key]['count'] = $count;
        }
        return $data;
    }

    function getDifficultyLevel() {
        return $getData = get_field_object('Difficulty_Level', 309);
    }

    function getChapter($id = NULL) {
        $chapterId = is_null($id) ? $_REQUEST['id'] : $id;
        $chapter = $this->getResults(['p' => $chapterId, 'post_type' => 'chapters'], 1);
        $chapter['course'] = get_the_title($chapter['course_field']);
        return $chapter;
    }

    function getChapterTopics($id = NULL) {
        $chapterId = is_null($id) ? $_REQUEST['id'] : $id;
        $data = $this->getResults(['meta_query' => [['key' => 'chapter_field', 'value' => $chapterId]], 'post_type' => 'topics']);
        foreach ($data as $key => $value) {
            $args = (['meta_query' => [['key' => 'topic_field', 'value' => $value['id']]], 'post_type' => 'questions']);
            $wp_query = new WP_Query($args);
            $count = $wp_query->post_count;
            $data[$key]['count'] = $count;
        }
        return $data;
    }

    function getTopicQuestions($topics = NULL, $noOfQns = NULL, $noOfMarks = NULL, $typeOfQns = NULL) {
        $topics = is_null($topics) ? $_REQUEST['topicNames'] : $topics;
        $noOfQns = is_null($noOfQns) ? $_REQUEST['noOfQns'] : $noOfQns;
        $noOfMarks = is_null($noOfMarks) ? $_REQUEST['noOfMarks'] : $noOfMarks;
        $typeOfQns = is_null($typeOfQns) ? $_REQUEST['typeOfQns'] : $typeOfQns;

        return $this->getResults(['meta_query' => [['key' => 'topic_field', 'value' => $topics, 'compare' => 'IN']], 'post_type' => 'questions']);
    }

    function getChapterTests($id = NULL) {
        $chapterId = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['meta_query' => [['key' => 'chapter', 'value' => $chapterId]], 'post_type' => 'tests']);
    }

    function getTest($id = NULL) {
        $testId = is_null($id) ? $_REQUEST['id'] : $id;
        $test = $this->getResults(['p' => $testId, 'post_type' => 'tests'], 1);
        $test['course'] = get_the_title($test['course_field']);
        $test['chapter'] = get_the_title($test['chapter_field']);
        return $test;
    }

    function getCatPosts() {
        return $this->getResults(['post_type' => 'posts', 'cat' => [$_REQUEST['cid']]]);
    }

    function getCategories() {
        return get_categories(['child_of' => $_REQUEST['id'], 'hide_empty' => 0, 'hierarchiel' => TRUE]);
    }

    function getResults($args, $single = NULL) {
        $output = [];
        $query = new WP_Query($args);
        while ($query->have_posts()): $query->the_post();
            $id = get_the_ID();
            $post = [
                'id' => $id,
                'title' => get_the_title(),
                'content' => get_the_content(),
                'link' => get_permalink($post->ID),
                'category' => get_the_category($post->ID),
                'img' => $this->getImg($post->ID)
            ];
            foreach (get_fields($id) as $k => $v) {
                $post[$k] = $v;
            }
            array_push($output, $post);
        endwhile;

        if ($single == 1)
            $output = $output[0];

        return $output;
    }

    function getImg($id) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
        if (!is_array($image)):
            $image = wp_get_attachment_image_src(38, 'full');
        endif;
        return $image['0'];
    }

    function getPostyByCourseCat() {

        $getPost = get_posts(['post_type' => 'courses', 'tax_query' => [[
            'taxonomy' => 'course-categories',
            'field' => 'id',
            'terms' => [4, 2],
        ]]]);

        foreach ($getPost as $getItem):
            $collectItem[] = [$getItem->ID];
        endforeach;
        echo "<pre>";
        print_r($collectItem);
        exit;
    }

    function testing1() {
        return get_field_object('Difficulty_Level', 309);
        $string = html_entity_decode(strip_tags(get_field('question', 308)));
        return $string;
        $my_post = array(
            'ID' => 308,
            'post_title' => $string,
        );

// Update the post into the database
        return wp_update_post($my_post);
        return update_field('post_title', 'Euclids division algorithm is used to find ……… of given positive integers.', 308);
        echo "<pre>";
        print_r($this->getResults(['post_type' => 'topics']));
        exit;
        return get_field('field_58dce36248e7c', 189);
    }

}
