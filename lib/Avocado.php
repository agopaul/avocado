<?php

/**
 * Main exception
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoException extends Exception{}

/**
 * undocumented class
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class Avocado{
	
	/**
	 * Kickstart library autoload
	 *
	 * @return void
	 * @author Paolo Agostinetto <paul.ago@gmail.com>
	 **/
	public static function autoload(){
		spl_autoload_register(array(__CLASS__, "register")); // Can't use self on PHP classbacks
	}
	
	/**
	 * Registers library classes
	 *
	 * @return bool
	 * @author Paolo Agostinetto <paul.ago@gmail.com>
	 **/
	public static function register($Classname){
		if(preg_match("/^Avocado/", $Classname)){
			require dirname(__FILE__)."/$Classname.php";
			return true;
		}
		return false;
	}
	
}

Avocado::autoload();

?>