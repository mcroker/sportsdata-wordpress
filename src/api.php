<?php
require_once plugin_dir_path(__FILE__) . 'common/index.php';

if (!function_exists('sd_api_teamdata')) :
    function sd_api_teamdata($request_data)
    {
        $parameters = $request_data->get_params();
        $data = sd_get_team_data($parameters['team']);
        wp_send_json($data);
    }
endif;

if (!function_exists('sd_api_team_post')) :
    function sd_api_team_post($request_data)
    {
        $parameters = $request_data->get_params();
        $team = sd_get_team($parameters['team']);
        $maxfixtures = $request_data['maxfixtures'];
        $data = $team->fixtures_now_and_next($maxfixtures, 2);
        wp_send_json($data);
    }
endif;

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/team/(?P<team>[a-zA-Z0-9-]+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'sd_api_teamdata'
    ));
    register_rest_route('sportsdata/v1', '/team/(?P<team>[a-zA-Z0-9-]+)', array(
        'methods' => 'POST',
        'callback' => 'sd_api_team_post'
    ));
});
