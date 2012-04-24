<?php
include("dBug.php");

/*

	REST/API for Lyrics
	Player: requires first_name, last_name

*/

$dbase = mysql_pconnect("localhost", "lyrics", "eltonjohn");
mysql_select_db("lyrics", $dbase);
	
$obj_method = '';
$player = new stdClass;
$game = new stdClass;

error_reporting(E_ALL);


if(isset($_REQUEST['obj_method'])){
	$obj_method = $_REQUEST['obj_method'];
	if(isset($_POST)){
		foreach($_POST as $key=>$value){
			$player->$key  = $value;
		}
	}
}

if($obj_method=='startGame'){

	/* testing */
		$player->first_name = "Anna";
		$player->last_name = "Billstrom";
	new dBug($player);
	$game_id = startGame($player);
	echo $game_id;
}


function startGame($player){
	
		// check if user exists
		$player->id = getPlayerId($player);
		if(!$player->id){
			// create player
			$player->id =createPlayer($player);
			echo "player created: ".$player->id;
		}

		mysql_query("insert into games (status) values ('started')");
		$result = mysql_query("select max(game_id) game_id from games");
		$record = mysql_fetch_array($result);
		$game->id = $record["game_id"];
		
		$sql = sprintf("insert into teams (game_id, player_id, status) values (%s,%s,'started')",$game->id, $player->id);
		mysql_query($sql) or die ("insert into teams fail: ".mysql_error());
		
		return $record['game_id'];
}

 function getPlayerId($player){
		$sql = sprintf("select player_id from players where first_name = '%s'",$player->first_name);
		$result = mysql_query($sql) or die ("query player fail ".mysql_error());
		$players = mysql_fetch_array($result);
		if(count($players)>0){
			$player->id = $players[0]["player_id"];
		} 
		return $player->id;

}

 function createPlayer($player){
			$sql = sprintf("insert into players (first_name, last_name) values ('%s','%s')", $player->first_name, $player->last_name);
			mysql_query($sql) or die ("inserting player failed".mysql_error());
			$player_id = $this->getPlayerId($player);
			return $player_id;
}

?>