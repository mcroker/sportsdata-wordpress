<?php
if (!class_exists('SDApiRequest')) :
    class SDApiRequest
    {

        public $uid;
        public $teams = array();
        public $team = null;
        public $attributes;

        private $data = null;
        private $body = null;
        private $request = null;
        private $isStale = false;

        static function handleRequest($request): ?SDApiRequest
        {

            $apiRequest = new SDApiRequest($request);

            if (is_null($apiRequest->data)) {
                wp_send_json_error(null, 400); // Bad request
                return null;
            }

            if (!isset($apiRequest->uid)) {
                wp_send_json_error(null, 400); // Bad request
                return null;
            }

            if (!$apiRequest->accepts('text/html')) {
                wp_send_json_error(null, 415); // Unsupported Media
                return null;
            }

            if (!$apiRequest->isStale()) {
                wp_send_json_error(null, 304); // Not Modified
                return null;
            }

            return $apiRequest;
        }

        function __construct($request = null)
        {
            if (!is_null($request)) {
                $this->request = $request;
                $this->body = $request->get_body();
                $this->data = json_decode($this->body);
            }

            if (isset($this->data->uid)) {
                $this->uid = $this->data->uid;
            }

            if (is_object($this->data->attributes)) {
                $this->attributes = $this->data->attributes;
            }

            if (isset($this->data->hash)) {
                $cachemode = ($this->data->force) ? CacheMode::serveronly : CacheMode::fetchexpired;
                foreach ($this->data->hash as $teamkey => $teamhash) {
                    $team = SDTeam::createFromCache($teamkey, $cachemode);
                    if (isset($team->hash) && $teamhash !== $team->hash) {
                        $this->isStale = true;
                    }
                    $this->teams[$teamkey] = $team;
                }
            }

            if (isset($this->data->teamkey)) {
                $this->team = $this->teams[$this->data->teamkey];
            }
            return null;
        }

        function accepts($accepted_media = 'text/html'): bool
        {
            $headers = $this->request->get_headers();
            if (sd_api_accepts($headers, 'text/html') && 'text/html' === $accepted_media) {
                return true;
            } elseif (sd_api_accepts($headers, 'application/json') && 'application/json' === $accepted_media) {
                return true;
            } else {
                return null;
            }
        }

        function isStale(): bool
        {
            return $this->isStale;
        }
    }
endif;
