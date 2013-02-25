<?php
namespace Core\Model\Utility;

class configuration{
	private static $settings;
	
	public static function updateSetting($name, $value){
		if(isset(self::$settings[$name])){
			self::$settings[$name] = $value;
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public static function addSetting($name, $value){
		if(!isset(self::$settings[$name])){
			self::$settings[$name] = $value;
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public static function getSetting($name){
		if(isset(self::$settings[$name])){
			return self::$settings[$name];
		}
		else
		{
			return null;
		}
	}
        
        public static function loadFromIni($fileName){
            if(is_readable($fileName)){
                self::$settings = parse_ini_file($fileName, true);
                return 1;
            }
            else
            {
                return 0;
            }
        }
}