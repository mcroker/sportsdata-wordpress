<?php
abstract class CacheMode
{
    const fetchexpired = 0;
    const cacheonly = 1;
    const serveronly = 2;
}

if (!function_exists('sd_add_force_reset_param')) :
    add_action('init', 'sd_add_force_reset_param');
    function sd_add_force_reset_param()
    {
        global $wp;
        $wp->add_query_var('force_refresh');
    }
endif;

if (!function_exists('sd_get_cache_data')) :
    function sd_get_cache_data($key, $force_fail = false): ?array
    {
        if ($force_fail) {
            delete_transient('sd_' . $key);
        }
        $transient = get_transient('sd_' . $key);
        if ($transient !== false) {
            $response = array();
            $response['data'] = $transient['data'];
            $response['is_stale'] = false;
            $response['hash'] = $transient['hash'];
            if (isset($transient['expiry']) && $transient['expiry'] <= new DateTime()) {
                $response['is_stale'] = true;
            }

            return $response;
        } else {
            return null;
        }
    }
endif;

if (!function_exists('sd_set_cache_data')) :
    // Returns true if the data has been modifed
    // Expire UTC Timestring
    function sd_set_cache_data($key, $value, $expire): string
    {
        $transient = array(
            'data' => $value,
            'expiry' => (is_string($expire)) ? new DateTime($expire, new DateTimeZone('UTC')) : $expire,
            'hash' => hash('ripemd160', $value)
        );
        set_transient('sd_' . $key, $transient);
        return $transient['hash'];
    }
endif;
