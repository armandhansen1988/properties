<?php
	require_once("../includes/init.php");
	require_once("../includes/DBC.php");
	
	$data 	= array();
	$uuid 	= DBC::dbescape($_POST['uuid']);
	$delete = DBC::dbsql("UPDATE properties SET status = '3' WHERE uuid = '$uuid';");
	if($delete){
		$data['error'] = '1';
	}else{
		$data['error'] = '2';
	}
	print json_encode($data);
?>
