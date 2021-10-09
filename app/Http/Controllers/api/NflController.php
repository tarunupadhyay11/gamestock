<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sportradar\Sportradar;

class nflController extends Controller
{
    private $base_url = "api.sportradar.us/nfl/official/";
	private $version; // Whole number (sequential, starting with the number 1)
	private $season; // Preseason (PRE), Regular Season (REG), Postseason (PST)
	private $season_valid = array('PRE','REG','PST');
	private $week; // 1 - 17 (Week 0 of Preseason is Hall of Fame game)
	private $team_valid = array('GB','SEA','NO','ATL','NE','MIA','JAC','PHI','OAK','NYJ','BUF','CHI','TEN','KC','CLE','PIT','WAS','HOU','CIN','BAL','MIN','STL','CAR','TB','SF','DAL','IND','DEN','NYG','DET','SD','ARI');
    private $sportrader;
    
	public function __construct($secure = false) {
		$this->api_key = 'erpuk4xjayjn6dkzxhcwrus6';
        $this->version = 'v5';
		$this->access_level = 'trial';
		$this->format = 'json';
        $this->url_protocol = $secure ? 'https://' : 'http://';
        $this->sportrader = new Sportradar($this->api_key ,$this->version,$this->format,false);
    }
    
	
	// private function check_season($season) {
	// 	if(!in_array($season,$this->season_valid)) throw new Exception('Season is not valid. Must be set as one of the following: '.implode(', ',$this->season_valid));
	// 	return $season;
	// }
	
	// private function check_week($week) {
	// 	if($week < 0 || $week > 17) throw new Exception('Week is invalid. Must be integer between 0 and 17');
	// 	return $week;
	// }
	
	// private function check_version($version) {
	// 	if(!is_int($version)) throw new Exception('Version is invalid. Must be whole number');
	// 	return $version;
    // }
    

    public function standings(Request $request) {
        $this->year = $request->year;
		$this->send_url = $this->base_url.$this->access_level.'/'.$this->version.'/en/seasons/'.$this->year.'/standings.'.$this->format.'?api_key='.$this->api_key;
        $curlData = $this->sportrader->curl($this->send_url);
        $data['response'] = true;
        $data['message'] = 'NFL STANDING DATA';
        $data['data'] = $curlData;
        return response()->json($data);
	}
	
	// private function check_team($team) {
	// 	if(!in_array($team,$this->team_valid)) throw new Exception('Team is not valid. Must be set as one of the following: '.implode(', ',$this->team_valid));
	// 	return $team;
	// }
	
	
	
	// public function weekly_schedule() {
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/schedule.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function season_schedule() {
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/schedule.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function game_statistics($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/statistics.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function game_summary($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/summary.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function play_by_play($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/pbp.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function play_summary($away_team, $home_team, $play_id) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/plays/'.$play_id.'.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function game_boxscore($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/boxscore.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function extended_boxscore($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/extended-boxscore.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function weekly_boxscore() {
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/boxscore.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function game_roster($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/roster.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function team_hierarchy() {
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/teams/hierarchy.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function team_roster($team) {
	// 	$team = $this->check_team($team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/teams/'.$team.'/roster.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function injuries($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/injuries.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function game_depth_chart($away_team, $home_team) {
	// 	$away_team = $this->check_team($away_team);
	// 	$home_team = $this->check_team($home_team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/'.$away_team.'/'.$home_team.'/depthchart.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function team_depth_chart($team) {
	// 	$team = $this->check_team($team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/teams/'.$team.'/depthchart.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function weekly_league_leaders() {
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/'.$this->year.'/'.$this->season.'/'.$this->week.'/leaders.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
    
	
	// public function rankings() {
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/teams/'.$this->year.'/'.$this->season.'/rankings.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
	// }
	
	// public function seasonal_statistics($team) {
	// 	$team = $this->check_team($team);
	// 	$this->send_url = $this->base_url.$this->access_level.$this->version.'/teams/'.$team.'/'.$this->year.'/'.$this->season.'/statistics.'.$this->format.'?api_key='.$this->api_key;
	// 	return $this->sportrader->curl($this->send_url);
    // }
}
