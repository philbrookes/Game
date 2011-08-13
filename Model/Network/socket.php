<?php
namespace Model\Network;
/**
* Description of socket
*
* @author Philip Brookes
*/
class socket {
	private $ip;
	private $port;
	private $sockHandle;

	public function getSock(){
		return $this->sockHandle;
	}
	public function setIP($newVal){
		$this->ip = $newVal;
	}
	public function getIP(){
		return $this->ip;
	}

	public function setPort($newVal){
		$this->port = $newVal;
	}
	public function getPort(){return $this->port;}

	public function __construct($ip=null, $port=null){
		$this->sockHandle = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_set_nonblock($this->sockHandle);
		$this->setPort($port);
		$this->setIP($ip);
	}                                                                                            

	public function open($ip=null, $port=null){                                                                                                                                                  
		if($ip)         $this->setIP($ip);                                                                                                                                                   
		if($port)       $this->setPort($port);                                                                                                                                               
		if(socket_bind($this->sockHandle, $this->getIP(), $this->getPort())){                                                                                                                
			socket_listen($this->sockHandle);
			return true;
		}
		return false;
	}

	public function accept($socket=null){
		if(is_null($socket)) return false;
		$newconn = @socket_accept($socket);
		if($newconn == false) return false;
		else $this->sockHandle = $newconn;
		socket_set_nonblock($this->sockHandle);
		return true;
	}

	public function getData(){
		$data = @socket_read($this->sockHandle, 1024);
		return $data;
	}
	
	public function isAlive(){
	    $res = @socket_recv($this->sockHandle, $ress, 1, MSG_PEEK);
	    $result = socket_last_error($this->sockHandle);
	    echo "got $result...\n";
	    if($result == 32 || $result == 104){
		echo "client is not alive\n";
		return false;
	    }else{
		echo "client is alive\n";
		return true;
	    }
	}

	public function write($data){
		socket_write($this->sockHandle, $data);
	}

	public function close(){
		socket_close($this->sockHandle);
	}

}