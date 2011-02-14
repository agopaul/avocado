<?php

/**
* AvocadoSchema Tests
*/
class TestAvocadoTable extends UnitTestCase{
	

	function setUp(){
		$this->Table = new AvocadoTable("people",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("name", "varchar", true, 255),
				new AvocadoField("notes", "text", true, 0)
			));
	}

	function testGetName(){
		$this->assertEqual($this->Table->getName(), "people");
	}

	// Iterator Interface
	function testCountIterator(){
		$this->assertEqual(iterator_count($this->Table), 3);
	}

	function testFieldsFromIterator(){
		foreach($this->Table as $Key=>$Field){
			$this->assertTrue($Field instanceof AvocadoField);
			if($Key==0){
				$this->assertEqual($Field->getName(), "id");
			}
			elseif($Key==1){
				$this->assertEqual($Field->getName(), "name");
			}
			elseif($Key==2){
				$this->assertEqual($Field->getName(), "notes");
			}
		}
	}

	// ArrayAccess Interface
	function testExistsArrayAccess(){
		$this->assertTrue(isset($this->Table['id']));
	}

	function testGetNameArrayAccess(){
		$this->assertEqual($this->Table['id']->getName(), "id");
	}

	function testSetArrayAccess(){
		$this->Table[] = new AvocadoField("address", "varchar", true, 127);
		$this->assertTrue(isset($this->Table['address']));
	}

	function testUnsetArrayAccess(){
		$this->assertTrue(isset($this->Table['name']));
		unset($this->Table['name']);
		$this->assertFalse(isset($this->Table['name']));
	}

	function testSetArrayAccessDoNotDupplicate(){
		$this->Table[] = new AvocadoField("address", "text", true, 0);
		$this->Table[] = new AvocadoField("address", "varchar", true, 255);
		$this->assertEqual(count($this->Table->toArray()), 4);
	}

}

?>