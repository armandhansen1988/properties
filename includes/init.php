<?php
	require_once('DBC.php');
	
	DBC::init();
	DBC::setConnectionID(1);
	DBC::setServer('localhost');
	DBC::setUser('armandco_mtc');
	DBC::setPassword(');3%oDR)Hgn#');
	DBC::setDatabase('armandco_mtc');
	DBC::setCharset("utf8");
	
	date_default_timezone_set('Africa/Johannesburg');
?>
