<?php

/**
* TestAvocadoFieldDiff Tests
*/
class TestAvocadoFieldDiff extends UnitTestCase{
	

	function setUp(){

		$this->Diff = new AvocadoFieldDiff;

		$this->Source = new AvocadoField("timestamp", "VARCHAR", false, 255);
		$this->Destination = new AvocadoField("timestamp", "DATETIME", true, null);

	}

	function testCompareFields(){
		$Diff = AvocadoFieldDiff::compareFields($this->Source, $this->Destination);
		$this->assertEqual($Diff->getNewType(), "VARCHAR");
		$this->assertEqual($Diff->getNewNullable(), false);
		$this->assertEqual($Diff->getNewLength(), 255);
	}

}

?>