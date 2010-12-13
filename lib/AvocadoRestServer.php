<?php

/**
* Class JSON API
*/
class AvocadoRestServer{
	
	function __construct(PDo $Db, $Method, $Params=null){
		$this->Avo = AvocadoSchema::getInstance($Db);
		switch($Params['act']){
			case 'get_all':
					$this->process($this->getAll());
				break;
			default:
					echo "unknow method";
				break;
		}
	}
	
	public static function getInstance(PDO $Db, $Method, $Params=null){
		return new self($Db, $Method, $Params);
	}
	
	public function setHeader(){
		header("Content-type: application/json");
	}
	
	public function getAll(){
		return $this->Avo->toJson();
	}
	
	public function process($Output){
		$this->setHeader();
		echo $Output;
	}
	
}

?>