<?php

/**
 * Handles two schemas' differences
 *
 * @package default
 * @author paul
 **/
class AvocadoSchemaDiff{
	
	protected $FirstHasTable, $SecondHasTable, $FirstHasField, $SecondHasField;
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author paul
	 **/
	function __construct(){
		$this->FirstHasTable = $this->SecondHasTable = array();
		$this->FirstHasField = $this->SecondHasField = array();
	}
	
	/**
	 * Insert a new Table which secondSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function firstHasTable(AvocadoTable $Table){
		$this->FirstHasTable[] = $Table;
	}
	
	/**
	 * Insert a new Table which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function secondHasTable(AvocadoTable $Table){
		$this->SecondHasTable[] = $Table;
	}

	/**
	 * Insert a new Field which secondSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function firstHasField(AvocadoField $Field){
		$this->FirstHasField[] = $Field;
	}
	
	/**
	 * Insert a new Field which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function secondHasField(AvocadoField $Field){
		$this->SecondHasField[] = $Field;
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
				"first_has_tables" => $this->FirstHasTable,
				"first_has_fields" => $this->FirstHasField,
				"second_has_tables" => $this->SecondHasTable,
				"second_has_fields" => $this->SecondHasField
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

		$FirstHasTable = $FirstHasField = array();
		$SecondHasTable = $SecondHasField = array();

		foreach($this->FirstHasTable as $Table)
			$FirstHasTable[] = $Table->toArray();

		foreach($this->FirstHasField as $Field)
			$FirstHasField[] = $Field->toArray();

		foreach($this->SecondHasTable as $Table)
			$SecondHasTable[] = $Table->toArray();

		foreach($this->SecondHasField as $Field)
			$SecondHasField[] = $Field->toArray();

		return array(
				"first_has_tables" => $FirstHasTable,
				"first_has_fields" => $FirstHasField,
				"second_has_tables" => $SecondHasTable,
				"second_has_fields" => $SecondHasField
			);
	}
	
}

?>