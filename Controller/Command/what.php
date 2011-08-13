<?php
namespace Controller\Command;
use Model\Instruction\instruction;
use Model\Utility\registry;

class what extends abCommand{
    public function processCommand(instruction $instruction) {
	$instruction->getPlayer()->sendData("What?");
    }
}