<?php
require_once 'SDTeam.php';
require_once 'cache.php';

if (!function_exists('sd_get_team')) :
    function sd_get_team($teamkey, $args = array()): ?SDTeam
    {
        $data = sd_get_team_data($teamkey, $args);
        return ($data !== null) ? new SDTeam($data) : null;
    }
endif;

if (!function_exists('sd_get_team_data')) :
    function sd_get_team_data($teamkey, $args = array()): ?stdClass
    {
        $cachekey = "team_$teamkey";

        $optCacheMode = isset($args['cachemode']) ? $args['cachemode'] : CacheMode::fetchexpired;
        $optForceReset = ($optCacheMode === CacheMode::serveronly);
        $optCacheOnly = ($optCacheMode === CacheMode::cacheonly);
        $optTimeout = isset($args['timeout']) ? $args['timeout'] : 30;

        $cache = ($optCacheMode !== CacheMode::serveronly) ? sd_get_cache_data($cachekey) : null;
        $isStale = !isset($cache) || $cache['is_stale'];
        $json = null;
        $hash = isset($cache) ? $cache['hash'] : null;

        if (($isStale || $optForceReset) && !$optCacheOnly) {
            $fetchresult = sd_get_server_team_data($teamkey, $optTimeout);
            if ($fetchresult !== null) {
                $hash = sd_set_cache_data($cachekey, $fetchresult['body'], $fetchresult['expires']);
                $isStale = false;
                $json = $fetchresult['json'];
            }
        }

        if (!isset($json) && isset($cache['data'])) {
            $json = json_decode($cache['data']);
        }

        if (isset($json)) {
            $json->isStale = $isStale;
            $json->hash = $hash;
        }
        return $json;
    }
endif;

if (!function_exists('sd_get_server_team_data')) :
    function sd_get_server_team_data($teamkey, $timeout = 30): ?array
    {
        $options  = get_option('sd_plugin_options');
        $url      = $options['api_url'];
        $getargs  = array(
            'timeout' => $timeout,
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $options['api_key']
            )

        );
        $result   = array();
        $response = wp_remote_get("$url/v1/team/$teamkey", $getargs);
        if (wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body);
            if ($json !== null) {
                $result['body'] = $body;
                $result['json'] = $json;
                $result['expires'] = wp_remote_retrieve_header($response, 'expires');
                $result['last-modifed'] = wp_remote_retrieve_header($response, 'last-modifed');
                return $result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
endif;

if (!function_exists('sd_refresh_cached_team_logo')) :
    function sd_refresh_cached_team_logo($team, $url): string
    {
        $basename = strtolower(str_replace([' & ', ' '], ['_and_', '_'], $team));
        $key = 'team_logo_' . $basename;
        $cache = sd_get_cache_data($key);
        if (!isset($cache) || $cache['is_stale']) {
            $image_resource = imagecreatefrompng($url);
            $temp = tempnam(sys_get_temp_dir(), 'image_cache_');
            imagepng($image_resource, $temp);
            $image_data = file_get_contents($temp);
            sd_set_cache_data($key, base64_encode($image_data), (new DateTime())->modify(("+1 day")));
            return site_url() . '/wp-json/sportsdata/v1/logo/' . $basename . '.png';
        } else {
            return site_url() . '/wp-json/sportsdata/v1/logo/' . $basename . '.png';
        }
    }
endif;
