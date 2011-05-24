<?php

/**
* AvocadoSchemaDiff Tests
*/
class TestAvocadoSchemaDiff extends UnitTestCase{
	

	function setUp(){
		$this->Diff = new AvocadoSchemaDiff;

		$this->Source = new AvocadoTable("people",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("name", "varchar", true, 255),
				new AvocadoField("notes", "text", true, null)
			));

		$this->Destination = new AvocadoTable("orders",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("customer_id", "int", true, 10)
			));
		
		$this->Field1 = new AvocadoField("name", "varchar", true, 255);

		$this->Field2 = new AvocadoField("customer_id", "int", true, 10);
	}

	function testCompareSchemas(){

		// Identical Case
		// Schema 1
		$Schema1Array = array(
				"orders" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"customer_id", "type"=>"int", "nullable"=>true, "length"=>11),
						array("name"=>"salesperson_id", "type"=>"int", "nullable"=>true, "length"=>11),
					),
				"people" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"name", "type"=>"varchar", "nullable"=>false, "length"=>255),
						array("name"=>"surname", "type"=>"text", "nullable"=>false, "length"=>null),
					)
			);

		$this->Schema1 = AvocadoSchema::getInstanceFromArray($Schema1Array);

		// Schema 2
		$Schema2Array = array(
				"orders" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"customer_id", "type"=>"int", "nullable"=>true, "length"=>11),
						array("name"=>"salesperson_id", "type"=>"int", "nullable"=>true, "length"=>11),
					),
				"people" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"name", "type"=>"varchar", "nullable"=>false, "length"=>255),
						array("name"=>"surname", "type"=>"text", "nullable"=>false, "length"=>null),
					)
			);

		$this->Schema2 = AvocadoSchema::getInstanceFromArray($Schema2Array);

		$this->Diff = AvocadoSchemaDiff::compareSchemas($this->Schema1, $this->Schema2);

		foreach($this->Diff->getAll() as $Elem){
			$this->assertTrue(empty($Elem));
		}

		// Different
		$Schema3Array = array(
				"orders2" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"customer_id", "type"=>"int", "nullable"=>true, "length"=>11),
						array("name"=>"salesperson_id", "type"=>"int", "nullable"=>true, "length"=>11),
					),
				"people" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"name", "type"=>"varchar", "nullable"=>false, "length"=>255)
					)
			);

		$Schema3 = AvocadoSchema::getInstanceFromArray($Schema3Array);
		$this->Diff = AvocadoSchemaDiff::compareSchemas($this->Schema1, $Schema3);


	}

	function testGetAll(){
		$this->Diff->addTable($this->Source);
		$this->Diff->deleteField($this->Field1);

		$All = $this->Diff->getAll();

		foreach($All["add_tables"]+$All["delete_tables"] as $Item)
			$this->assertIsA($Item, "AvocadoTable");
		
		foreach($All["add_fields"]+$All["delete_fields"] as $Item)
			$this->AssertIsA($Item, "AvocadoField");

		$this->assertEqual($All["add_tables"], 
							array(
								$this->Source
							));

		$this->assertEqual($All["delete_fields"], 
							array(
								$this->Field1
							));
	}

	function testAddTables(){
		
		$this->Diff->addTable($this->Source);
		$this->Diff->addTable($this->Destination);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["add_tables"], 
							array(
								$this->Source->toArray(),
								$this->Destination->toArray()
							));
	}

	function testAddFields(){
		
		$this->Diff->addField($this->Field1);
		$this->Diff->addField($this->Field2);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["add_fields"], 
							array(
									$this->Field1->toArray(),
									$this->Field2->toArray(),
								));
	}

	function testdeleteTables(){
		
		$this->Diff->deleteTable($this->Source);
		$this->Diff->deleteTable($this->Destination);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["delete_tables"], 
							array(
								$this->Source->toArray(),
								$this->Destination->toArray()
							));
	}

	function testdeleteFields(){
		
		$this->Diff->deleteField($this->Field1);
		$this->Diff->deleteField($this->Field2);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["delete_fields"], 
							array(
									$this->Field1->toArray(),
									$this->Field2->toArray(),
								));
	}

}

?>