<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if(defined("AUTOLOADED")){ return; }
define("AUTOLOADED", 1);
/*
 * This function is registered as an autoloader to intelligently include files
 * in the background.
 */
function autoloader($className){
    $classFile = dirname(__FILE__)."/".str_replace("\\", "/", $className).".php";

    if(!file_exists($classFile)) return false;

    include($classFile);
    return true;
}

spl_autoload_register("autoloader");
