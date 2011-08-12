<?php
namespace Model\Object\Actor;

use Model\Network\socket;

class player extends abLiving implements inLiving {
	/**
	 *
	 * @var \Model\Network\socket 
	 */
	private $socket;
	
	public function __construct(){
	    $this->socket = new socket();
	}
	
        public function getData()
        {
            return $this->socket->getData();
        }
        
	public function sendData($data){
            $this->socket->write($data);
	}
        
        public function accept($socket){
            $this->socket->accept($socket);
        }
	
	public function isConnected(){
	    return $this->socket->isAlive();
	}
	public function closeSocket(){
	    $this->socket->close();
	}
}