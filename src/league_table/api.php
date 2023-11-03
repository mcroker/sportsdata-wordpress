<?php
require_once plugin_dir_path(__FILE__) . '../common/index.php';
require_once 'template.php';

if (!function_exists('sd_api_league_table_post')) :
    function sd_api_league_table_post($request_data)
    {
        $apiRequest = SDApiRequest::handleRequest($request_data);
        if (!is_null($apiRequest)) {
            if (isset($apiRequest->team)) {
                header("Content-Type: text/html");
                echo sd_league_table_render_content_inner($apiRequest->uid, $apiRequest->team);
                exit();
            } else {
                wp_send_json_error(null, 404); // Not Found
            }
        }
    }
endif;

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/league_table', array(
        'methods' => 'POST',
        'callback' => 'sd_api_league_table_post'
    ));
});
