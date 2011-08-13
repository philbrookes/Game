<?php
namespace Controller\Command;
use Model\Instruction\instruction;
use Model\Utility\registry;

class welcome extends abCommand{
    public function processCommand(instruction $instruction) {
	$instruction->getPlayer()->sendData("Welcome to the Game...");
        $instruction->getPlayer()->sendData("To talk to other users use: blurt <message>");
        $instruction->getPlayer()->sendData("To set your own name use: name <name> (only the first word will be used!)");
        $instruction->getPlayer()->sendData("Have fun...");
    }
}