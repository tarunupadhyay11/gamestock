<?php
namespace App\Sportradar;

require_once 'SportsData.php';


class Sportradar extends SportsData {
	
	
    //private $base_url = "api.sportradar.us/nfl-";
    private $base_url = "api.sportradar.us/nfl/official/";
	private $version; // Whole number (sequential, starting with the number 1)
	private $season; // Preseason (PRE), Regular Season (REG), Postseason (PST)
	private $season_valid = array('PRE','REG','PST');
	private $week; // 1 - 17 (Week 0 of Preseason is Hall of Fame game)
	private $team_valid = array('GB','SEA','NO','ATL','NE','MIA','JAC','PHI','OAK','NYJ','BUF','CHI','TEN','KC','CLE','PIT','WAS','HOU','CIN','BAL','MIN','STL','CAR','TB','SF','DAL','IND','DEN','NYG','DET','SD','ARI');
	
	public function __construct($api_key,$version,$access_level,$format = 'json',$secure = false) {
		$this->api_key = $api_key;
		// $this->season = $this->check_season($season);
		// $this->year = $this->check_year($year);
		// $this->week = $this->check_week($week);
        //$this->version = $this->check_version($version);
        $this->version = $version;
		$this->access_level = $this->check_access_level($access_level);
		$this->format = $this->check_format($format);
		$this->url_protocol = $secure ? 'https://' : 'http://';
    }
	
	private function check_season($season) {
		if(!in_array($season,$this->season_valid)) throw new Exception('Season is not valid. Must be set as one of the following: '.implode(', ',$this->season_valid));
		return $season;
	}
	
	private function check_week($week) {
		if($week < 0 || $week > 17) throw new Exception('Week is invalid. Must be integer between 0 and 17');
		return $week;
	}
	
	private function check_version($version) {
		if(!is_int($version)) throw new Exception('Version is invalid. Must be whole number');
		return $version;
	}
	
}