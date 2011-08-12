<?php

namespace Model\Object\Actor;
use Model\Network\socket;

class player implements inLiving {
	private $socket;
	
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
}