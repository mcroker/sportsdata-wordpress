<?php
if (!class_exists('CacheMode')) :
    abstract class CacheMode
    {
        const fetchexpired = 0;
        const cacheonly = 1;
        const serveronly = 2;
    }
endif;

if (!class_exists('SDCacheEntry')) :
    class SDCacheEntry
    {
        public $data = null;
        public $isStale = null;
        public $hash = null;
        public $expiry = null;
        public $lastModified = null;

        static function get($key, $url, $cacheMode): ?SDCacheEntry
        {
            // Try to sastify from cache
            $cacheEntry = null;
            if ($cacheMode !== CacheMode::serveronly) {
                $cacheEntry = SDCacheEntry::getFromCache($key);
            }
            if (!is_null($cacheEntry) && $cacheEntry->isStale === false) {
                return $cacheEntry;
            } else {
                // Fetch from sever & add to cache
                if ($cacheMode !== CacheMode::cacheonly) {
                    $data = SDCacheEntry::getFromServer($url);
                    if (!is_null($data)) {
                        $data->save($key);
                        return $data;
                    }
                }

                // Return the best we can - possibly a stale entry
                return $cacheEntry;
            }
        }

        static function getFromCache($key): ?SDCacheEntry
        {
            $transient = get_transient('sd_' . $key);
            if (isset($transient) && $transient !== false) {
                return new SDCacheEntry($transient);
            } else {
                return null;
            }
        }

        static function getFromServer($path, $timeout = 30): ?SDCacheEntry
        {
            $options  = get_option('sd_plugin_options');
            $urlBase  = $options['api_url'];
            $getargs  = array(
                'timeout' => $timeout,
                'headers' => array(
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $options['api_key']
                )

            );
            $result   = new SDCacheEntry();
            $response = wp_remote_get("$urlBase/$path", $getargs);
            if (wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $json = json_decode($body);
                if (!is_null($json)) {
                    $result->data = $json;
                    $expires = wp_remote_retrieve_header($response, 'expires');
                    $result->expiry = (is_string($expires)) ? new DateTime($expires, new DateTimeZone('UTC')) : $expires;
                    $result->lastModified = wp_remote_retrieve_header($response, 'last-modifed');
                    $result->hash = hash('ripemd160', $body);

                    return $result;
                } else {
                    return null;
                }
            } else {
                console_log("GET $urlBase/$path");
                console_log(wp_remote_retrieve_response_code($response));
                return null;
            }
        }

        function __construct($serialized_data = null)
        {
            if ($serialized_data !== null) {
                $data = unserialize($serialized_data);
                if (isset($data->data)) {
                    $this->data = $data->data;
                }
                if (isset($data->hash)) {
                    $this->hash = $data->hash;
                }
                $this->isStale = false;
                if (isset($data->expiry)) {
                    $this->expiry = $data->expiry;
                    if ($data->expiry <= new DateTime()) {
                        $data->isStale = true;
                    }
                }
                if (isset($data->lastModified)) {
                    $this->lastModified = $data->lastModified;
                }
            }
        }

        function save($key, $expires = 0): bool
        {
            return set_transient('sd_' . $key, serialize($this), $expires);
        }
    }
endif;

if (!function_exists('sd_add_force_reset_param')) :
    add_action('init', 'sd_add_force_reset_param');
    function sd_add_force_reset_param()
    {
        global $wp;
        $wp->add_query_var('force_refresh');
    }
endif;
