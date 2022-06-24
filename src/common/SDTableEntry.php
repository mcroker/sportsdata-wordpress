<?php
if (!class_exists('SDTableEntry')) :
    class SDTableEntry
    {
        public $position;
        public $team;
        public $played;
        public $won;
        public $drew;
        public $lost;
        public $pointsFor;
        public $pointsAgainst;
        public $tryBonus;
        public $losingBonus;
        public $leaguePoints;

        function __construct($data)
        {
            $this->position = isset($data->position) ? $data->position : null;
            $this->team = isset($data->team) ? $data->team : null;
            $this->played = isset($data->played) ? $data->played : null;
            $this->won = isset($data->won) ? $data->won : null;
            $this->drew = isset($data->drew) ? $data->drew : null;
            $this->lost = isset($data->lost) ? $data->lost : null;
            $this->pointsFor = isset($data->pointsFor) ? $data->pointsFor : null;
            $this->pointsAgainst = isset($data->pointsAgainst) ? $data->pointsAgainst : null;
            $this->tryBonus = isset($data->tryBonus) ? $data->tryBonus : null;
            $this->losingBonus = isset($data->losingBonus) ? $data->losingBonus : null;
            $this->leaguePoints = isset($data->leaguePoints) ? $data->leaguePoints : null;
        }
    }
endif;
