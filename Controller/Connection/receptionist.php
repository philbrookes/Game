<?php
namespace Controller\Connection;

use Model\Network\socket;
use Model\Utility\configuration;
use Model\Object\Actor\player;

class receptionist{
    
    private $listenSocket, $players;
    
    public function __construct(){
        $this->listenSocket = new socket(configuration::getSetting("host"), configuration::getSetting("port"));
        $this->listenSocket->open();
	echo "Kate listening on: ".configuration::getSetting("host").":".configuration::getSetting("port")."\n";
	$this->players = array();
    }
    
    public function getPlayers(){
	return $this->players;
    }
    
    public function checkDisconnects(){
	$playerKicked=0;
	foreach($this->players as $player){
	    if(!$player->isConnected()){
		echo "player disconnected, closing connection\n";
		$player->closeConnection();
		unset($player);
		$playerKicked=1;
	    }
	}
	if($playerKicked) rsort($this->players);
    }
    
    public function checkNewConnections(){
	$this->checkDisconnects();
	$tmp = new player();
	if($tmp->accept($this->listenSocket->getSock())){
	    echo "Got a new connection...\n";
	    if(sizeof($this->players) < configuration::getSetting("max_players")){
		echo "We have space for this connection\n";
		$this->players[] = $tmp;
		echo "sending welcome message\n";
		$tmp->sendData(configuration::getSetting("welcome_message"));
	    }else{
		echo "sending system too full message\n";
		$this->sendSystemFullMessage($tmp);
	    }
        }
    }
    public function sendSystemFullMessage(player $player){
	$player->sendData(configuration::getSetting("system_full_message"));
	$player->closeSocket();
    }
}