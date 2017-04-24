<?php

trait Users {

    public function login($uname = null, $pass = null, $playerId = null) {

        $uname = is_null($uname) ? $_REQUEST['uname'] : $uname;
        $pass = is_null($pass) ? $_REQUEST['pass'] : $pass;
        $playerId = is_null($playerId) ? $_REQUEST['playerId'] : $playerId;

        $user = wp_signon(['user_login' => $uname, 'user_password' => $pass], false);
        (isset($_REQUEST['playerId']) && !empty($_REQUEST['playerId'])) ? update_field('player_id', $playerId, "user_$user->ID") : '';


        if (is_wp_error($user)) {
            return ['error' => "Invalid Credentials!"];
        } else {
            return $this->getUserDetails($user->ID);
        }
    }

    function socialRegistration($fname = null, $lname = null, $email = null, $userId = null, $source = null, $playerId = null, $mobileNo = null, $handle = null) {

        $fname = is_null($fname) ? $_REQUEST['fname'] : $fname;
        $lname = is_null($lname) ? $_REQUEST['lname'] : $lname;
        $email = is_null($email) ? $_REQUEST['email'] : $email;
        $userId = is_null($userId) ? $_REQUEST['userId'] : $userId;
        $source = is_null($source) ? $_REQUEST['source'] : $source;
        $playerId = is_null($playerId) ? $_REQUEST['playerId'] : $playerId;
        $mobileNo = is_null($mobileNo) ? $_REQUEST['mobileNo'] : $mobileNo;
        $handle = is_null($handle) ? $_REQUEST['handle'] : $handle;

        $frm = $source == 2 ? "fb" : ($source == 3 ? "gl" : "");


        if ($email == '')
            return ['error' => "Invalid Email Id!"];

        if ($user = get_user_by("email", $email)) {
            (isset($playerId) && !empty($playerId)) ? update_field('player_id', $playerId, "user_" . $user->ID) : '';
            return $this->getUserDetails($user->ID);
        }

        if (!empty($handle) && username_exists($handle))
            return ['error' => "Handle Already Taken"];


        $args = array(
            'first_name' => $fname,
            'last_name' => $lname,
            'user_email' => $email,
            'user_login' => empty($handle) ? $frm . $userId : $handle,
            'role' => 'subscriber',
            'nickname' => $fname . " " . $lname,
            'display_name' => $fname . " " . $lname,
            'rich_editing' => FALSE
        );

        $user = wp_insert_user($args);

        update_field('source', $source, 'user_' . $user);
        update_field('mobile_no', $mobileNo, 'user_' . $user);
        update_field('profile_picture', $userId, 'user_' . $user);
        (isset($playerId) && !empty($playerId)) ? update_field('player_id', $playerId, "user_" . $user) : '';


        return $this->getUserDetails($user);
    }

    function manualRegistration($fname = null, $lname = null, $email = null, $pass = null, $handle = null, $playerId = null, $mobileNo = null) {

        $fname = is_null($fname) ? $_REQUEST['fname'] : $fname;
        $lname = is_null($lname) ? $_REQUEST['lname'] : $lname;
        $email = is_null($email) ? $_REQUEST['email'] : $email;
        $pass = is_null($pass) ? $_REQUEST['pass'] : $pass;
        $handle = is_null($handle) ? $_REQUEST['handle'] : $handle;
        $mobileNo = is_null($mobileNo) ? $_REQUEST['mobileNo'] : $mobileNo;
        $source = 1;
        $playerId = is_null($playerId) ? $_REQUEST['playerId'] : $playerId;

        if (email_exists($email))
            return ['msg' => "Email Id already exist", 'errorType' => 'danger'];

        if (username_exists($handle) && !empty($handle) && isset($handle))
            return ['msg' => "Handle already taken", 'errorType' => 'danger'];

        if (empty($email) || empty($pass) || empty($fname) || empty($lname))
            return ['msg' => "Please fill all the compulsory fields!", 'errorType' => 'danger'];


        $args = array(
            'first_name' => $fname,
            'last_name' => $lname,
            'user_email' => $email,
            'user_pass' => $pass,
            'user_login' => $handle,
            'role' => 'subscriber',
            'nickname' => $fname . " " . $lname,
            'display_name' => $fname . " " . $lname,
            'rich_editing' => FALSE
        );

        $user = wp_insert_user($args);

        update_field('source', $source, 'user_' . $user);
        update_field('mobile_no', $mobileNo, 'user_' . $user);
        (isset($playerId) && !empty($playerId)) ? update_field('player_id', $playerId, "user_" . $user) : '';

        return $this->getUserDetails($user);
    }

    function profileUpdate($fname = null, $lname = null, $pass = null, $handle = null, $playerId = null, $mobileNo = null) {
        global $wpdb;

        $fname = is_null($fname) ? $_REQUEST['fname'] : $fname;
        $lname = is_null($lname) ? $_REQUEST['lname'] : $lname;
        $pass = is_null($pass) ? $_REQUEST['pass'] : $pass;
        $handle = is_null($handle) ? $_REQUEST['handle'] : $handle;
        $mobileNo = is_null($mobileNo) ? $_REQUEST['mobileNo'] : $mobileNo;
        $source = 1;
        $playerId = is_null($playerId) ? $_REQUEST['playerId'] : $playerId;

        $user = get_user_by('ID', $this->userId)->data;



        if (empty($fname) || empty($lname))
            return ['msg' => "Please fill all the compulsory fields!", 'errorType' => 'danger'];

        $args = [
            'ID' => $user->ID,
            'first_name' => $fname,
            'last_name' => $lname,
            'role' => 'subscriber',
            'nickname' => $fname . " " . $lname,
            'display_name' => $fname . " " . $lname,
            'rich_editing' => FALSE
        ];

        !empty($pass) ? $args['user_pass'] = $pass : '';

        if (!empty($handle) && $handle != $user->user_login && username_exists($handle))
            return ['msg' => "Handle already taken", 'errorType' => 'danger'];


        if (!empty($handle) && $handle != $user->user_login && !username_exists($handle))
            $wpdb->update($wpdb->users, ['user_login' => $handle], ['ID' => $user->ID]);




        $user = wp_update_user($args);
        update_field('mobile_no', $mobileNo, 'user_' . $user);
        (isset($playerId) && !empty($playerId)) ? update_field('player_id', $playerId, "user_" . $user) : '';

        return $this->getUserDetails($user);
    }

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
