<?php
require_once('SDFixture.php');
require_once('SDCompetition.php');

if (!class_exists('SDTeam')) :
    class SDTeam
    {
        public $isStale = null;
        public $hash = null;
        public $competitions = [];
        public $allFixtures = [];

        function __construct($data)
        {

            $this->isStale = isset($data->isStale) ? $data->isStale : null;
            $this->hash = isset($data->hash) ? $data->hash : null;
            if (isset($data->competitions) && is_array(($data->competitions))) {
                foreach ($data->competitions as $competition) {
                    $competition = new SDCompetition($competition);
                    $this->competitions[] = $competition;
                    $this->allFixtures = array_merge($this->allFixtures, $competition->fixtures);
                }
            } else {
                throw new Exception('Invalid competitions property in API result');
            }
        }
    }
endif;
