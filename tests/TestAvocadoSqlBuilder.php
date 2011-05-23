<?php

/**
* AvocadoSqlBuilder Tests
*/
class TestAvocadoSqlBuilder extends UnitTestCase{

	private static function getMethod($MethodName){
		$Class = new ReflectionClass("AvocadoSqlBuilder");
		$Method = $Class->getMethod($MethodName);
		$Method->setAccessible(true);
		return $Method;
	}
	

	function setUp(){
		$this->Diff = new AvocadoSchemaDiff;

		$this->Field1 = new AvocadoField("id", "int", false, 10);
		$this->Field2 = new AvocadoField("name", "varchar", true, 255);

		$this->Source = new AvocadoTable("people",array(
				$this->Field1,
				$this->Field2,
				new AvocadoField("notes", "text", true, null)
			));

		$this->Destination = new AvocadoTable("orders",array(
				new AvocadoField("id", "int", false, 10),
				new AvocadoField("customer_id", "int", true, 10)
			));

		$this->Diff->addTable($this->Source);
		//$this->Diff->deleteField($this->Field1);

		$this->Builder = new AvocadoSqlBuilder($this->Diff);
	}

	function testBuild(){

		$Expected = "CREATE TABLE people(\n\t'id' int(10) NOT NULL,\n\t'name' varchar(255) NULL,\n\t'notes' text NULL\n)";

		$this->assertEqual($this->Builder->build(), $Expected);
	}


	// Field testing

	function testTableToSql(){

		$Method = self::getMethod("tableToSql");

		// First table
		$Ret = $Method->invokeArgs($this->Builder, array($this->Source));
		$Expected = "CREATE TABLE people(\n\t'id' int(10) NOT NULL,\n\t'name' varchar(255) NULL,\n\t'notes' text NULL\n)";
		$this->assertEqual($Ret, $Expected);

		// Second table
		$Ret = $Method->invokeArgs($this->Builder, array($this->Destination));
		$Expected = "CREATE TABLE orders(\n\t'id' int(10) NOT NULL,\n\t'customer_id' int(10) NULL\n)";
		$this->assertEqual($Ret, $Expected);

	}

	function testFieldToSql(){

		$Method = self::getMethod("fieldToSql");

		$AddSql = "ALTER TABLE people ADD id int(10) NOT NULL;";
		$UpdateSql = "ALTER TABLE people MODIFY id int(10) NOT NULL;";
		$IntoTableSql = "'id' int(10) NOT NULL";

		$Table = new AvocadoTable("people", array($this->Field1));

		// Create
		$Ret = $Method->invokeArgs($this->Builder, array($this->Field1, AvocadoSqlBuilder::CREATE));
		$this->assertEqual($Ret,$AddSql);

		// Update
		$Ret = $Method->invokeArgs($this->Builder, array($this->Field1, AvocadoSqlBuilder::ALTER));
		$this->assertEqual($Ret, $UpdateSql);
		
		// IntoTable
		$Ret = $Method->invokeArgs($this->Builder, array($this->Field1, AvocadoSqlBuilder::INTO_TABLE));
		$this->assertEqual($Ret, $IntoTableSql);

	}

}

?>