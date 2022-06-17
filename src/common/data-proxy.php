<?php
require_once 'SDTeam.php';

abstract class CacheMode
{
    const fetchexpired = 0;
    const cacheonly = 1;
    const serveronly = 2;
}

if (!function_exists('sd_get_team')) :
    function sd_get_team($teamkey, $args = array()): ?SDTeam
    {
        $data = sd_get_team_data($teamkey, $args);
        return ($data !== null) ? new SDTeam($data) : null;
        return new SDTeam(sd_get_team_data($teamkey, $args));
    }
endif;

if (!function_exists('sd_get_team_data')) :
    function sd_get_team_data($teamkey, $args = array()): ?stdClass
    {
        $key = "sd_team_$teamkey";
        $stalekey = "sd_team_stale_$teamkey";

        $optCacheMode = isset($args['cachemode']) ? $args['cachemode'] : CacheMode::fetchexpired;
        $optForceReset = ($optCacheMode === CacheMode::serveronly);
        $optCacheOnly = ($optCacheMode === CacheMode::cacheonly);
        $optTimeout = isset($args['timeout']) ? $args['forceReftimeoutresh'] : 30;

        $isStale = false;
        $data = get_transient($key);
        if ($data === false && $optCacheOnly) {
            $data = get_transient($stalekey);
            $isStale = true;
        }

        if (($data === false || $optForceReset) && !$optCacheOnly) {
            $options  = get_option('sd_plugin_options');
            $url      = $options['api_url'];
            $getargs  = array(
                'timeout'     => $optTimeout
            );
            $response = wp_remote_get("$url/v1/team/$teamkey", $getargs);
            $body     = wp_remote_retrieve_body($response);
            $json     = json_decode($body);
            $json->isStale = false;

            set_transient($key, $body, isset($json->cacheFor) ? $json->cacheFor : 600); // default 10mins

            $staledata = get_transient($stalekey);
            if ($body !== $staledata) {
                $json->isUpdated = true;
                set_transient($stalekey, $body, 0); // Keep a copy forever
            } else {
                $json->isUpdated = false;
            }

        } elseif ($data !== false) {
            $json = json_decode($data);
            $json->isStale = $isStale;
            $json->isUpdated = false;

        } else {
            $json = null;
        }

        return $json;
    }
endif;
