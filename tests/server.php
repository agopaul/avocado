<?php

require __DIR__."/../lib/Avocado.php";

$Db = new PDO('mysql:host=localhost;dbname=films2_1', "root", "qwerty");
AvocadoRestServer::getInstance($Db, "GET", array('act'=>'get_all'));

?>