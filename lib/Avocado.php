<?php

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

/**
 * Main exception
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoException extends Exception{}

/**
 * WriteMePlease
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoSchema{ // aka AvocadoTables
	
	protected $Db;
	protected $Tables;
	
	function __construct(PDO $Db){
		$this->Db = $Db;
		$this->scanTables();
	}
	
	public static function getInstance(PDO $Db){
		return new self($Db);
	}
	
	public function getTables(){
		return $this->Tables;
	}
	
	protected function scanTables(){
		foreach($this->Db->query("SHOW TABLES") as $Table){
			$Tablename = $Table[0];
			$Fields = array();
			foreach($this->Db->query("SHOW COLUMNS FROM $Tablename") as $Field){
				preg_match("/(\w+)((\([\d]+)\))?/", $Field['Type'], $Matches);
				$Fields[] = new AvocadoField($Field['Field'], $Matches[1], $Field['Null']=='YES'?true:false, $Matches[2]?(int)$Matches[2]:null);
			}		
			
			$this->Tables[] = new AvocadoTable($Tablename, $Fields);
		}
		return true;
	}
	
	public function toArray(){
		$Tables = array();
		foreach($this->getTables() as $Table){
			$Fields = array();
			foreach($Table->getFields() as $Field){
				$Fields[$Field->getName()] = $Field->toArray();
			}
			$Tables[$Table->getName()] = $Fields;
		}
		return $Tables;
	}
	
	public function toJson(){
		return json_encode($this->toArray());
	}
}

/**
 * Table entity
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoTable{ // implements ArrayIterator
	
	/**
	 * Add field option
	 **/
	const ADD_FIELD = "ADD";
	
	/**
	 * Update field option
	 **/
	const UPDATE_FIELD = "UPD";
	
	protected $Tablename, $Fields;
	
	function __construct($Tablename, array $Fields){
		if(!$Tablename) throw new AvocadoException("Tablename can't be null");
		$this->Tablename = $Tablename;
		$this->Fields = $Fields;
	}
	
	public function getName(){
		return $this->Tablename;
	}
	
	public function getFields(){
		return $this->Fields;
	}
	
	/**
	 * Return a field by name
	 *
	 * @return mixed
	 * @author paul
	 **/
	public function getFieldByName($FieldName){
		foreach($this->Fields as $Field){
			if($Name==$Field->getName())
				return $Field;
		}
		return false;
	}
	
	/**
	 * Return fields in array form
	 *
	 * @return array
	 * @author paul
	 **/
	public function toArray(){
		$Fields = array();
		foreach($this->Fields as $FieldName=>$Field){
			$Fields[$FieldName] = $Field->toarray();
		}
		return $Fields;
	}
	
	/**
	 * Return sql to create/modify field
	 *
	 * @return string
	 * @author paul
	 **/
	function toSql($FieldName, $Action=self::ADD_FIELD){
		$Field = $this->getFieldByName($FieldName);
		$FieldArr = $Field->toArray();
		switch($Action){
			case self::ADD_FIELD:
					return sprintf("ALTER TABLE %s ADD %s %s(%s) %s;", 
										$this->getName(),
										$Field->getName(),
										$FieldArr['type'],
										$FieldArr['length'],
										$FieldArr['nullable'] ? 'NULL' : 'NOT NULL'
									);
				break;
				
			case self::UPDATE_FIELD:
					return sprintf("ALTER TABLE %s MODIFY %s %s(%s) %s;", 
										$this->getName(),
										$Field->getName(),
										$FieldArr['type'],
										$FieldArr['length'],
										$FieldArr['nullable'] ? 'NULL' : 'NOT NULL'
									);
				break;
		}
	}
	
}

/**
 * Field entity
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoField{
	
	protected $FieldName, $Type, $Nullable, $Length;
	
	/**
	 * Set field properties
	 *
	 * @return void
	 * @author paul
	 **/
	function __construct($FieldName, $Type, $Nullable=null, $Length=null){
		if(!$FieldName) throw new AvocadoException("FieldName can't be null");
		if(!$Type) throw new AvocadoException("Field type can't be null");
		$this->FieldName = $FieldName;
		$this->Type = $Type;
		$this->Nullable = (bool)$Nullable;
		$this->Length = (int)$Length;
	}
	
	/**
	 * Return field name
	 *
	 * @return string
	 * @author paul
	 **/
	public function getName(){
		return $this->FieldName;
	}
	
	/**
	 * Return field in array form
	 *
	 * @return array
	 * @author paul
	 **/
	public function toArray(){
		return array(
				'name' => $this->FieldName,
				'type' => $this->Type,
				'nullable' => $this->Nullable,
				'length' => $this->Length,
			);
	}
	
}


?>