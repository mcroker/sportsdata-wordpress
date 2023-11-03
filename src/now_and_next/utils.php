<?php
if (!function_exists('sd_fixtures_now_and_next')) :
    function sd_fixtures_now_and_next($teams, $attributes): array
    {

        $maxrows = isset($attributes["maxrows"]) ? $attributes["maxrows"] : 6;
        $maxfuture = isset($attributes["maxfuture"]) ? $attributes["maxfuture"] : 3;
        $oldestfirst = isset($attributes["oldestfirst"]) ? $attributes["oldestfirst"] : false;

        if (isset($attributes["teamkey"])) {

            $fixtures = [];
            foreach ($teams as $team) {
                if (isset($team) && is_array($team->allFixtures)) {
                    $fixtures = array_merge($fixtures, $team->allFixtures);
                }
            }

            $rowsdisplayed = 0;
            $nowtimestamp = time();
            $displayfixtures = [];

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
        } else {
            return [];
        }
    }
endif;
