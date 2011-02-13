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
		//var_dump($this->FieldName, $this->Length);
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
			
			// TODO :: work out this thing
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

?>