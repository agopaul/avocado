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
		$this->alterField = array();
	}

	/**
	 * Compare 2 schemas and save the differences
	 *
	 * @return void
	 * @author paul
	 **/
	public static function compareSchemas(AvocadoSchema $Schema1, AvocadoSchema $Schema2){

		$Instance = new self;

		$Tables = array_merge($Schema1->getTables(true), $Schema2->getTables(true));

		foreach($Tables as $Table){
			if(isset($Schema1[$Table->getName()]) && !isset($Schema2[$Table->getName()])){
				$Instance->addTable($Table);
			}
			elseif(isset($Schema2[$Table->getName()]) && !isset($Schema1[$Table->getName()])){
				$Instance->deleteTable($Table);
			}
			else{

				// Compare fields
				$T1 = $Schema1[$Table->getName()]->getFields(true);
				$T2 = $Schema2[$Table->getName()]->getFields(true);

				foreach($T1+$T2 as $Field){
					$FieldName = $Field->getName();
					
					if(isset($T1[$FieldName]) && !isset($T2[$FieldName])){
						$Instance->addField($T1[$FieldName]);
					}
					elseif(isset($T2[$FieldName]) && !isset($T1[$FieldName])){
						$Instance->deleteField($T2[$FieldName]);
					}
					else{ 
						// Field exists on both tables, 
						// lets compare them
						$FieldDiff = AvocadofieldDiff::compareFields($T1[$FieldName], $T2[$FieldName]);
						
						if($FieldDiff)
							$Instance->alterField($FieldDiff);

						//var_dump($Field->getName(), $FieldDiff);

					}
				}

			}
		}
		return $Instance;
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
	 * Delete a Field which firstSchema doesn't have
	 *
	 * @return void
	 * @author paul
	 **/
	function deleteField(AvocadoField $Field){
		$this->deleteField[] = $Field;
	}

	/**
	 * Alter/Update field properties
	 *
	 * @return void
	 * @author paul
	 **/
	function alterField(AvocadoFieldDiff $FieldDiff){
		$this->alterField[] = $FieldDiff;
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
				"add_tables"	=> $this->addTable,
				"add_fields"	=> $this->addField,
				"delete_tables"	=> $this->deleteTable,
				"delete_fields"	=> $this->deleteField,
				"alter_fields"	=> $this->alterField
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