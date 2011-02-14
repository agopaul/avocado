<?php

/**
* AvocadoSchema Tests
*/
class TestAvocadoTable extends UnitTestCase{
	

	function setUp(){
		$this->Table = new AvocadoTable("orders",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("name", "varchar", true, 255),
				new AvocadoField("notes", "text", true, 0)
			));
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

}

?>