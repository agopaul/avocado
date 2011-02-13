<?php

/**
* AvocadoSchema Tests
*/
class TestAvocadoSchema extends UnitTestCase{
	
	function __construct(){

		parent::__construct();

		$this->Pdo = new PDO('mysql:host=localhost;dbname=avocado_tests', "root", "qwerty");

		$this->Pdo->query("DROP TABLE IF EXISTS people; CREATE TABLE people(
							id INT PRIMARY KEY,
							name VARCHAR(255) NOT NULL,
							surname TEXT NOT NULL
						)");

		$this->Pdo->query("DROP TABLE IF EXISTS orders; CREATE TABLE orders (
								id INT PRIMARY KEY,
								customer_id INT,
								salesperson_id INT
							)");

	}

	function setUp(){
		$this->Schema = AvocadoSchema::getInstanceFromDb($this->Pdo);
	}

	function testDbHydration(){
		$Schema = AvocadoSchema::getInstanceFromDb($this->Pdo);
		$this->assertEqual(count($Schema->getTables()), 2);
	}

	function testArrayHydration(){
		$Input = array(
				"orders" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>0),
						array("name"=>"customer_id", "type"=>"int", "nullable"=>true, "length"=>0),
						array("name"=>"salesperson_id", "type"=>"int", "nullable"=>true, "length"=>0),
					),
				"people" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>0),
						array("name"=>"name", "type"=>"varchar", "nullable"=>false, "length"=>255),
						array("name"=>"surname", "type"=>"text", "nullable"=>false, "length"=>0),
					)
			);
		$Schema = AvocadoSchema::getInstanceFromArray($Input);
		$this->assertIdentical($Schema->toArray(), $Input);
	}

	function testExistsArrayAccessIterator(){
		$this->assertTrue(isset($this->Schema['orders']));
	}

	function testGetArrayAccessIterator(){
		$this->assertEqual($this->Schema['orders']->getName(), "orders");
	}

	function testSetArrayAccessIterator(){
		$Input = array(
				"countries" => array(
						array("name"=>"id", "type"=>"int", "nullable"=>false, "length"=>0),
						array("name"=>"country_id", "type"=>"int", "nullable"=>true, "length"=>0)
					)
			);
		$Schema2 = AvocadoSchema::getInstanceFromArray($Input);

		$this->Schema[] = $Schema2["countries"];
		$this->asserttrue(isset($this->Schema['countries']));
	}
	
	function testUnsetArrayAccessIterator(){
		unset($this->Schema['orders']);
		$this->assertFalse(isset($this->Schema['orders']));
	}

}


?>