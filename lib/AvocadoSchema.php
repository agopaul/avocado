<?php

/**
 * WriteMePlease
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoSchema implements SeekableIterator{
	
	protected $Db;
	protected $Tables;
	protected $TablesName, $Key;
	
	function __construct(PDO $Db=null, array $Arr=null){
		$this->Tables = array();

		if($Db && !$Arr)
			$this->fromDb($Db);
		elseif(!$Db && $Arr)
			$this->fromArray($Arr);
		else throw new AvocadoException("You must provide a PDO instance or an array");

		$this->TablesName = array_keys($this->Tables);
		$this->rewind();
	}

	/** Iterator methos **/
	public function seek($Key){
		$this->Key = $Key;
		if(!$this->valid())
			throw new OutOfBoundsException("Invalid seek position ({$this->Key})");
		
		$this->TablesName[$Key];
	}

	public function rewind(){
		reset($this->TablesName);
	}

	public function next(){
		next($this->TablesName);
	}

	public function key(){
		return key($this->TablesName);
	}

	public function current(){
		return current($this->TablesName);
	}

	public function valid(){
		return isset($this->TablesName[$this->Key]);
	}

	/** Other methos **/
	
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
		$Final = array();
		foreach($this->getTables() as $TableName=>$Table){
			$Final[$TableName] = $Table->toArray();
		}
		return $Final;
	}
	
	public function toJson(){
		foreach($this->toArray() as $Table){
			$TableName = $Table->getName();
			$Tables[$TableName] = $Table->toArray();
		}
		return json_encode($Tables);
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