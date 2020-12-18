<?php
session_start();
if (isset ($_GET['capteur'])){
	

	$capt = $_GET['capteur'];
	$tab = explode(",", $capt);
	$_SESSION['capteur']=$tab;
	echo json_encode( $_SESSION['capteur']);
	
	
	
}

?>