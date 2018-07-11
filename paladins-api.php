<?php
require 'vendor/autoload.php';

class HiRezAPI {
    var $devKey; 
    var $devAuthID;
    var $platform;
    var $language;

    public function devkey($devkey){
        $this->devkey = $devkey;
    }
    public function devAuthID($devAuthID){
        $this->devAuthID = $devAuthID;
    }
    public function platform($input = 'pc'){
        $platforms = array(
            'pc'    => 'http://api.paladins.com/paladinsapi.svc',
            'xbox'  => 'http://api.xbox.paladins.com/paladinsapi.svc',
            'ps4'   => 'http://api.ps4.paladins.com/paladinsapi.svc'
        );
        if (!array_key_exists($input, $platforms)){
            $input = 'pc';
        }
        $this->platform = $platforms[$input];
    }
    public function language($langcode = 'en'){
        $langArray = array(
            'en'    => '1',
            'ger'   => '2',
            'fr'    => '3',
            'ch'    => '5',
            'esp'   => '7',
            'esp-latin' => '9',
            'pt'    => '10',
            'ru'    => '11',
            'pl'    => '12',
            'tr'    => '13'
        );
        $this->language = $langArray[$langcode];
    }
    private function GenerateSignature ($method) {
        $str = $this->devkey . $method . $this->devAuthID . $this->TimeStamp ();
        return md5($str);
    }
    private function TimeStamp () {
        return gmdate('YmdHis');
    }
    private function Session () {
        $client = new GuzzleHttp\Client();
        return $client->request("GET", $this->platform . "/createsessionjson/" . $this->devkey . "/" . $this->GenerateSignature ("createsession") . '/' . $this->TimeStamp ());
    }

    //APIs - Connectivity
    public function Ping(){
        $client = new GuzzleHttp\Client();
        return $client->request("GET", $this->platform . "/pingJson")->getBody();
    }
    public function CreateSession() {
        $memcache = new Memcache;
        $memcache->connect('127.0.0.1', 11211) or die ("Could not connect");
    
        if($memcache->get('sessionid') == ''){ 
            $session = json_decode($this->Session()->getBody(), true);
            $memcache->set('sessionid', $session['session_id'],false,900);  
            return $memcache->get('sessionid');  
        }else{
            return $memcache->get('sessionid');
        }
    }
    public function TestSession(){
        $client = new GuzzleHttp\Client();
        return $client->request("GET", $this->platform . "/testsessionJson/" . $this->devkey . "/" . $this->GenerateSignature("testsession") . "/" . $this->CreateSession() . "/" . $this->TimeStamp() )->getBody();
    }
    public function GetHirezServerStatus(){
        $client = new GuzzleHttp\Client();
        return $client->request("GET", $this->platform . "/gethirezserverstatusJson/" . $this->devkey . "/" . $this->GenerateSignature("gethirezserverstatus") . "/" . $this->CreateSession() . "/" . $this->TimeStamp() )->getBody();
    }

    //APIs
    public function GetDataUsed () {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getdatausedJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getdataused") . "/" . $this->CreateSession() . "/" . $this->TimeStamp ())->getBody();
    }
    public function GetDemoDetails ($matchId) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getdemodetailsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getdemodetails") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $matchId)->getBody();
    }
    public function GetEsportsProLeagueDetails () {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getesportsproleaguedetailsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getesportsproleaguedetails") . "/" . $this->CreateSession() . "/" . $this->TimeStamp ())->getBody();
    }
    public function GetFriends ($player) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getfriendsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getfriends") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $player)->getBody();
    }
    public function GetChampionRanks ($player) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getchampionranksJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getchampionranks") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $player)->getBody();
    }
    public function GetChamps(){
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getchampionsJson/" . $this->devkey . "/" . $this->GenerateSignature ("getchampions") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $this->language)->getBody();
    }
    public function GetChampionSkins($champName = 'Androxus'){
        $json = $this->GetChamps();
        $champs = json_decode($this->GetChamps(), true);
        foreach( $champs as $index=>$json ){
            if( $champName == $json['Name']){
                $champID = json_decode($json['id'], true);
            }
        }
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getchampionskinsJson/" . $this->devkey . "/" . $this->GenerateSignature ("getchampionskins") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $champID . "/" . $this->language)->getBody();
    }
    public function GetItems () {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getitemsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getitems") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $this->language)->getBody();
    }
    public function GetMatchDetails ($matchId) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getmatchdetailsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getmatchdetails") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $matchId)->getBody();
    }
    public function GetMatchPlayerDetails ($matchId) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getmatchplayerdetailsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getmatchplayerdetails") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $matchId)->getBody();
    }
    public function GetMatchHistory ($player) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getmatchhistoryJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getmatchhistory") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $player)->getBody();
    }
    public function GetMotd () {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getmotdJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getmotd") . "/" . $this->CreateSession() . "/" . $this->TimeStamp ())->getBody();
    }
    public function GetPlayer ($player) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getplayerJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getplayer") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $player)->getBody();
    }
    public function GetPlayerLoadouts ($player) {
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getplayerloadoutsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getplayerloadouts") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $player . "/" . $this->language)->getBody();
    }
    public function GetPlayerStatus ($player) {
        /*
            0 - Offline
            1 - In Lobby  (basically anywhere except god selection or in game)
            2 - god Selection (player has accepted match and is selecting god before start of game)
      	    3 - In Game (match has started)
      	    4 - Online (player is logged in, but may be blocking broadcast of player state)
	        5 - Unknown (player not found)
        */
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getplayerstatusJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getplayerstatus") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $player)->getBody();
    }
    public function GetTopMatches() {
        $client = new GuzzleHttp\Client();
        return $client->request("GET", $this->platform . "/gettopmatchesJson/" . $this->devkey . "/" . $this->GenerateSignature("gettopmatches") . "/" . $this->CreateSession() . "/" . $this->TimeStamp())->getBody();
    }
    public function GetPlayerAchievements ($player) {
        $ids = json_decode($this->GetPlayer($player), true);
        foreach( $ids as $id ){
            $playerId = $id['Id'];
        }
        $client = new GuzzleHttp\Client(); 
        return $client->request("GET",$this->platform . "/getplayerachievementsJson/" .  $this->devkey . "/" . $this->GenerateSignature ("getplayerachievements") . "/" . $this->CreateSession() . "/" . $this->TimeStamp () . "/" . $playerId)->getBody();
    }
    public function GetPatchInfo() {
        $client = new GuzzleHttp\Client();
        return $client->request("GET", $this->platform . "/getpatchinfoJson/" . $this->devkey . "/" . $this->GenerateSignature("getpatchinfo") . "/" . $this->CreateSession() . "/" . $this->TimeStamp())->getBody();
    }
}
