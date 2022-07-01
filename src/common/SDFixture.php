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
        public $isHome = null;

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
            $this->isHome = isset($data->isHome) ? $data->isHome : null;
        }

        public static function sort_by_date_asc($a, $b)
        {
            $atz = $a->timestamp;
            $btz =  $b->timestamp;
            if ($atz < $btz) {
                return -1;
            } elseif ($atz === $btz) {
                return 0;
            } else {
                return 1;
            }
        }

        public static function sort_by_date_desc($a, $b)
        {
            return SDFixture::sort_by_date_asc($a, $b) * -1;
        }
    }

endif;
