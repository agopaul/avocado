<?php

/**
 * Index entity
 *
 * @package default
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoIndex{

	const PRIMARY = 1;
	const UNIQUE = 2;
	const FULLTEXT = 3;
		
	protected $Fields, $Type, $Table;

	function __construct(array $Fields, $Type){
		$this->Fields = $Fields;
		$this->Type = $Type;
	}

	/**
	 * return index's fields
	 *
	 * @return array
	 * @author paul
	 **/
	function getFields(){
		return $this->Fields;
	}

	/**
	 * return index type
	 *
	 * @return integer
	 * @author paul
	 **/
	function getType(){
		return $this->Type;
	}

}

?>