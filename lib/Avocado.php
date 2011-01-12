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

/**
 * Table entity
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoTable{ // implements ArrayIterator
	
	protected $Tablename, $Fields;
	
	function __construct($Tablename, array $Fields){
		if(!$Tablename) throw new AvocadoException("Tablename can't be null");
		$this->Tablename = $Tablename;
		$this->Fields = $Fields;
		foreach($this->Fields as $Field)
			$Field->setTable($this);
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
	 * Return the table SQL
	 *
	 * @return string
	 * @author paul
	 **/
	public function toSql(){
		$Sql = "CREATE TABLE {$this->getName()}(\n";
		foreach($this->getFields() as $Field){
			$Sql .= $Field->toSql(AvocadoField::ADD_TABLE_FIELD) . ",\n";
		}
		return $Sql . ")";
	}
	
}

/**
 * Field entity
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoField{
	
	/**
	 * Add field option
	 **/
	const ADD_TABLE_FIELD = "ADD_T";

	/**
	 * Add field option
	 **/
	const ADD_FIELD = "ADD";
	
	/**
	 * Update field option
	 **/
	const UPDATE_FIELD = "UPD";

	protected $FieldName, $Type, $Nullable, $Length, $Table;
	
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
	 * Get the table
	 *
	 * @return AvocadoTable
	 * @author paul
	 **/
	function getTable(){
		return $this->Table;
	}

	/**
	 * Set the table
	 *
	 * @return void
	 * @author paul
	 **/
	function setTable(AvocadoTable $Table){
		$this->Table = $Table;
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

	/**
	 * Return sql to create/modify field
	 *
	 * @return string
	 * @author paul
	 **/
	function toSql($Action=self::ADD_FIELD){
		if(!$this->Table) throw new AvocadoException("You must set the Table");

		switch($Action){
			case self::ADD_FIELD:
					return sprintf("ALTER TABLE %s ADD %s %s(%s) %s;", 
										$this->Table->getName(),
										$this->getName(),
										$this->Type,
										$this->Length,
										$this->Nullable ? 'NULL' : 'NOT NULL'
									);
				break;
				
			case self::UPDATE_FIELD:
					return sprintf("ALTER TABLE %s MODIFY %s %s(%s) %s;", 
										$this->Table->getName(),
										$this->getName(),
										$this->Type,
										$this->Length,
										$this->Nullable ? 'NULL' : 'NOT NULL'
									);
				break;

			case self::ADD_TABLE_FIELD:
					return sprintf("'%s' %s%s %s",
										$this->getName(),
										$this->Type,
										$this->Length ? "($this->Length)" : null,
										$this->Nullable ? 'NULL' : 'NOT NULL'
									);
				break;
				
			default:
					throw new AvocadoException("Unknow action");
				break;
		}
	}
	
}

/**
 * Handles two schemas' differences
 *
 * @package default
 * @author paul
 **/
class AvocadoSchemaDiff{
	
	protected $FirstHas, $SecondtHas;
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author paul
	 **/
	function __construct(){}
	
	/**
	 * Insert a new Table which secondSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function firstHasTable(AvocadoTable $Table){
		$this->FirstHas[] = $Table;
	}
	
	/**
	 * Insert a new Table which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function secondtHasTable(AvocadoTable $Table){
		$this->SecondtHas[] = $Table;
	}
	
}


?>