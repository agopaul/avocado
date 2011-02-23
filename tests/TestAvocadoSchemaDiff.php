<?php

/**
* AvocadoSchemaDiff Tests
*/
class TestAvocadoSchemaDiff extends UnitTestCase{
	

	function setUp(){
		$this->Diff = new AvocadoSchemaDiff;

		$this->Table1 = new AvocadoTable("people",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("name", "varchar", true, 255),
				new AvocadoField("notes", "text", true, null)
			));

		$this->Table2 = new AvocadoTable("orders",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("customer_id", "int", true, 10)
			));
		
		$this->Field1 = new AvocadoField("name", "varchar", true, 255);

		$this->Field2 = new AvocadoField("customer_id", "int", true, 10);
	}

	function testGetAll(){
		$this->Diff->firstHasTable($this->Table1);
		$this->Diff->secondHasField($this->Field1);

		$All = $this->Diff->getAll();

		foreach($All["first_has_tables"]+$All["second_has_tables"] as $Item)
			$this->assertIsA($Item, "AvocadoTable");
		
		foreach($All["first_has_fields"]+$All["second_has_fields"] as $Item)
			$this->AssertIsA($Item, "AvocadoField");

		$this->assertEqual($All["first_has_tables"], 
							array(
								$this->Table1
							));

		$this->assertEqual($All["second_has_fields"], 
							array(
								$this->Field1
							));
	}

	function testFirstHasTables(){
		
		$this->Diff->firstHasTable($this->Table1);
		$this->Diff->firstHasTable($this->Table2);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["first_has_tables"], 
							array(
								$this->Table1->toArray(),
								$this->Table2->toArray()
							));
	}

	function testFirstHasFields(){
		
		$this->Diff->firstHasField($this->Field1);
		$this->Diff->firstHasField($this->Field2);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["first_has_fields"], 
							array(
									$this->Field1->toArray(),
									$this->Field2->toArray(),
								));
	}

	function testSecondHasTables(){
		
		$this->Diff->secondHasTable($this->Table1);
		$this->Diff->secondHasTable($this->Table2);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["second_has_tables"], 
							array(
								$this->Table1->toArray(),
								$this->Table2->toArray()
							));
	}

	function testSecondHasFields(){
		
		$this->Diff->secondHasField($this->Field1);
		$this->Diff->secondHasField($this->Field2);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["second_has_fields"], 
							array(
									$this->Field1->toArray(),
									$this->Field2->toArray(),
								));
	}

}

?>