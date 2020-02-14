<?php
	session_start();
	 
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: *");
	
	require_once("config.php");
	require_once("routers.php");
	require_once("vendor/autoload.php"); 

	$core = new lib\Core\Core;
	$core->run();  
