<?php
require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_api_fixtures_list_post')) :
    function sd_api_fixtures_list_post($request_data)
    {
        $parameters = $request_data->get_params();
        $headers = $request_data->get_headers();

        if (isset($parameters['team'])) {
            $team = sd_get_team($parameters['team']);
            if (isset($team)) {
                if (sd_api_accepts($headers, 'text/html')) {
                    if ($team->isUpdated) {
                        header("Content-Type: text/html");
                        echo sd_now_and_next_render_tbody_inner($team);
                        exit();
                    } else {
                        wp_send_json_error(null, 304); // Not Modified
                    }
                } else {
                    wp_send_json_error(null, 415); // Unsupported Media
                }
            } else {
                wp_send_json_error(null, 404); // Not found
            }
        } else {
            wp_send_json_error(null, 400); // Bad request
        }
    }
endif;

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/team/(?P<team>[a-zA-Z0-9-_]+)/fixtures_list', array(
        'methods' => 'POST',
        'callback' => 'sd_api_fixtures_list_post'
    ));
});
