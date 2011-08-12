<?php
namespace Model\Utility;

class registry{
	private static $settings;
	
	public static function updateObject($name, $value){
		if(isset(self::$settings[$name])){
			self::$settings[$name] = $value;
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public static function addObject($name, $value){
		if(!isset(self::$settings[$name])){
			self::$settings[$name] = $value;
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public static function getObject($name){
		if(isset(self::$settings[$name])){
			return self::$settings[$name];
		}
		else
		{
			return null;
		}
	}
}