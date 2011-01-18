<?php

/**
 * WriteMePlease
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoSchema{ // aka AvocadoTables
	
	protected $Db;
	protected $Tables;
	
	function __construct(PDO $Db=null, array $Arr=null){
		if($Db && !$Arr)
			$this->fromDb($Db);
		elseif(!$Db && $Arr)
			$this->fromArray($Arr);
		else throw new AvocadoException("You must provide a PDO instance or an array");
	}
	
	public static function getInstance(PDO $Db=null, array $Arr=null){
		return new self($Db, $Arr);
	}

	public static function getInstanceFromDb(PDO $Db){
		return new self($Db);
	}

	public static function getInstanceFromArray(array $Arr){
		return new self(null, $Arr);
	}
	
	public function getTables(){
		return $this->Tables;
	}
	
	protected function fromDb($Db){
		foreach($Db->query("SHOW TABLES") as $Table){
			$Tablename = $Table[0];
			$Fields = array();
			foreach($Db->query("SHOW COLUMNS FROM $Tablename") as $Field){
				preg_match("/(\w+)((\([\d]+)\))?/", $Field['Type'], $Matches);
				$Fields[] = new AvocadoField($Field['Field'],
											$Matches[1],
											$Field['Null']=='YES'?true:false,
											isset($Matches[2])?(int)$Matches[2]:null);
			}		
			
			$this->Tables[] = new AvocadoTable($Tablename, $Fields);
		}
		return true;
	}

	protected function fromArray($Input){
		foreach($Input as $TableName=>$Table){
			$Fields = array();
			foreach($Table as $Field){
				$Fields[] = new AvocadoField($Field['name'],
											$Field['type'],
											(bool)$Field['nullable'],
											(int)$Field['length']);
			}		
			
			$this->Tables[] = new AvocadoTable($TableName, $Fields);
		}
		return true;
	}
	
	public function toArray(){
		return $this->getTables();
	}
	
	public function toJson(){
		return json_encode($this->toArray());
	}

	/**
	 * Return the whole schema sql
	 *
	 * @return string
	 * @author paul
	 **/
	public function toSql(){
		$Sql = "";
		foreach($this->getTables() as $Table){
			$Sql .= $Table->toSql() . ";\n";
		}
		return $Sql;
	}

}

?>