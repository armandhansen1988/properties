<?php
	require_once('DBC.php');
	
	DBC::init();
	DBC::setConnectionID(1);
	DBC::setServer('localhost');
	DBC::setUser('user');
	DBC::setPassword('password');
	DBC::setDatabase('database');
	DBC::setCharset("utf8");
	
	date_default_timezone_set('Africa/Johannesburg');
?>
