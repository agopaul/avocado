<?php

/**
* AvocadoIndex Tests
*/
class TestAvocadoIndex extends UnitTestCase{
	

	function setUp(){
		$this->Index = new AvocadoIndex(array("id"), AvocadoIndex::PRIMARY);
	}

	function testGetters(){
		$this->assertEqual($this->Index->getFields(), array("id"));
		$this->assertEqual($this->Index->getType(), AvocadoIndex::PRIMARY);
	}
}

?>