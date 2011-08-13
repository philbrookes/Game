<?php
namespace Model\Utility\MGCParser;

use Model\Utility\configuration;
use \Model\Object\Actor\player;

class Parser{
    
    private static function findEndIf(script $script, $linenum){
        $counter = 0;
        for($i = $linenum+1; $i<=sizeof($script->getLines())-1; $i++){
            if(substr($line, 0, 2) == "if") $counter++;
            if(substr($line, 0, 6) == "endif"){
                $counter--;
                if($counter == 0){
                    return $i;
                }
            }
        }
    } 
    
    private static function handleVariables($line){
        ereg('|\$([a-zA-Z0-9])|', $line, $matches);
        $varname = $matches[0];
        //check for protected variable names
        if(!in_array($varname, $protectedNames)){
            //get assignment
            $value = trim(substr($line, strpos($line, "=")));
            //delete ;
            $value = substr($value,0,strlen($value)-1);
            if(substr($value, 0, 1) == "'"){
                $value = substr($value, 1, strlen($value)-1);
            }
            //new value
            $script->setVarValue($varname, $value);
            
            
            if(strpos($value, "$") !== false){
            //evaluation of other variables
                //@TODO: not yet implemented
            }
        }
    }
    
    private static function getVarValue($var, script $script){
        if(substr($var,0,1) == "$"){
            $var = $script->getVarValue($var);
        }elseif(substr($var, 0, 1) == "'"){
            $var = substr($var, 1, strlen($var)-1);
        }
        return $var;
    }
    
    private static function handleIfStatement(script $script, $line, $lineon){
        //get equation from if statement
        ereg("|\((.*)\)|", $line, $matches);
        $equation = $matches[0];
        //get varnames used
        list($var1, $var2) = explode("|", str_replace($operations, "|", $equation));
        $var1 = self::getVarValue($var1, $script);
        $var2 = self::getVarValue($var2, $script);
        
        foreach($operations as $operation){
            if(strpos($equation, $operation)){
                switch($operation){
                    case "==":
                        if($var1 != $var2){
                            //false jump to endif
                            $lineon = self::findEndIf($script, $lineon);
                        }
                        break;
                    case "!=":
                        if($var1 == $var2){
                            //false jump to endif
                            $lineon = self::findEndIf($script, $lineon);
                        }
                        break;
                    case ">":
                        if($var1 <= $var2){
                            //false jump to endif
                            $lineon = self::findEndIf($script, $lineon);
                        }
                        break;
                    case "<":
                        if($var1 >= $var2){
                            //false jump to endif
                            $lineon = self::findEndIf($script, $lineon);
                        }
                        break;
                    case ">=":
                        if($var1 < $var2){
                            //false jump to endif
                            $lineon = self::findEndIf($script, $lineon);
                        }
                        break;
                    case "<=":
                        if($var1 > $var2){
                            //false jump to endif
                            $lineon = self::findEndIf($script, $lineon);
                        }
                        break;
                }
                break;
            }
        }
        return $lineon;
    }
    private static function populateSpecialVars(script $script, player $player){
        $script->setVarValue("player_name", $player->getName());
        $script->setVarValue("player_id", $player->getId());
    }
    
    private static function processFunctionCall($script, $line){
        $namespace = "\\Controller\\Exposed\\";
        $classname = substr($line, 0, strpos("(", $line));
        $wholeclass = $namespace.$classname;
        //get args
        ereg("|\((.*)\)|", $line, $matches);
        $args = $matches[0];
        $args = explode(",", $args);
        
        foreach($args as $arg){
            $arg = self::getVarValue($arg, $script);
        }
        
        if(!class_exists($wholeclass)){
            return $classname;
        }
        $class = new $wholeclass;
        
        $class->process($args);
        return true;
    }
    
    public static function execute(Script $script, player $player){
        $protectedNames = explode(",", configuration::getSetting("protected_varnames"));
        $operations = explode(",", configuration::getSetting("operations"));
        self::populateSpecialVars($script, $player);
        
        if($script->syntaxOK()){
            $lineon = 0;
            $lines = $script->getLines();
            print_r($lines);
            while($lineon <= sizeof($script->getLines())-1){
                $line = $lines[$lineon];
                //assigning a variable
                if(strpos(trim($line), '$') === 0){
                    self::handleVariables($line);
                }else if(strpos(trim($line), "if")=== 0){
                //if statement
                    $lineon = self::handleIfStatement($script, $line, $lineon);
                }else{
                //function call
                    //function found
                    $res = self::processFunctionCall($script, $line);
                    if($res !== true){
                        echo "Badly formatted code on line: $linenum in {$script->getFile()}\n";
                    }
                }
                $lineon++;
            }
        }
    }
    
}
