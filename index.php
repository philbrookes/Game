<?php
/**
 * @author: Phil Brookes
 * Twitter: @philthi
 * email: phil@locals.ie
 */
include "autoloader.php";

use Model\Utility\configuration;
use Controller\Core\engine;
use Model\Utility\registry;

configuration::loadFromIni(dirname(__FILE__)."/Config/config.ini");

ini_set("default_socket_timeout", configuration::getSetting("socket_timeout"));

$players = array();
registry::addObject("players", $players);

$engine = new engine();
$engine->setStatus(STATUS_OK);
$engine->loop();