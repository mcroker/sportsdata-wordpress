<?php
require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_api_fixtures_list_post')) :
    function sd_api_fixtures_list_post($request_data)
    {
        $apiRequest = SDApiRequest::handleRequest($request_data);
        if (!is_null($apiRequest)) {
            if (isset($apiRequest->team)) {
                header("Content-Type: text/html");
                echo sd_fixture_list_render_content_inner($apiRequest->uid, $apiRequest->team);
                exit();
            } else {
                wp_send_json_error(null, 404); // Not Found
            }
        }
    }
endif;

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/fixtures_list', array(
        'methods' => 'POST',
        'callback' => 'sd_api_fixtures_list_post'
    ));
});
