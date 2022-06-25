<?php
require_once('SDFixture.php');
require_once('SDCompetition.php');

if (!class_exists('SDTeam')) :
    class SDTeam
    {
        public $isStale = null;
        public $isUpdated = null;
        public $competitions = [];
        public $allFixtures = [];

        function __construct($data)
        {

            $this->isStale = isset($data->isStale) ? $data->isStale : null;
            $this->isUpdated = isset($data->isUpdated) ? $data->isUpdated : null;
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

        function fixtures_now_and_next($maxrows = null, $maxfuture = null, $oldestfirst = false)
        {
            $maxrows = (isset($maxrows)) ? $maxrows : 6;
            $maxfuture = (isset($maxfuture)) ? $maxfuture : 3;

            $rowsdisplayed = 0;
            $nowtimestamp = time();
            $displayfixtures = [];
            $fixtures = $this->allFixtures;

            // Next $maxfuture Future fixtures
            uasort($fixtures, array('SDFixture', 'sort_by_date_asc'));
            foreach ($fixtures as $fixture) {
                if ($rowsdisplayed < $maxfuture && $rowsdisplayed < $maxrows && $fixture->timestamp >= $nowtimestamp) {
                    $rowsdisplayed += 1;
                    $displayfixtures[] = $fixture;
                }
            }
            // Past $maxrows-$maxfuture results
            uasort($fixtures, array('SDFixture', 'sort_by_date_desc'));
            foreach ($fixtures as $fixture) {
                if ($rowsdisplayed < $maxrows && $fixture->timestamp < $nowtimestamp) {
                    $rowsdisplayed += 1;
                    $displayfixtures[] = $fixture;
                }
            }
            // Display Fixtures
            if ($oldestfirst) {
                uasort($displayfixtures, array('SDFixture', 'sort_by_date_asc'));
            } else {
                uasort($displayfixtures, array('SDFixture', 'sort_by_date_desc'));
            }
            return $displayfixtures;
        }
    }
endif;
