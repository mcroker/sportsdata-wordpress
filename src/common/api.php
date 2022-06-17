<?php
require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_api_team_get')) :
    function sd_api_team_get($request_data)
    {
        $parameters = $request_data->get_params();
        $data = sd_get_team_data($parameters['team']);
        wp_send_json($data);
    }
endif;

if (!function_exists('sd_api_accepts')) :
    function sd_api_accepts($headers, $content)
    {
        return 0 !== count(array_filter($headers['accept'], fn ($value) => str_contains(strtolower($value), $content)));
    }
endif;

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/team/(?P<team>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'sd_api_team_get'
    ));
});
