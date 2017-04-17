<?php

$api = new Api;

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

    function getEntitiesByCategories($id = null) {
        $catId = is_null($id) ? $_REQUEST['id'] : $id;

        return $this->getResults(['post_type' => 'entities', 'tax_query' => [[
                    'taxonomy' => 'categories',
                    'field' => 'id',
                    'terms' => $catId,
        ]]]);
    }

    function getEntity($id = null) {
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['post_type' => 'entities', 'p' => $id], 1);
    }

    function getGossips($id = null) {
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['post_type' => 'gossips', 'meta_query' => [[
                    'key' => 'gossip_about',
                    'value' => $id
        ]]]);
    }

    function getGossip($id = null) {
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['post_type' => 'gossips', 'p' => $id], 1);
    }

    function insertGossip() {
        $data = array(
            'comment_post_ID' => $id,
            'comment_content' => $msg,
            'user_id' => $this->userId,
            'comment_date' => $time,
            'comment_approved' => 1,
        );

        return wp_insert_post($data);
    }

    function insertComment($id = null, $msg = null) {
        $time = current_time('mysql');
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $msg = is_null($msg) ? $_REQUEST['msg'] : $msg;

        $data = array(
            'comment_post_ID' => $id,
            'comment_content' => $msg,
            'user_id' => $this->userId,
            'comment_date' => $time,
            'comment_approved' => 1,
        );

        return wp_insert_comment($data);
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

}
