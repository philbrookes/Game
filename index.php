<?php
include "autoloader.php";

use Model\Utility\configuration;
use Controller\Core\engine;

configuration::loadFromIni(dirname(__FILE__)."/Config/config.ini");

$engine = new engine();
$engine->setStatus(STATUS_OK);
$engine->loop();