<?php

trait Trends {

    public function topHatedEntities($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_negative_gossips',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
        ]);
    }

    public function topHatedEntitiesToday($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_negative_gossips_today',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
        ]);
    }

    public function topLovedEntities($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_positive_gossips',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
        ]);
    }

    public function topLovedEntitiesToday($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_positive_gossips_today',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
        ]);
    }

    public function topFollowedEntities($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_followers',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
        ]);
    }

    public function topGossipedEntities($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'entities',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_gossips',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
        ]);
    }

    public function latestGossips($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'order_by' => 'date',
                    'order' => 'desc'
        ]);
    }

    public function topUpvotedPositiveGossips($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_upvotes',
                    'orderby' => 'meta_value',
                    'order' => 'DESC',
                    'meta_query' => [[
                    'key' => 'feedback_type',
                    'value' => '0'
        ]]]);
    }

    public function topDownvotedPositiveGossips($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_downvotes',
                    'orderby' => 'meta_value',
                    'order' => 'DESC',
                    'meta_query' => [[
                    'key' => 'feedback_type',
                    'value' => '0'
        ]]]);
    }

    public function topUpvotedNegativeGossips($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_upvotes',
                    'orderby' => 'meta_value',
                    'order' => 'DESC',
                    'meta_query' => [[
                    'key' => 'feedback_type',
                    'value' => '1'
        ]]]);
    }

    public function topDownvotedNegativeGossips($pageNo = null) {
        $page = is_null($pageNo) ? $_REQUEST['pageNo'] : $pageNo;
        $limit = 20;

        return $this->getResults(['post_type' => 'gossips',
                    'posts_per_page' => $limit,
                    'paged' => $page,
                    'meta_key' => 'total_downvotes',
                    'orderby' => 'meta_value',
                    'order' => 'DESC',
                    'meta_query' => [[
                    'key' => 'feedback_type',
                    'value' => '1'
        ]]]);
    }

}
