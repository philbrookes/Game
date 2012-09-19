<?php
namespace Controller\Core;

use Controller\Connection\receptionist;

define("STATUS_RUNNING", 1);
define("STATUS_EXIT", 0);
define("STATUS_STARTUP", 2);
class engine {
	private $status = STATUS_STARTUP;
	/**
	 *
	 * @var \receptionist 
	 */
	private $Kate;
	
	public function __construct() {
	    $this->Kate = new receptionist();
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setStatus($status){
		$this->status = $status;
	}
	
	public function loop(){
        while($this->status == STATUS_STARTUP){
            if($this->Kate->initListener())
            {
                $this->setStatus(STATUS_RUNNING);
                self::outputToConsole("Socket bound, engine is running!");
            }
        }
		while($this->status == STATUS_RUNNING){
		    $this->Kate->checkNewConnections();		
		    $this->Kate->mapCommands();
		}
	}
    
    public static function outputToConsole($msg)
    {
        echo $msg. "\n";
    }
}