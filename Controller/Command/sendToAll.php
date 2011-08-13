<?php
namespace Controller\Command;
use Model\Instruction\instruction;
use Model\Utility\registry;

class sendToAll extends abCommand{
    public function processCommand(instruction $instruction) {
	$msg = implode(" ", $instruction->getArguments());
	$players = registry::getObject("players");
	foreach($players as $player){
	    if($player->getId() != $instruction->getPlayer()->getId()){
		$player->sendData($instruction->getPlayer()->getName()." said: ".$msg);
	    }else{
		$player->sendData("You said: ".$msg);
	    }
	}
    }
}
?>
