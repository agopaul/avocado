<?php

/**
 * Handles two field' differences
 *
 * @package default
 * @author paul
 **/
class AvocadoFieldDiff{
	
	protected $NewType, $NewLength, $NewNullable, $Name;
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author paul
	 **/
	function __construct(){
		$this->NewType = $this->NewLength = null;
		$this->Name = $this->NewNullable = null;
	}

	/**
	 * Compare 2 fields and return a new instance
	 *
	 * @return void
	 * @author paul
	 **/
	public static function compareFields(AvocadoField $Source, AvocadoField $Destination){
		$Instance = new self;

		// Bothe the source end the 
		// destination should have the same name!
		
		if($Source->getName() != $Destination->getName())
			throw new AvocadoException("Can't provide two completely different fields");

		$Instance->setName($Source->getName());
		
		if($Source->getType() != $Destination->getType())
			$Instance->setNewType($Source->getType());

		if($Source->getNullable() != $Destination->getNullable())
			$Instance->setNewNullable($Source->getNullable());

		if($Source->getLength() != $Destination->getLength())
			$Instance->setNewLength($Source->getLength());
		
		return $Instance;
	}

	/**
	 * sets the Field name
	 *
	 * @return void
	 * @author paul
	 **/
	function setName($Name){
		$this->Name = $Name;
	}

	/**
	 * gets the Field name
	 *
	 * @return string
	 * @author paul
	 **/
	function getName(){
		return $this->Name;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function getNewType(){
		return $this->NewType;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function getNewNullable(){
		return $this->NewNullable;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function getNewLength(){
		return $this->NewLength;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function setNewType($NewType){
		$this->NewType = $NewType;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function setNewNullable($NewNullable){
		$this->NewNullable = $NewNullable;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	function setNewLength($NewLength){
		$this->NewLength = $NewLength;
	}
}