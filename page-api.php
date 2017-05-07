<?php


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
