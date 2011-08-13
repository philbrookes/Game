<?php
namespace Controller\Command;
use Model\Instruction\instruction;
use Model\Utility\registry;

class setName extends abCommand{
    public function processCommand(instruction $instruction) {
	$instruction->getPlayer()->setName($instruction->getArguments(0));
        $instruction->getPlayer()->sendData("Set name to: " . $instruction->getArguments(0));
    }
}