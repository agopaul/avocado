<?php

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
	function __construct($FieldName, $Type, $Nullable=false, $Length=null){
		if(!$FieldName) throw new AvocadoException("FieldName can't be null");
		if(!$Type) throw new AvocadoException("Field type can't be null");
		if($Length<0 && !is_null($Length))
			throw new AvocadoException("Length type can't be null");

		$this->FieldName = $FieldName;
		$this->Type = $Type;
		$this->Nullable = (bool)$Nullable;
		$this->Length = $Length;
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
	 * Return field type
	 *
	 * @return string
	 * @author paul
	 **/
	public function getType(){
		return $this->Type;
	}

	/**
	 * Return field length
	 *
	 * @return int
	 * @author paul
	 **/
	public function getLength(){
		return $this->Length;
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
		//var_dump($this->FieldName, $this->Length);
		return array(
				'name' => $this->FieldName,
				'type' => $this->Type,
				'nullable' => $this->Nullable,
				'length' => $this->Length,
			);
	}

	
	
}

?>