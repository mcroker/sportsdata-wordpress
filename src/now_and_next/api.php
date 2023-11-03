<?php
require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_api_now_and_next_post')) :
    function sd_api_now_and_next_post($request_data)
    {
        $apiRequest = SDApiRequest::handleRequest($request_data);
        if (!is_null($apiRequest)) {
            header("Content-Type: text/html");
            echo sd_now_and_next_render_content_inner($apiRequest->teams, $apiRequest->attributes);
            exit();
        }
    }
endif;

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/now_and_next', array(
        'methods' => 'POST',
        'callback' => 'sd_api_now_and_next_post'
    ));
});
