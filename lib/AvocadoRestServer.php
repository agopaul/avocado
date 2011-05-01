<?php

/**
* Class JSON API
*/
class AvocadoRestServer{
	
	function __construct(PDO $Db){
		$this->Avo = AvocadoSchema::getInstanceFromDb($Db);
	}
	
	public static function getInstance(PDO $Db){
		return new self($Db);
	}
	
	public function setHeader(){
		header("Content-type: application/json");
	}
	
	public function getAll(){
		return $this->Avo->toArray();
	}

	public function processRequest($Method, $Params=null){
		$this->setHeader();
		switch($Params['act']){
			case 'get_all':
					echo $this->getAll();
				break;
			default:
					echo "unknow method";
				break;
		}
	}
	
}


?>