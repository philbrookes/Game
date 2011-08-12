<?php
use Model\Networking\socket;
use Model\Utility\configuration;
use Model\Utility\registry;

class receptionist{
    
    private $listenSocket;
    
    public function __construct(){
        $this->listenSocket = new socket(configuration::getSetting("host"), configuration::getSetting("port"));
        $this->listenSocket->open();
    }
    
    public function checkDisconnects(){
        
    }
    
    public function checkNewConnections(){
        $players = registry::getObject("players");
        if(sizeof($players) < configuration::getSetting("max_players")){
            
        }
    }
    
}