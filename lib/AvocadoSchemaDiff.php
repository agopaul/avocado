<?php

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