<?php

require get_template_directory() . '/inc/traits/Users.php';
require get_template_directory() . '/inc/traits/Trends.php';
require get_template_directory() . '/inc/traits/Cron.php';
require get_template_directory() . '/inc/traits/OneSignal.php';

class API {

    use Users,
        Trends,
        Cron,
        OneSignal;

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

    function getEntityCategories() {
        return get_terms('categories');
    }

    function getEntitiesByCategory($id = null, $pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;
        $catId = is_null($id) ? $_REQUEST['id'] : $id;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'tax_query' => [[
                    'taxonomy' => 'categories',
                    'field' => 'id',
                    'terms' => $catId,
        ]]]);
    }

    function getEntity($id = null) {
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['post_type' => 'entities', 'p' => $id], 1);
    }

    function getEntityFollowers($id = null, $pageNo = null) {
        global $wpdb;
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }
        $limit = 20;

        $getfollowers = $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "entity_followers 
                        WHERE 
                        entity = $id limit $start,$limit
                ");


        $followers = [];

        foreach ($getfollowers as $f) {
            array_push($followers, $this->getUserDetails($f->follower));
        }

        return $followers;
    }

    function getEntityNews($id = null, $pageNo = null) {
        global $wpdb;
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }
        $limit = 20;

        $news = $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "news 
                        WHERE 
                        entity = $id order by time desc limit $start,$limit
                ");




        return $news;
    }

    function getGossips($id = null, $pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_query' => [[
                    'key' => 'gossip_about',
                    'value' => $id
        ]]]);
    }

    function getGossip($id = null) {
        global $wpdb;
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $gossip = $this->getResults(['post_type' => 'gossips', 'p' => $id], 1);

        if (!empty($gossip['via']) && is_numeric($gossip['via'])) {
            $gossip['via'] = $wpdb->get_row("SELECT id,title,url FROM " . $wpdb->prefix . "news where id = " . $gossip['via'], ARRAY_A);
        }

        return $gossip;
    }

    function vote($id = null, $type = null) {
        global $wpdb;
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $type = is_null($type) ? $_REQUEST['type'] : $type;

        if (!empty($id) && !empty($this->userId)) {

            $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "voters 
                        WHERE 
                        voter = $this->userId AND 
                        gossip = $id AND
                        type = $type    
                ");

            if ($wpdb->num_rows <= 0) {
                $wpdb->insert($wpdb->prefix . "voters", ["voter" => $this->userId, "gossip" => $id, "type" => $type]);
            }

            $this->calculateVotes($id);



            return ['success' => 'voted'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }

    function unvote($id = null, $type = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $type = is_null($type) ? $_REQUEST['type'] : $type;

        if (!empty($id) && !empty($this->userId)) {


            $wpdb->delete($wpdb->prefix . "voters", ['voter' => $this->userId, 'gossip' => $id, "type" => $type]);

            $this->calculateVotes($id);


            return ['success' => 'unvoted'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }

    function getvoters($id = null, $type = null, $pageNo = null) {
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $type = is_null($type) ? $_REQUEST['type'] : $type;

        $limit = 20;

        global $wpdb;

        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }

        $getVoters = $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "voters 
                        WHERE 
                        gossip = $id and type=$type limit $start,$limit
                ");


        $voters = [];

        foreach ($getVoters as $f) {
            array_push($voters, $this->getUserDetails($f->voter));
        }

        return $voters;
    }

    function getGossipFollowers($id = null, $pageNo = null) {
        global $wpdb;
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }
        $limit = 20;

        $getfollowers = $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "gossip_followers 
                        WHERE 
                        gossip = $id limit $start,$limit
                ");


        $followers = [];

        foreach ($getfollowers as $f) {
            array_push($followers, $this->getUserDetails($f->follower));
        }

        return $followers;
    }

    function insertEntity($entity = null, $media = null, $current_location = null, $facebook_link = null, $twitter_link = null, $youtube_link = null, $instagram_link = null, $linkedin_link = null, $category = null) {
        $entity = is_null($entity) ? $_REQUEST['entity'] : $id;
        $meta = [];
        $meta['current_location'] = is_null($current_location) ? $_REQUEST['current_location'] : $current_location;
        $meta['facebook_link'] = is_null($facebook_link) ? $_REQUEST['facebook_link'] : $facebook_link;
        $meta['twitter_link'] = is_null($twitter_link) ? $_REQUEST['twitter_link'] : $twitter_link;
        $meta['youtube_link'] = is_null($youtube_link) ? $_REQUEST['youtube_link'] : $youtube_link;
        $meta['linkedin_link'] = is_null($linkedin_link) ? $_REQUEST['linkedin_link'] : $linkedin_link;
        $meta['instagram_link'] = is_null($instagram_link) ? $_REQUEST['instagram_link'] : $instagram_link;
        $category = is_null($category) ? $_REQUEST['category'] : $category;

        $args = [
            'post_type' => 'entities',
            'post_title' => $entity,
            'post_status' => 'publish',
            'post_author' => $this->userId,
            'meta_input' => $meta,
            'tax_input' => [
                'categories' => [$category]
        ]];
        return $pid = wp_insert_post($args);
    }

    function insertGossip($gossip = null, $media = null, $feedbackType = null, $isAnonymous = null, $via = null) {
        $id = is_null($id) ? $_REQUEST['id'] : $id;
        $gossip = is_null($gossip) ? $_REQUEST['gossip'] : $gossip;
        $feedbackType = is_null($feedbackType) ? $_REQUEST['feedbackType'] : $feedbackType;
        $isAnonymous = is_null($isAnonymous) ? $_REQUEST['isAnonymous'] : $isAnonymous;
        $via = is_null($via) ? $_REQUEST['via'] : $via;

        $args = [
            'post_type' => 'gossips',
            'post_content' => $gossip,
            'post_title' => substr($gossip, 0, 20),
            'post_status' => 'publish',
            'post_author' => $this->userId,
            'meta_input' => [
                'gossip_about' => $id,
                'feedback_type' => $feedbackType,
                'total_upvotes' => 0,
                'total_downvotes' => 0,
                'is_anonymous' => $isAnonymous,
                'via' => $via
        ]];
        $pid = wp_insert_post($args);
        $this->calculateEntityStats($id);
        return $pid;
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

    function getUserDetails($id = null) {

        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "connections 
                        WHERE 
                        followee = $id
                ");

        $followers = $wpdb->num_rows;

        $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "connections 
                        WHERE 
                        follower = $id 
                ");

        $following = $wpdb->num_rows;

        $data = get_userdata($id)->data;
        $data->first_name = get_user_meta($id, 'first_name', true);
        $data->last_name = get_user_meta($id, 'last_name', true);
        $data->source = get_user_meta($id, 'source', true);
        $data->img = $this->getUserImg($id);
        $data->follower_count = $followers;
        $data->following_count = $following;
        unset($data->user_pass);

        return $data;
    }

    function getUserImg($id) {

        $picture = get_user_meta($id, 'profile_picture', true);

        if (!empty($picture)) {

            switch (get_user_meta($id, 'source', true)) {
                case "Facebook":
                    $picture = "http://graph.facebook.com/$picture/picture?type=large";
                    break;
                case "Manual":
                    $uploads = wp_upload_dir();
                    $picture = $uploads['baseurl'] . "/profilepics/$picture";
                    break;

                default:
                    $uploads = wp_upload_dir();
                    $picture = $uploads['baseurl'] . "/profilepics/$picture";
                    break;
            }
        } else {
            $picture = get_template_directory_uri() . "/images/user.png";
        }
        return $picture;
    }

    function search($array, $key, $value) {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }

        return $results;
    }

    function calculateVotes($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        $votes = $wpdb->get_row("SELECT 
                                    COUNT( IF(TYPE =0, 1, NULL ) ) AS upvotes, 
                                    COUNT( IF( TYPE =1, 1, NULL ) ) AS downvotes
                                    FROM  " . $wpdb->prefix . "voters  
                                    WHERE gossip =$id");



        update_field("total_upvotes", $votes->upvotes, $id);
        update_field("total_downvotes", $votes->downvotes, $id);
    }

    function calculateEntityStats($id = null) {
        global $wpdb;
        $id = is_null($id) ? $_REQUEST['id'] : $id;

        $followers = $wpdb->get_var("SELECT count(*) as count FROM `" . $wpdb->prefix . "entity_followers` where entity = $id");
        $totalGossips = $wpdb->get_var("SELECT count(*) as total FROM " . $wpdb->prefix . "posts, " . $wpdb->prefix . "postmeta WHERE " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "postmeta.post_id AND " . $wpdb->prefix . "postmeta.meta_key = 'gossip_about' AND " . $wpdb->prefix . "postmeta.meta_value = $id AND " . $wpdb->prefix . "posts.post_status = 'publish' AND " . $wpdb->prefix . "posts.post_type = 'gossips'");

        $totalPositiveGossips = $wpdb->get_var("SELECT count(*) as total FROM " . $wpdb->prefix . "posts JOIN " . $wpdb->prefix . "postmeta entity ON ( " . $wpdb->prefix . "posts.ID = entity.post_id AND entity.meta_key = 'gossip_about' ) JOIN " . $wpdb->prefix . "postmeta type ON ( " . $wpdb->prefix . "posts.ID = type.post_id AND type.meta_key = 'feedback_type' ) WHERE entity.meta_value = $id AND type.meta_value=0 AND " . $wpdb->prefix . "posts.post_status = 'publish' AND " . $wpdb->prefix . "posts.post_type = 'gossips'");
        $totalNegativeGossips = $wpdb->get_var("SELECT count(*) as total FROM " . $wpdb->prefix . "posts JOIN " . $wpdb->prefix . "postmeta entity ON ( " . $wpdb->prefix . "posts.ID = entity.post_id AND entity.meta_key = 'gossip_about' ) JOIN " . $wpdb->prefix . "postmeta type ON ( " . $wpdb->prefix . "posts.ID = type.post_id AND type.meta_key = 'feedback_type' ) WHERE entity.meta_value = $id AND type.meta_value=1 AND " . $wpdb->prefix . "posts.post_status = 'publish' AND " . $wpdb->prefix . "posts.post_type = 'gossips'");

        $totalGossipsToday = $wpdb->get_var("SELECT count(*) as total FROM " . $wpdb->prefix . "posts, " . $wpdb->prefix . "postmeta WHERE " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "postmeta.post_id AND " . $wpdb->prefix . "postmeta.meta_key = 'gossip_about' AND " . $wpdb->prefix . "postmeta.meta_value = $id AND " . $wpdb->prefix . "posts.post_status = 'publish' AND " . $wpdb->prefix . "posts.post_type = 'gossips' AND DATE( " . $wpdb->prefix . "posts.post_date ) =  '" . date('Y-m-d') . "'");

        $totalPositiveGossipsToday = $wpdb->get_var("SELECT count(*) as total FROM " . $wpdb->prefix . "posts JOIN " . $wpdb->prefix . "postmeta entity ON ( " . $wpdb->prefix . "posts.ID = entity.post_id AND entity.meta_key = 'gossip_about' ) JOIN " . $wpdb->prefix . "postmeta type ON ( " . $wpdb->prefix . "posts.ID = type.post_id AND type.meta_key = 'feedback_type' ) WHERE entity.meta_value = $id AND type.meta_value=0 AND " . $wpdb->prefix . "posts.post_status = 'publish' AND " . $wpdb->prefix . "posts.post_type = 'gossips' AND DATE( " . $wpdb->prefix . "posts.post_date ) =  '" . date('Y-m-d') . "'");
        $totalNegativeGossipsToday = $wpdb->get_var("SELECT count(*) as total FROM " . $wpdb->prefix . "posts JOIN " . $wpdb->prefix . "postmeta entity ON ( " . $wpdb->prefix . "posts.ID = entity.post_id AND entity.meta_key = 'gossip_about' ) JOIN " . $wpdb->prefix . "postmeta type ON ( " . $wpdb->prefix . "posts.ID = type.post_id AND type.meta_key = 'feedback_type' ) WHERE entity.meta_value = $id AND type.meta_value=1 AND " . $wpdb->prefix . "posts.post_status = 'publish' AND " . $wpdb->prefix . "posts.post_type = 'gossips' AND DATE( " . $wpdb->prefix . "posts.post_date ) =  '" . date('Y-m-d') . "'");


        update_field("total_followers", $followers, $id);
        update_field("total_gossips", $totalGossips, $id);
        update_field("total_positive_gossips", $totalPositiveGossips, $id);
        update_field("total_negative_gossips", $totalNegativeGossips, $id);
        update_field("total_gossips_today", $totalGossipsToday, $id);
        update_field("total_positive_gossips_today", $totalPositiveGossipsToday, $id);
        update_field("total_negative_gossips_today", $totalNegativeGossipsToday, $id);
    }

}
