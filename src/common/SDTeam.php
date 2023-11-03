<?php
require_once('SDFixture.php');
require_once('SDCompetition.php');

if (!class_exists('SDTeam')) :
    class SDTeam
    {
        public $isStale = null;
        public $hash = null;
        public $key = null;
        public $competitions = [];
        public $allFixtures = [];

        static function createFromCache($teamkey, $cacheMode = CacheMode::fetchexpired): ?SDTeam
        {
            if (isset($teamkey)) {
                $cachekey = "team_$teamkey";
                $cacheEntry = SDCacheEntry::get($cachekey, "api/v1/team/$teamkey/rfu", $cacheMode);
                if (!is_null($cacheEntry) && is_object($cacheEntry->data)) {
                    $json = $cacheEntry->data;
                    $json->isStale = $cacheEntry->isStale;
                    $json->hash = $cacheEntry->hash;
                    $json->key = $teamkey;
                    return new SDTeam($json);
                }
            }
            return null;
        }

        function __construct($data)
        {
            $this->key = isset($data->key) ? $data->key : null;
            $this->isStale = isset($data->isStale) ? $data->isStale : null;
            $this->hash = isset($data->hash) ? $data->hash : null;
            if (isset($data->competitions) && is_array(($data->competitions))) {
                foreach ($data->competitions as $competition) {
                    $competition = new SDCompetition($competition);
                    $this->competitions[] = $competition;
                    if (is_array($competition->fixtures)) {
                        $this->allFixtures = array_merge($this->allFixtures, $competition->fixtures);
                    }
                }
            } else {
                throw new Exception('Invalid competitions property in API result');
            }
        }
    }
endif;
