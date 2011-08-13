<?php
namespace Model\Utility\MGCParser;

class Script {
    private $vars, $lines, $contents;
    private $file;
    private $status=0;
    
    public function __construct($file){
        if(file_exists($file) && is_readable($file)){
            $this->file = $file;
            $this->tidy();
            $this->status=1;
        }else{
            $this->status=0;
        }
    }
    
    public function getFile(){
        return $this->file;
    }
    
    public function setVarValue($varname, $value){
        $this->vars[$varname] = $value;
    }
    
    public function getVarValue($varname){
        return $this->vars[$varname];
    }
    
    public function getLines(){
        return $this->lines;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function syntaxOK(){
        return $this->getStatus();
    }
    
    private function tidy(){
        //read file into array
        echo "reading script from: ".$this->file."\n";
        $this->lines =  file($this->file);
        print_r($this->lines);
        foreach($this->lines as $line){
            //remove all comments
            $pos = strpos($line, "//");
            if($pos !== false){
                $line = substr($line, 0, $pos);
            }
        }
        $this->contents = implode(" ", $this->lines);
        //concatenate file and split on line-ends: ;
        $this->lines = explode(";", $this->content);
    }
}

?>
