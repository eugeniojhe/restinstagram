<?php
require 'environment.php';
  
$config = array();
if(ENVIRONMENT == 'development') {
	define("BASE_URL", "http://localhost/restinstagram/");
	$config['dbname'] = 'db_instagran';
	$config['host'] = 'localhost';
	$config['dbuser'] = 'root';
	$config['dbpass'] = '';
	$config['jwt_secret_key'] = "udianddf123456";
} else {
	define("BASE_URL", "http://localhost/restinstagram/");
	$config['dbname'] = 'db_instagran';
	$config['host'] = 'localhost';
	$config['dbuser'] = 'root';
	$config['dbpass'] = '';
	$config['jwt_secret_key'] = "udianddf123456";
}

global $db;
try {
	$db = new PDO("mysql:dbname=".$config['dbname'].";host=".$config['host'], $config['dbuser'], $config['dbpass']);
} catch(PDOException $e) {
	echo "ERRO: ".$e->getMessage();
	exit;
}