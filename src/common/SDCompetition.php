<?php
require_once('SDTableEntry.php');

if (!class_exists('SDCompetition')) :
    class SDCompetition
    {
        public $id;
        public $name;
        public $display;
        public $table;
        public $competition;
        public $season;
        public $team;

        function __construct($data)
        {
            if (isset($data->table) && is_array(($data->table))) {
                foreach ($data->table as $tableentry) {
                    $this->table[] = new SDTableEntry($tableentry);
                }
            } else {
                throw new Exception('Invalid table property in API result');
            }

            $this->competition = isset($data->competition) ? $data->competition : wp_generate_uuid4();
            $this->season = isset($data->season) ? $data->season : null;
            $this->team = isset($data->team) ? $data->team : null;

            $this->id = $this->competition . '-' . $this->season . '-' . $this->team;
            $this->name = isset($data->name) ? $data->name : null;

            $this->displayname = $this->season . '/' . strval(intval($this->season) - 1999) . ' ' . $this->name;
        }
    }
endif;
