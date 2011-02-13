<?php

/**
* AvocadoSchema Tests
*/
class TestAvocadoField extends UnitTestCase{
	

	function setUp(){
		$this->Field = new AvocadoField("id", "int", false, 10);
	}

	function testGetName(){
		$this->assertEqual($this->Field->getName(), "id");
	}

	function testToArray(){
		$Expected = array(
						'name' => "id",
						'type' => "int",
						'nullable' => false,
						'length' => 10,
					);
		$this->assertIdentical($this->Field->toArray(), $Expected);
	}

	function testTablePresenceCallingToSql(){
		
		try{
			$this->Field->toSql(AvocadoField::ADD_FIELD);
			$this->fail("Exception should be raised");
		}
		catch(AvocadoException $e){
			$this->pass("Exception correctly raised");
		}

	}

	function testToSql(){
		
		$AddSql = "ALTER TABLE people ADD id int(10) NOT NULL;";
		$UpdateSql = "ALTER TABLE people MODIFY id int(10) NOT NULL;";
		$AlterSql = "ALTER TABLE people MODIFY id int(10) NOT NULL;";

		$Table = new AvocadoTable("people", array($this->Field));
		$this->assertEqual(
				$this->Field->toSql(AvocadoField::ADD_FIELD),
				$AddSql);
		
		$this->assertEqual(
				$this->Field->toSql(AvocadoField::UPDATE_FIELD),
				$UpdateSql);

	}

}

?>