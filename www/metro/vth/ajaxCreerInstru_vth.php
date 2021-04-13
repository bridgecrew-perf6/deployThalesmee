<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET["dom"]))
{
	require('../../conf/connexion_param.php'); //connexion a la bdd
	$dom=$_GET["dom"];
	$str="select idDes, nomDes from designation where idDom_domaine='$dom';";
	$req=mysqli_query($bdd, $str);
	
	
	$output = array(
		"des" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $req ) )
	{
		$row = array(
		"idDes"=>$aRow["idDes"],
		"nomDes"=>$aRow["nomDes"]	
		);
		
		$output['des'][] = $row;
	}
	
	echo json_encode( $output );
}
