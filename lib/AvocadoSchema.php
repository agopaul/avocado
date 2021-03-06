<?php

/*
**	TODO :: Implement a factory object, AvocadoSchema should not handle the hydration logic
*/


/**
 * WriteMePlease
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoSchema implements ArrayAccess, Iterator{
	
	protected $Db;
	protected $Tables;
	protected $CurrentKey;
	
	private function __construct(PDO $Db=null, array $Arr=null){
		$this->Tables = array();

		if($Db && !$Arr)
			$this->fromDb($Db);
		elseif(!$Db && $Arr)
			$this->fromArray($Arr);
		else throw new AvocadoException("You must provide a PDO instance or an array");

		// Reset Iterator cursor
		$this->rewind();
	}

	/** ArrayAccess iterator methos **/
	public function offsetExists($Key){
		if($Key)
			return array_key_exists($Key, $this->toArray());
	}

	public function offsetGet($Key){
		if($this->offsetExists($Key)){
			foreach($this->Tables as $Table){
				if($Table->getName()==$Key)
					return $Table;
			}
		}
	}

	public function offsetSet($Key, $Table){
		if(!$Table instanceof AvocadoTable)
			throw new AvocadoException("You must provide a valid AvocadoTable instance");

		// Delete the table if it already exists
		if($this->offsetExists($Key) || 
				$this->offsetExists($Table->getName())){
			$this->offsetUnSet($Table->getName());
		}

		$this->Tables[] = $Table;
	}

	public function offsetUnSet($Name){
		if($this->offsetExists($Name)){
			foreach($this->Tables as $Key=>$Table){
				if($Table->getName()==$Name){
					// Unset the whole fucking object, please
					$this->Tables[$Key] = null;
					unset($this->Tables[$Key]);
				}
			}
		}
	}

	/** Iterator methos **/
	public function rewind(){
		$this->CurrentKey = 0;
	}

	public function current(){
		return $this->Tables[$this->CurrentKey];
	}

	public function key(){
		return $this->CurrentKey;
	}

	public function next(){
		++$this->CurrentKey;
	}

	public function valid(){
		return isset($this->Tables[$this->CurrentKey]);
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
	
	public function getTables($Associative=false){
		if($Associative){
			$App = array();
			foreach($this->Tables as $Table)
				$App[$Table->getName()] = $Table;
			
			return $App;
		}
		return $this->Tables;
	}
	
	protected function fromDb($Db){
		foreach($Db->query("SHOW TABLES") as $Table){
			$Tablename = $Table[0];
			$Fields = array();
			foreach($Db->query("SHOW COLUMNS FROM $Tablename") as $Field){
				preg_match("/(\w+)(\(([\d]+)\))?/", $Field['Type'], $Matches);
				$Fields[] = new AvocadoField($Field['Field'],
											$Matches[1],
											$Field['Null']=='YES'?true:false,
											isset($Matches[3])?(int)$Matches[3]:null);
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
											$Field['length']);
			}
			
			$this->Tables[] = new AvocadoTable($TableName, $Fields);
		}
		return true;
	}
	
	public function toArray(){
		$Final = array();
		foreach($this->getTables() as $Table){
			$Final[$Table->getName()] = $Table->toArray();
		}
		return $Final;
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