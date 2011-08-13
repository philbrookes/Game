<?php
namespace Controller\Command;
use Model\Instruction\instruction;

abstract class abCommand{
    abstract public function processCommand(instruction $instruction);
}