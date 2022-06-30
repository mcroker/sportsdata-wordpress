<?php
require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_api_fixtures_list_post')) :
    function sd_api_fixtures_list_post($request_data)
    {
        $parameters = $request_data->get_params();
        $headers = $request_data->get_headers();
        $arguments = array(
            'cachemode' => (isset($parameters['force']) && $parameters['force']) ? CacheMode::serveronly : CacheMode::fetchexpired
        );

        if (isset($parameters['team']) && isset($parameters['uid'])) {
            $team = sd_get_team($parameters['team'], $arguments);
            if (isset($team)) {
                if (sd_api_accepts($headers, 'text/html')) {
                    if (isset($parameters['hash']) && $parameters['hash'] === $team->hash) {
                        wp_send_json_error(null, 304); // Not Modified
                    } else {
                        header("Content-Type: text/html");
                        echo sd_fixture_list_render_content_inner($parameters['uid'], $team);
                        exit();
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
