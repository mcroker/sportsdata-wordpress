<?php
require_once 'SDTeam.php';

if (!function_exists('sd_get_team_data')) :
    function sd_get_team_data($teamkey, $args = array()): ?stdClass
    {
        $key = "sd_team_$teamkey";
        $stalekey = "sd_team_stale_$teamkey";
        $data = get_transient($key);

        $optForceReset = isset($args['forceRefresh']) ? $args['forceRefresh'] : false;
        $optTimeout = isset($args['timeout']) ? $args['forceReftimeoutresh'] : 30;

        if ($data === false || $optForceReset) {
            $options  = get_option('sd_plugin_options');
            $url      = $options['api_url'];
            $getargs  = array(
                'timeout'     => $optTimeout
            );
            $response = wp_remote_get("$url/v1/team/$teamkey", $getargs);
            $body     = wp_remote_retrieve_body($response);
            $json     = json_decode($body);
            set_transient($key, $body, isset($json->cacheFor) ? $json->cacheFor : 600); // default 10mins
            set_transient($stalekey, $body, 0); // default 10mins
        } else {
            $json     = json_decode($data);
        }
        return $json;
    }
endif;

if (!function_exists('sd_get_team')) :
    function sd_get_team($teamkey, $forceRefresh = false): ?SDTeam
    {
        return new SDTeam(sd_get_team_data($teamkey, $forceRefresh));
    }
endif;
