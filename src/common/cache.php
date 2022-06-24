<?php
abstract class CacheMode
{
    const fetchexpired = 0;
    const cacheonly = 1;
    const serveronly = 2;
}

if (!function_exists('sd_add_force_reset_param')) :
    add_action('init', 'sd_add_force_reset_param');
    function sd_add_force_reset_param() {
        global $wp;
        $wp->add_query_var('force_refresh');
    }
endif;

if (!function_exists('sd_get_cache_data')) :
    function sd_get_cache_data($key): ?array 
    {
        $transient = get_transient('sd_' . $key);
        if ($transient !== false) {
            $response = array();
            $response['data'] = $transient['data'];
            if (isset($transient['expiry']) && (new DateTime($transient['expiry']) <= new DateTime())) {
                $response['is_stale'] = true;
            } else {
                $response['is_stale'] = false;
            }
            return $response;
        } else {
            return null;
        }
    }
endif;

if (!function_exists('sd_set_cache_data')) :
    // Returns true if the data has been modifed
    function sd_set_cache_data($key, $value, $expire): bool
    {
        $transient = array(
            'data' => $value,
            'expiry' => $expire
        );
        $existing = get_transient('sd_' . $key);
        set_transient('sd_' . $key, $transient);
        return (is_array($existing) && $existing['data'] !== $value);
    }
endif;
