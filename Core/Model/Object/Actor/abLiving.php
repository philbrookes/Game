<?php
namespace Core\Model\Object\Actor;
abstract class abLiving {
	protected   $health,
		    $name,
		    $sex,
		    $alive,
		    $dexterity,
		    $strength,
		    $intelligence,
		    $wisdom,
		    $constitution;
        
        public function __construct(){
            $this->name = "Phil";
        }
        
        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }
}
