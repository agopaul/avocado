<?php

/**
 * Handles two schemas' differences
 *
 * @package default
 * @author paul
 **/
class AvocadoSchemaDiff{
	
	protected $addTable, $deleteTable, $addField, $deleteField;
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author paul
	 **/
	function __construct(){
		$this->addTable = $this->deleteTable = array();
		$this->addField = $this->deleteField = array();
	}
	
	/**
	 * Insert a new Table which secondSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function addTable(AvocadoTable $Table){
		$this->addTable[] = $Table;
	}
	
	/**
	 * Insert a new Table which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function deleteTable(AvocadoTable $Table){
		$this->deleteTable[] = $Table;
	}

	/**
	 * Insert a new Field which secondSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function addField(AvocadoField $Field){
		$this->addField[] = $Field;
	}
	
	/**
	 * Insert a new Field which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function deleteField(AvocadoField $Field){
		$this->deleteField[] = $Field;
	}

	/**
	 * Export the whole diff to an array
	 * of objects
	 *
	 * @return void
	 * @author paul
	 **/
	function getAll(){

		return array(
				"add_tables" => $this->addTable,
				"add_fields" => $this->addField,
				"delete_tables" => $this->deleteTable,
				"delete_fields" => $this->deleteField
			);
	}

	/**
	 * Export the whole diff to an array, 
	 * with tables and field
	 *
	 * @return void
	 * @author paul
	 **/
	function toArray(){

		$addTable = $addField = array();
		$deleteTable = $deleteField = array();

		foreach($this->addTable as $Table)
			$addTable[] = $Table->toArray();

		foreach($this->addField as $Field)
			$addField[] = $Field->toArray();

		foreach($this->deleteTable as $Table)
			$deleteTable[] = $Table->toArray();

		foreach($this->deleteField as $Field)
			$deleteField[] = $Field->toArray();

		return array(
				"add_tables" => $addTable,
				"add_fields" => $addField,
				"delete_tables" => $deleteTable,
				"delete_fields" => $deleteField
			);
	}
	
}

?>