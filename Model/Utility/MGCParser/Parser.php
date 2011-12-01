<?php
namespace Model\Utility\MGCParser;

use Model\Utility\configuration;
use \Model\Object\Actor\player;
use Model\Instruction\instruction;

class Parser{
    
    private static function findEndIf(script $script, $linenum){
        echo "looking for endif\n";
        $counter = 0;
        $lines = $script->getLines();
        for($i = $linenum+1; $i<sizeof($lines)-1; $i++){
            $line = trim($lines[$i]);
            echo "checking for endif in ($i) $line\n";
            if(substr($line, 0, 2) == "if"){
                echo "found if, counter: $counter\n";
                $counter++;
            }
            if(substr($line, 0, 6) == "endif"){
                echo "found endif, counter: $counter\n";
                if($counter > 0){
                    $counter--;
                }else{
                    return $i;
                }
            }
        }
    } 
    
    private static function handleConcatenation($equation, script $script){
        $inspeechmarks=false;
        $bits = array();
        $biton=0;
        
        for($i=0;$i<strlen($equation);$i++){
            $char = substr($equation, $i, 1);
            if($char == "'"){
                if($inspeechmarks == true){
                    $inspeechmarks = false;
                }
                else $inspeechmarks = true;
            }elseif(!ctype_alnum($char) && ! $inspeechmarks){
                if( $char != "&" && strlen( trim($char) ) && $char != "_" ){ //not a whitespace char or & or _
                    echo "Syntax error in position: $i in $equation\n";
                }
            }
            if($char == "&" && ! $inspeechmarks){
                    $biton++;
            }else{
                $bits[$biton] .= $char;
            }
        }
        foreach($bits as $bit){
            $bit = trim($bit);
            $value .= self::getVarValue($bit, $script);
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
            echo "Attempt to overwrite a protected variable in ".$script->getFile()." on line: ".$linenum."\n";
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
                        echo "got check equals for $var1 == $var2\n";
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
        echo $classname." / ".$wholeclass."\n";
        
        if(!class_exists($wholeclass, true)){
            echo "$wholeclass did not exist...\n";
            return false;
        }
        
        echo "$wholeclass exists... processing.\n";
        $class = new $wholeclass;
        
        if( ! ($class instanceof \Controller\Exposed\exposedFunction) ){
            echo "$wholeclass does not implement \\Controller\\Exposed\\exposedFunction\n";
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
                        echo "Badly formatted code on line: $lineon in {$script->getFile()}\n";
                    }
                }
                $lineon++;
            }
        }
    }
    
}
