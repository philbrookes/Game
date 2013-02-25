<?php
/**
 * @author: Phil Brookes
 * Twitter: @philthi
 * email: phil@locals.ie
 */
include "autoloader.php";

use Core\Model\Utility\configuration;
use Core\Controller\Core\engine;
use Core\Model\Utility\registry;

configuration::addSetting("root_dir", dirname(__FILE__));
configuration::addSetting("config_dir", configuration::getSetting("root_dir") . DIRECTORY_SEPARATOR . "Core" . DIRECTORY_SEPARATOR . "Config");
configuration::loadFromIni(configuration::getSetting("config_dir") . DIRECTORY_SEPARATOR . "config.ini");

ini_set("default_socket_timeout", configuration::getSetting("socket_timeout"));

$players = array();
registry::addObject("players", $players);

$engine = new engine();
$engine->setStatus(STATUS_STARTUP);
$engine->loop();