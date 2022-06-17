<?php
if (!class_exists('SDFixture')) :
    class SDFixture
    {
        public $dateString = null;
        public $dateTime = null;
        public $timestamp = null;

        public $homeLogoUrl = null;
        public $awayLogoUrl = null;
        public $homeTeam = null;
        public $awayTeam = null;
        public $homeScore = null;
        public $awayScore = null;

        function __construct($data)
        {
            if (isset($data->date)) {
                $this->dateString = $data->date;
                $this->dateTime = new DateTime($data->date);
                $this->timestamp = $this->dateTime->getTimestamp();
            } else {
                throw new Exception('Missing date field on API response');
            }
            $this->homeLogoUrl = isset($data->homeLogoUrl) ? $data->homeLogoUrl : null;
            $this->awayLogoUrl = isset($data->awayLogoUrl) ? $data->awayLogoUrl : null;
            $this->homeTeam = isset($data->homeTeam) ? $data->homeTeam : null;
            $this->awayTeam = isset($data->awayTeam) ? $data->awayTeam : null;
            $this->homeScore = isset($data->homeScore) ? $data->homeScore : null;
            $this->awayScore = isset($data->awayScore) ? $data->awayScore : null;
        }

        public static function sort_by_date_asc($a, $b)
        {
            $atz = $a->timestamp;
            $btz =  $b->timestamp;
            return ($atz > $btz);
        }

        public static function sort_by_date_desc($a, $b)
        {
            $atz = $a->timestamp;
            $btz =  $b->timestamp;
            return ($atz < $btz);
        }
    }

endif;