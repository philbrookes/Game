<?php
include "autoloader.php";

use Model\Utility\configuration;
use Controller\Core\engine;
use Model\Utility\registry;

configuration::loadFromIni(dirname(__FILE__)."/Config/config.ini");

$players = array();
registry::addObject("players", $players);

$engine = new engine();
$engine->setStatus(STATUS_OK);
$engine->loop();