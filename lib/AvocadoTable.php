<?php

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

?>