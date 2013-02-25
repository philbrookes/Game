<?php
namespace Core\Controller\Connection;

use Core\Model\Network\socket;
use Core\Model\Utility\configuration;
use Core\Model\Utility\registry;
use Core\Model\Object\Actor\player;
use Core\Model\Instruction\instruction;
use Core\Model\Utility\MGCParser\Script;
use Core\Model\Utility\MGCParser\Parser;
use Core\Controller\Core\engine;

class receptionist{
    private $listenSocket;
    
    public function __construct(){
        $this->listenSocket = new socket(configuration::getSetting("host"), configuration::getSetting("port"));
    }
    
    public function initListener(){
        return $this->listenSocket->open();
    }
    
    
    public function checkDisconnects(){
        $players = registry::getObject("players");
        foreach($players as $index => $player){
            if(!$player->isConnected()){
            engine::outputToConsole("disconnected player found...");
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
                $tmp->assignId();
                engine::outputToConsole("New Player connected");
                $players[] = $tmp;
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
                $instruction = new instruction($res, $player);

                $file = configuration::getSetting("root_dir")
                    . configuration::getSetting("scripts_dir") 
                    . $instruction->getCommand()
                    . "." . configuration::getSetting("scripts_ext");
            engine::outputToConsole("Looking for {$file}");
                if(file_exists($file)){
                    $script = new Script($file);
                    Parser::execute($script, $player, $instruction);
                }else{
                    engine::outputToConsole($file . " does not exist!");
                    $player->sendData("what?");
                }
            }
        }
    }
}