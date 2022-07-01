<?php
if (!function_exists('sd_fixtures_now_and_next')) :
    function sd_fixtures_now_and_next($fixtures, $maxrows = null, $maxfuture = null, $oldestfirst = false)
    {
        $maxrows = (isset($maxrows)) ? $maxrows : 6;
        $maxfuture = (isset($maxfuture)) ? $maxfuture : 3;

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
    }
endif;
