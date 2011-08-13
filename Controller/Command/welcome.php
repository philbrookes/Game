<?php
namespace Controller\Command;
use Model\Instruction\instruction;
use Model\Utility\registry;

class welcome extends abCommand{
    public function processCommand(instruction $instruction) {
	$instruction->getPlayer()->sendData("Welcome to the Game...
To talk to other users use: blurt <message>
To set your own name use: name <name> (only the first word will be used!)
Have fun...");
    }
}