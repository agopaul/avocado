<?php

/**
 * Database configuration for unit tests
 *
 * @package avocado
 * @author Paolo Agostinetto
 **/
class AvocadoTestConfig{
	
	static $Dsn = 'mysql:host=localhost;dbname=avocado_tests';
	static $Username = '';
	static $Password = '';

}

/**
 * Client configuration for unit tests
 *
 * @package avocado
 * @author Paolo Agostinetto
 **/
class AvocadoTestClientConfig{
	
	static $Dsn = 'mysql:host=localhost;dbname=db_to_compare_1';
	static $Username = '';
	static $Password = '';
	static $Url = 'http://path/to/avocado/tests/client.php';

}

/**
 * Server configuration for unit tests
 *
 * @package avocado
 * @author Paolo Agostinetto
 **/
class AvocadoTestServerConfig{

	static $Dsn = 'mysql:host=localhost;dbname=db_to_compare_2';
	static $Username = '';
	static $Password = '';
	static $Url = 'http://path/to/avocado/tests/server.php';

}


?>