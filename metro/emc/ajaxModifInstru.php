<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET["equip"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$equip=$_GET["equip"];
	$str="select idDes, fonction from designation_emc where idEquip_equipement_emc='$equip';";
	$req=mysqli_query($bdd, $str);

	
	$output = array(
		"equip" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $req ) )
	{
		$row = array(
		"idDes"=>$aRow["idDes"],
		"fonction"=>$aRow["fonction"]	
		);
		
		$output['equip'][] = $row;
	}
	
	echo json_encode( $output );
}
