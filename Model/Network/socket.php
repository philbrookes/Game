<?php
namespace Model\Network;
/**
* This class will act as either a listener socket, a client socket or a server socket.
* EXAMPLE 1: Make a listener:
*   $listenSocket = new socket($MyExternalIP, $MyOpenPort);
*   $listenSocket->open();
* 
* EXAMPLE 2: Client / server Socket:
*   $serverSock = new Socket();
*   $serverSock->Accept($listenSock->getSock());
* 
* @author Philip Brookes 
* email: phil@locals.ie 
* twitter: @philthi
* 
* I love talking to people
* if you wish to comment on any of the code below
* do not hesitate to contact me.
* 
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

	/**
	* IP and Port are required if this socket will be a listener socket
	*/
	public function __construct($ip=null, $port=null){
		$this->sockHandle = socket_create(AF_INET, SOCK_STREAM, 0);
		//Make it a non-blocking socket
		socket_set_nonblock($this->sockHandle);
		$this->setPort($port);
		$this->setIP($ip);
	}                                                                                            

	public function open($ip=null, $port=null){                                                                                                                                                  
		if($ip)         $this->setIP($ip);                                                                                                                                                   
		if($port)       $this->setPort($port);                                                                                                                                               
		//Binds to specified socket and port and listens for connection requests
		if(socket_bind($this->sockHandle, $this->getIP(), $this->getPort())){                                                                                                                
			socket_listen($this->sockHandle);
			return true;
		}
		return false;
	}
	//accepts the pending connection request from a socketHandle (Note that this is not an instance of this class, but the return of getSock())
	public function accept($socket=null){
		if(is_null($socket)) return false;
		$newconn = @socket_accept($socket);
		if($newconn == false) return false;
		else $this->sockHandle = $newconn;
		//Make it a non-blocking connection
		socket_set_nonblock($this->sockHandle);
		return true;
	}

	//reads data from the socket, will return blank if nothing is there to read
	public function getData(){
		$data = @socket_read($this->sockHandle, 1024);
		return $data;
	}
	
	//Returns true if the remote end is still connected, false if the remote end has hung up.
	public function isAlive(){
	    $res = @socket_recv($this->sockHandle, $data, 1024, MSG_PEEK);
	    $result = socket_last_error($this->sockHandle);
	    //if bytes received is zero rather than blank, client has hung up
	    if($result == 32 || $result == 104 || $res === 0){
			return false;
	    }else{
			return true;
	    }
	}
	//send data to the remote socket
	public function write($data){
		socket_write($this->sockHandle, $data);
	}
	//disconnect the socket
	public function close(){
		socket_close($this->sockHandle);
	}
}