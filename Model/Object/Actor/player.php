<?php
namespace Model\Object\Actor;

use Model\Network\socket;

class player extends abLiving implements inLiving {
	/**
	 *
	 * @var \Model\Network\socket 
	 */
	private $socket;
	private $id;
	
	private static $staticId;
	
	public function __construct(){
            parent::__construct();
	    $this->socket = new socket();
	}
        
	public function assignId(){
	    $this->id = ++self::$staticId;
	}
        
	public function getId(){
	    return $this->id;
	}
        
        public function getData()
        {
            return $this->socket->getData();
        }
        
	public function sendData($data){
            $this->socket->write($data);
	}
        
        public function accept($socket){
            return $this->socket->accept($socket);
        }
	
	public function isConnected(){
	    return $this->socket->isAlive();
	}
       
	public function closeSocket(){
	    $this->socket->close();
	}
}