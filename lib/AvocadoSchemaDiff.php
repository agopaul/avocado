<?php

/**
 * Handles two schemas' differences
 *
 * @package default
 * @author paul
 **/
class AvocadoSchemaDiff{
	
	protected $FirstHasTable, $SecondHasTable, $FirstHasField, $SecondtHasField;
	
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
		$this->FirstHasTable[] = $Table;
	}
	
	/**
	 * Insert a new Table which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function secondHasTable(AvocadoTable $Table){
		$this->SecondtHasTable[] = $Table;
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
		$this->SecondtHasField[] = $Field;
	}
	
}

?>