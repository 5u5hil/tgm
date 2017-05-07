<?php

trait Cron {

    function updateEntityStats() {
        global $wpdb;
        $entities = $wpdb->get_results("SELECT id
                                        FROM  " . $wpdb->prefix . "posts
                                        WHERE post_type =  'entities'
                                        AND post_status =  'publish'");

        foreach ($entities as $entity) {
            $this->calculateEntityStats($entity->id);
        }
    }

    function updateEntityNews() {
        global $wpdb;
        $entities = $wpdb->get_results("SELECT id,post_title as title
                                        FROM  " . $wpdb->prefix . "posts
                                        WHERE post_type =  'entities'
                                        AND post_status =  'publish'");

        $news = [];
        foreach ($entities as $entity) {
            $news = $this->xmlToJson("https://news.google.com/news?&output=rss&q=" . str_replace(" ", "+", strtolower(trim($entity->title))));

            foreach ($news as $new) {
                $url = explode("cluster=", $new['guid']);
                $wpdb->insert($wpdb->prefix . "news", ['entity' => $entity->id,
                    'title' => $new['title'],
                    'content' => $new['description'],
                    'time' => strftime("%Y-%m-%d %H:%M:%S", strtotime($new['pubDate'])),
                    'url' => $url[1]
                ]);
            }
        }
    }

    function xmlToJson($url) {
        $fileContents = file_get_contents($url);
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents, 'SimpleXMLElement', LIBXML_NOCDATA);


        $news = json_decode(json_encode($simpleXml->channel), TRUE);
        return $news['item'];
    }

}
