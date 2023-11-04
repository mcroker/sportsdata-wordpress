<?php
require_once('SDTableEntry.php');
require_once('SDFixture.php');

if (!class_exists('SDCompetition')) :
    class SDCompetition
    {
        public $id;
        public $name;
        public $displayname;
        public $table =[];
        public $competition;
        public $season;
        public $team;
        public $fixtures =[];

        function __construct($data)
        {
            if (isset($data->table) && is_array(($data->table))) {
                foreach ($data->table as $tableentry) {
                    $this->table[] = new SDTableEntry($tableentry);
                }
            } else {
                throw new Exception('Invalid table property in API result');
            }

            if (isset($data->fixtures) && is_array(($data->fixtures))) {
                foreach ($data->fixtures as $fixture) {
                    $this->fixtures[] = new SDFixture($fixture);
                }
            } else {
                throw new Exception('Invalid fixtures property in API result');
            }

            $this->competition = isset($data->competition) ? $data->competition : wp_generate_uuid4();
            $this->season = isset($data->season) ? $data->season : null;
            $this->team = isset($data->team) ? $data->team : null;

            $this->id = $this->competition . '-' . $this->season . '-' . $this->team;
            $this->name = isset($data->competitionName) ? $data->competitionName : null;

            $this->displayname = $this->season . '/' . strval(intval($this->season) - 1999) . ' ' . $this->name;
        }

        public static function sort_by_season_asc($a, $b)
        {
            $aseason = intval($a->season);
            $bseason =  intval($b->season);
            if ($aseason < $bseason) {
                return -1;
            } elseif ($aseason === $bseason) {
                return 0;
            } else {
                return 1;
            }
        }

        public static function sort_by_season_desc($a, $b)
        {
            return SDCompetition::sort_by_season_asc($a, $b) * -1;
        }
    }
endif;
