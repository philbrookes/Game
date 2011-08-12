<?php

namespace Controller\Core;
define("STATUS_OK", 1);
define("STATUS_EXIT", 0);

class engine {
	private $status;
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setStatus($status){
		$this->status = $status;
	}
	
	public function loop(){
		while($this->status != STATUS_EXIT){
			
		}
	}
}