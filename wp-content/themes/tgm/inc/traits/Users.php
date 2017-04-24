<?php

trait Users {

    public function follow($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        if (!empty($id) && !empty($this->userId)) {

            $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "connections 
                        WHERE 
                        follower = $this->userId AND 
                        followee = $id
                ");

            if ($wpdb->num_rows <= 0) {
                $wpdb->insert($wpdb->prefix . "connections", ["follower" => $this->userId, "followee" => $id]);
            }


            return ['success' => 'followed'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }

    public function unfollow($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        if (!empty($id) && !empty($this->userId)) {


            $wpdb->delete($wpdb->prefix . "connections", ['follower' => $this->userId, 'followee' => $id]);



            return ['success' => 'Unfollowed'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }

    public function followEntity($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        if (!empty($id) && !empty($this->userId)) {

            $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "entity_followers 
                        WHERE 
                        follower = $this->userId AND 
                        entity = $id
                ");

            if ($wpdb->num_rows <= 0) {
                $wpdb->insert($wpdb->prefix . "entity_followers", ["follower" => $this->userId, "entity" => $id]);
            }


            return ['success' => 'followed'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }

    public function unfollowEntity($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        if (!empty($id) && !empty($this->userId)) {


            $wpdb->delete($wpdb->prefix . "entity_followers", ['follower' => $this->userId, 'entity' => $id]);



            return ['success' => 'Unfollowed'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }
    
    public function followGossip($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        if (!empty($id) && !empty($this->userId)) {

            $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "gossip_followers 
                        WHERE 
                        follower = $this->userId AND 
                        gossip = $id
                ");

            if ($wpdb->num_rows <= 0) {
                $wpdb->insert($wpdb->prefix . "gossip_followers", ["follower" => $this->userId, "gossip" => $id]);
            }


            return ['success' => 'followed'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }
    
    public function unfollowGossip($id = null) {
        global $wpdb;

        $id = is_null($id) ? $_REQUEST['id'] : $id;

        if (!empty($id) && !empty($this->userId)) {


            $wpdb->delete($wpdb->prefix . "gossip_followers", ['follower' => $this->userId, 'gossip' => $id]);



            return ['success' => 'Unfollowed'];
        } else {
            return ['error' => 'Oops ... Looks like something went wrong'];
        }
    }

    public function getMyFollowers($pageNo = null) {

        global $wpdb;

        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }

        $getfollowers = $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "connections 
                        WHERE 
                        followee = $this->userId limit $start,$limit
                ");


        $followers = [];

        foreach ($getfollowers as $f) {
            array_push($followers, $this->getUserDetails($f->follower));
        }

        return $followers;
    }

    public function getMyFollowees($pageNo = null) {

        global $wpdb;

        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }

        $getfollowers = $wpdb->get_results("
                    SELECT * FROM " . $wpdb->prefix . "connections 
                        WHERE 
                        follower = $this->userId limit $start,$limit
                ");


        $followees = [];

        foreach ($getfollowers as $f) {
            array_push($followees, $this->getUserDetails($f->followee));
        }

        return $followees;
    }

    public function getMyGossips($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'author' => $this->userId
        ]);
    }

    public function getMyFollowedEntities($pageNo = null) {
        global $wpdb;

        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }
        $limit = 20;

        $getMyFollowedEntities = $wpdb->get_results("
                    SELECT entity FROM " . $wpdb->prefix . "entity_followers 
                        WHERE 
                        follower = $this->userId limit $start,$limit
                ", 'ARRAY_A');

        if ($wpdb->num_rows <= 0)
            return array_column($getMyFollowedEntities, 'entities');

        return $this->getResults(['post_type' => 'entities',
                    'post__in' => array_column($getMyFollowedEntities, 'entity')
        ]);
    }

    public function getMyFollowedGossips($pageNo = null) {
        global $wpdb;

        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        if (empty($page)) {
            $start = 0;
        } else {
            $start = ($page - 1) * 20;
        }
        $limit = 20;

        $getMyFollowedGossips = $wpdb->get_results("
                    SELECT gossip FROM " . $wpdb->prefix . "gossip_followers 
                        WHERE 
                        follower = $this->userId limit $start,$limit
                ", 'ARRAY_A');

        if ($wpdb->num_rows <= 0)
            return array_column($getMyFollowedGossips, 'gossip');

        return $this->getResults(['post_type' => 'gossips',
                    'post__in' => array_column($getMyFollowedGossips, 'gossip')
        ]);
    }

    public function getMyComments($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;
        $comments = get_comments(['author__in' => [$this->userId],
            'offset' => $page,
            'number' => $limit,
        ]);

        foreach ($comments as $comment) {
            $comment->gossip = get_the_title($comment->comment_post_ID);
            $comment->entity = get_field("gossip_about", $comment->comment_post_ID);
        }

        return $comments;
    }

}
