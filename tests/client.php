<?php

require __DIR__."/../lib/Avocado.php";

$Db = new PDO('mysql:host=localhost;dbname=films2', "root", "qwerty");
AvocadoRestClient::getInstance($Db, array())->compareTo("http://hydra/avocado/tests/server.php");



?>