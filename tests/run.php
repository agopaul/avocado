<?php

require_once dirname(__FILE__) . "/../lib/Avocado.php";
require_once dirname(__FILE__) . "/simpletest/autorun.php";

spl_autoload_register(function($Classname){
	
	if(preg_match("/^TestAvocado/", $Classname)){
		require dirname(__FILE__)."/$Classname.php";
		return true;
	}
	return false;

});

class AllFileTests extends TestSuite {
	function __construct() {
		parent::__construct();

		foreach(new DirectoryIterator(dirname(__FILE__)."/") as $File){
			if(preg_match("/^TestAvocado(.*)\.php$/", $File)){
				$this->addFile(dirname(__FILE__).'/'.$File);
			}
		}
	}
}

?>