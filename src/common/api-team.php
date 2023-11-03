<?php
require_once 'cache.php';

if (!function_exists('sd_api_team_get')) :
    function sd_api_team_get($request_data)
    {
        $parameters = $request_data->get_params();
        $data = SDTeam::createFromCache($parameters['team']);
        wp_send_json($data);
    }
endif;



add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/team/(?P<team>[a-zA-Z0-9-_]+)', array(
        'methods' => 'GET',
        'callback' => 'sd_api_team_get'
    ));
});
