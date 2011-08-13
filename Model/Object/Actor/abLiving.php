<?php
namespace Model\Object\Actor;
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
        protected $name;
        
        public function __construct(){
            $this->name = "UNNAMED";
        }
        
        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }
}
