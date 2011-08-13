<?php
namespace Controller\Connection;

use Model\Network\socket;
use Model\Utility\configuration;
use Model\Utility\registry;
use Model\Object\Actor\player;
use Model\Instruction\instruction;

class receptionist{
    
    private $listenSocket;
    
    public function __construct(){
        $this->listenSocket = new socket(configuration::getSetting("host"), configuration::getSetting("port"));
        $this->listenSocket->open();
    }
    
    public function checkDisconnects(){
	$players = registry::getObject("players");
	foreach($players as $index => $player){
	    if(!$player->isConnected()){
		echo "disconnected player found...\n";
		$player->closeSocket();
		unset($players[$index]);
	    }
	}
	rsort($players);
	registry::updateObject("players", $players);
    }
    
    public function checkNewConnections(){
	$this->checkDisconnects();
        $players = registry::getObject("players");
	$tmp = new player();
	if($tmp->accept($this->listenSocket->getSock())){
	    if(sizeof($players) < configuration::getSetting("max_players")){
		$players[] = $tmp;
		$tmp->sendData(configuration::getSetting("welcome_message"));
		registry::updateObject("players", $players);
	    }else{
		$this->sendSystemFullMessage($tmp);
	    }
        }
    }
    public function sendSystemFullMessage(player $player){
	$player->sendData(configuration::getSetting("system_full_message"));
	$player->closeSocket();
    }
    
    public function mapCommands(){
	$players = registry::getObject("players");
	foreach($players as $player){
	    $res = $player->getData();
	    if($res != ""){
		$instruction = new instruction($res);
		switch(strtolower($instruction->getCommand())){
		    case "blurt":
			$comm = new \Controller\Command\sendToAll();
			$comm->processCommand($instruction);
			break;
		    default :
			$comm = new \Controller\Command\what();
			$comm->processCommand($instruction);
		}
	    }
	}
    }
}