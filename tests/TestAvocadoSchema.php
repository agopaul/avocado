<?php

/**
* AvocadoSchema Tests
*/
class TestAvocadoSchema extends UnitTestCase{
	
	function __construct(){

		parent::__construct();

		$this->Pdo = new PDO(AvocadoTestConfig::$Dsn,
							 AvocadoTestConfig::$Username,
							 AvocadoTestConfig::$Password);

		$this->Pdo->query("DROP TABLE IF EXISTS orders; CREATE TABLE orders (
						id INT PRIMARY KEY,
						customer_id INT(11),
						salesperson_id INT(11)
					)");
					
		$this->Pdo->query("DROP TABLE IF EXISTS people; CREATE TABLE people(
							id INT PRIMARY KEY,
							name VARCHAR(255) NOT NULL,
							surname TEXT NOT NULL
						)");

	}

	function setUp(){
		$this->Schema = AvocadoSchema::getInstanceFromDb($this->Pdo);
	}

	// Hydration
	function testDbHydration(){
		$Schema = AvocadoSchema::getInstanceFromDb($this->Pdo);
		$this->assertEqual(count($Schema->getTables()), 2);

		$Tables = $Schema->getTables();
		$this->assertEqual($Tables[0]->getName(), "orders");

		$Fields = $Tables[0]->getFields();
		$this->assertEqual($Fields[1]->getName(), "customer_id");
		$this->assertEqual($Fields[1]->getType(), "int");

		$this->assertEqual($Tables[1]->getName(), "people");

		$Fields = $Tables[1]->getFields();
		$this->assertIdentical($Fields[1]->getLength(), 255);
		
	}

	function testArrayHydration(){
		$Input = array(
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
		$Schema = AvocadoSchema::getInstanceFromArray($Input);
		$this->assertIdentical($Schema->toArray(), $Input);

		/// TODO :: Test toArray switch
	}

	// ArrayAccess Interface
	function testExistsArrayAccess(){
		$this->assertTrue(isset($this->Schema['orders']));
	}

	function testGetArrayAccess(){
		$this->assertEqual($this->Schema['orders']->getName(), "orders");
	}

	function testSetArrayAccess(){
		$Input = array(
				"countries" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>11),
						array("name"=>"country_id", "type"=>"int", "nullable"=>true, "length"=>11)
					)
			);
		$Schema2 = AvocadoSchema::getInstanceFromArray($Input);

		$this->Schema[] = $Schema2["countries"];
		$this->asserttrue(isset($this->Schema['countries']));
	}

	function testUnsetArrayAccess(){
		$this->assertTrue(isset($this->Schema['orders']));
		unset($this->Schema['orders']);
		$this->assertFalse(isset($this->Schema['orders']));
	}

	function testSetArrayAccessDoNotDupplicate(){
		$this->Schema[] = new AvocadoTable("users",array(
				new AvocadoField("id", "int", false, 11),
				new AvocadoField("name", "varchar", true, 255),
				new AvocadoField("username", "varchar", true, 127)
			));
		$this->Schema[] = new AvocadoTable("users",array(
				new AvocadoField("id", "int", false, 11),
				new AvocadoField("username", "varchar", true, 127)
			));
		$this->assertEqual(count($this->Schema->toArray()), 3);
	}

	// Iterator Interface
	function testCountIterator(){
		$this->assertEqual(iterator_count($this->Schema), 2);
	}

	function testTablesFromIterator(){
		foreach($this->Schema as $Key=>$Table){
			$this->assertTrue($Table instanceof AvocadoTable);
			if($Key==0){
				$this->assertEqual($Table->getName(), "orders");
			}
			elseif($Key==1){
				$this->assertEqual($Table->getName(), "people");
			}
		}
	}

	// Other methods
	function testToArray(){

		$Expected = array(
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
		$this->assertIdentical($this->Schema->toArray(), $Expected);
	}


}


?>