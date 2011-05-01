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

	function testGetType(){
		$this->assertEqual($this->Field->getType(), "int");
	}

	function testGetLength(){
		$this->assertIdentical($this->Field->getLength(), 10);
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
	
}

?>