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

configuration::addSetting("root_dir", dirname(__FILE__));
configuration::loadFromIni(configuration::getSetting("root_dir") . DIRECTORY_SEPARATOR . "Config" . DIRECTORY_SEPARATOR . "config.ini");
ini_set("default_socket_timeout", configuration::getSetting("socket_timeout"));

$players = array();
registry::addObject("players", $players);

$engine = new engine();
$engine->setStatus(STATUS_STARTUP);
$engine->loop();