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
	 * Compare 2 schemas and save the differences
	 *
	 * @return void
	 * @author paul
	 **/
	public function compareSchemas(AvocadoSchema $Schema1, AvocadoSchema $Schema2){

		$Tables = array_merge($Schema1->getTables(), $Schema2->getTables());

		foreach($Tables as $Table){
			if(isset($Schema1[$Table->getName()]) && !isset($Schema2[$Table->getName()])){
				$this->addTable($Table);
			}
			elseif(isset($Schema2[$Table->getName()]) && !isset($Schema1[$Table->getName()])){
				$this->deleteTable($Table);
			}
			else{

				// Compare fields
				$T1 = $Schema1[$Table->getName()]->getFields();
				$T2 = $Schema2[$Table->getName()]->getFields();

				foreach(array_merge($T1, $T2) as $Field){
					/// TODO :: Check the field properties too
					
					//foreach($T1 as $CurrentField)
					//	$CurrentField->getName()

					if(isset($T1[$Field->getName()]) && !isset($T2[$Field->getName()])){
						$this->addField($Field);
					}
					elseif(isset($T2[$Field->getName()]) && !isset($T1[$Field->getName()])){
						$this->deleteField($Field);
					}
				}

			}
		}
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