<?php
namespace Controller\Connection;

use Model\Network\socket;
use Model\Utility\configuration;
use Model\Utility\registry;
use Model\Object\Actor\player;

class receptionist{
    
    private $listenSocket, $players;
    
    public function __construct(){
        $this->listenSocket = new socket(configuration::getSetting("host"), configuration::getSetting("port"));
        $this->listenSocket->open();
    }
    
    public function checkDisconnects(){
	$this->players = registry::getObject("players");
	foreach($this->players as $player){
	    if(!$player->isConnected()){
		$player->closeConnection();
		unset($player);
	    }
	}
	rsort($this->players);
	registry::updateObject("players", $this->players);
    }
    
    public function checkNewConnections(){
	$this->checkDisconnects();
        $this->players = registry::getObject("players");
	$tmp = new player();
	if($tmp->accept($this->listenSocket->getSock())){
	    if(sizeof($players) < configuration::getSetting("max_players")){
		$this->players[] = $tmp;
		$tmp->sendData(configuration::getSetting("welcome_message"));
		registry::updateObject("players", $this->players);
	    }else{
		$this->sendSystemFullMessage($tmp);
	    }
        }
    }
    public function sendSystemFullMessage(player $player){
	$player->sendData(configuration::getSetting("system_full_message"));
	$player->closeSocket();
    }
}