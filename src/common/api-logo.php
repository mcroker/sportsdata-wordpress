<?php
require_once 'cache.php';

add_action('rest_api_init', function () {
    register_rest_route('sportsdata/v1', '/logo/(?P<team>[a-zA-Z0-9-_]+).png', array(
        'methods' => 'GET',
        'callback' => 'sd_api_team_logo_get'
    ));
});

if (!function_exists('sd_team_logo_url')) :
    function sd_team_logo_url($png): string
    {
        $force = get_query_var('force_refresh') === 'true' ? '?force_refresh=true' : '';
        return get_rest_url(null, 'sportsdata/v1/logo/' . $png . $force);
    }
endif;

if (!function_exists('sd_api_team_logo_get')) :
    function sd_api_team_logo_get($request_data)
    {
        $options  = get_option('sd_plugin_options');
        $urlBase  = $options['logo_url'];

        $parameters = $request_data->get_params();
        $team = $parameters['team'];
        $key = 'sd_team_logo_' . $team;

        $cache = null;
        $query_vars = $request_data->get_query_params();
        if (!isset($query_vars['force_refresh']) || $query_vars['force_refresh'] !== 'true') {
            $cache = get_transient($key);
        }

        if (isset($cache) && $cache !== false) {
            header('Content-Type: image/png');
            echo base64_decode($cache);
            exit;
        } else {
            $image_resource = imagecreatefrompng($urlBase . $team . ".png?alt=media");;
            if ($image_resource !== false) {
                $temp = tempnam(sys_get_temp_dir(), 'image_cache_');
                imagepng($image_resource, $temp);
                $image_data = file_get_contents($temp);
                set_transient($key, base64_encode($image_data), 60 * 60 * 24);
                header('Content-Type: image/png');
                echo $image_data;
                exit;
            }
        }
        wp_send_json(null, 404);
    }
endif;
