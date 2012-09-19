<?php
namespace Model\Utility\MGCParser;

use Model\Utility\configuration;
use \Model\Object\Actor\player;
use Model\Instruction\instruction;
use Controller\Core\engine;

class Parser{
    private static function findEndIf(script $script, $linenum){
        $counter = 0;
        $lines = $script->getLines();
        for($i = $linenum+1; $i<sizeof($lines)-1; $i++){
            $line = trim($lines[$i]);
            if(substr($line, 0, 2) == "if"){
                $counter++;
            }
            if(substr($line, 0, 6) == "endif"){
                if($counter > 0){
                    $counter--;
                }else{
                    return $i;
                }
            }
        }
    } 
    
    private static function handleConcatenation($equation, script $script){
        $inSpeechmarks=false;
        $chunks = array();
        $chunk = "";
        $value = "";
        
        for($i=0;$i<strlen($equation);$i++){
            $char = substr($equation, $i, 1);
            if($char == "'"){
                if($inSpeechmarks == true){
                    $inSpeechmarks = false;
                }
                else $inSpeechmarks = true;
            }elseif(!ctype_alnum($char) && ! $inSpeechmarks){
                if( $char != "&" && strlen( trim($char) ) && $char != "_" && $char != "$"){ //not a whitespace char or & or _
                    engine::outputToConsole("Syntax error in position: $i in $equation");
                }
            }
            if($char == "&" && ! $inSpeechmarks){
                $chunks[] = $chunk;
                $chunk = "";
            }else{
                $chunk .= $char;
            }
        }
        //pick up the last bit
        $chunks[] = $chunk;
        
        foreach($chunks as $chunk){
            $chunk = trim($chunk);
            $value .= self::getVarValue($chunk, $script);
        }
    
        return $value;
    }
    
    private static function handleVariables($line, $linenum, script $script){
        $protectedNames = explode(",", configuration::getSetting("protected_varnames"));

        preg_match('|\$([a-zA-Z][a-zA-Z0-9]+)|', $line, $matches);
        $varname = $matches[1];
        //check for protected variable names
        if(!in_array($varname, $protectedNames)){
            //get assignment
            $value = trim(substr($line, strpos($line, "=")+1));
            //delete ;
            $value = substr($value,0,strlen($value)-1);
            
            $value = self::handleConcatenation($value, $script);
            
            //new value
            $script->setVarValue($varname, $value);
        }else{
            engine::outputToConsole("Attempt to overwrite a protected variable in ".$script->getFile()." on line: ".$linenum);
        }
    }
    
    private static function getVarValue($var, script $script){
        $var = trim($var);
        if(substr($var,0,1) == "$"){
            $var = substr($var, 1);
            $var = $script->getVarValue($var);
        }elseif(substr($var, 0, 1) == "'"){
            $var = substr($var, 1, strlen($var)-2);
        }
        return $var;
    }
    
    private static function handleIfStatement(script $script, $line, $lineon){
        $operations = explode(",", configuration::getSetting("operations"));
        //get equation from if statement
        preg_match("|\((.*)\)|", $line, $matches);
        $equation = $matches[1];
        //get varnames used
        list($var1, $var2) = explode("|", str_replace($operations, "|", $equation));
        $var1 = self::handleConcatenation($var1, $script);
        $var2 = self::handleConcatenation($var2, $script);
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
    private static function populateSpecialVars(script $script, player $player, instruction $instruction){
        $script->setVarValue("player_name", $player->getName());
        $script->setVarValue("player_id", $player->getId());
        $script->setVarValue("argument", implode(" ", $instruction->getArguments()));
    }
    
    private static function processFunctionCall($script, $line){
        $line = trim($line);
        $namespace = "\\Controller\\Exposed\\";
        $classname = substr($line, 0, strpos($line, "("));
        $wholeclass = $namespace.$classname;
        
        if(!class_exists($wholeclass, true)){
            return false;
        }
        
        $class = new $wholeclass;
        
        if( ! ($class instanceof \Controller\Exposed\exposedFunction) ){
            engine::outputToConsole("$wholeclass does not implement \\Controller\\Exposed\\exposedFunction");
            return false;
        }
        
        //get args
        preg_match("|\((.*)\)|", $line, $matches);
        $args = $matches[1];
        $args = explode(",", $args);
        
        foreach($args as $index => $arg){
            $args[$index] = self::handleConcatenation($arg, $script);
        }
        
        $class->process($args);
        return true;
    }
    
    public static function execute(Script $script, player $player, instruction $instruction){
        self::populateSpecialVars($script, $player, $instruction);
        
        if($script->syntaxOK()){
            $lineon = 0;
            $lines = $script->getLines();
            while($lineon < sizeof($lines)-1){
                $line = $lines[$lineon];
                //assigning a variable
                if(strpos(trim($line), '$') === 0){
                    self::handleVariables($line, $lineon, $script);
                }else if(strpos(trim($line), "if")=== 0){
                //if statement
                    $lineon = self::handleIfStatement($script, $line, $lineon);
                }else if(trim($line) !== "endif"){
                    //function found
                    $res = self::processFunctionCall($script, $line);
                    if($res !== true){
                        engine::outputToConsole("Badly formatted code on line: $lineon in {$script->getFile()}");
                    }
                }
                $lineon++;
            }
        }
    }
    
}
