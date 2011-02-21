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
				new AvocadoField("notes", "text", true, 0)
			));
	}

	function testFirstHasTable(){
		
		$this->Diff->firstHasTable($this->Table1);
		$Arr = $this->Diff->toArray();

		$this->assertEqual($Arr["first_has_tables"][0], $this->Table1->toArray());
	}

}

?>