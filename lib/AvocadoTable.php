<?php

/**
 * Table entity
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoTable implements ArrayAccess, Iterator{
	
	protected $Tablename, $Fields;
	protected $CurrentKey;
	
	function __construct($Tablename, array $Fields){
		if(!$Tablename) throw new AvocadoException("Tablename can't be null");
		$this->Tablename = $Tablename;
		$this->Fields = $Fields;

		// Set the backreference on the field objects
		foreach($this->Fields as $Field)
			$Field->setTable($this);

		$this->rewind();
	}

	/** ArrayAccess iterator methos **/
	public function offsetExists($Key){
		if($Key){
			foreach($this->Fields as $Field){
				if($Field->getName()==$Key)
					return true;
			}
		}
		else
			return false;
	}

	public function offsetGet($Key){
		if($this->offsetExists($Key)){
			foreach($this->Fields as $Field){
				if($Field->getName()==$Key)
					return $Field;
			}
		}
	}

	public function offsetSet($Key, $Field){
		if(!$Field instanceof AvocadoField)
			throw new AvocadoException("You must provide a valid AvocadoField instance");
		
		// Delete the field if it already exists
		if($this->offsetExists($Key) || 
				$this->offsetExists($Field->getName())){
			$this->offsetUnSet($Field->getName());
		}

		$this->Fields[] = $Field;
	}

	public function offsetUnSet($Name){
		if($this->offsetExists($Name)){
			foreach($this->Fields as $Key=>$Field){
				if($Field->getName()==$Name){
					$this->Fields[$Key] = null;
					unset($this->Fields[$Key]);
					return true;
				}
			}
		}
		return false;
	}

	/** Iterator methos **/
	public function seek($Key){
		if(!$this->valid())
			throw new OutOfBoundsException("Invalid seek position ({$this->Key})");
		
		$this->CurrentKey = $Key;
	}

	public function rewind(){
		$this->CurrentKey = 0;
	}

	public function next(){
		++$this->CurrentKey;
	}

	public function key(){
		return $this->CurrentKey;
	}

	public function current(){
		return $this->Fields[$this->CurrentKey];
	}

	public function valid(){
		return isset($this->Fields[$this->CurrentKey]);
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