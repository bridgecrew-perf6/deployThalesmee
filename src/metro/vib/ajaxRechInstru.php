<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET["num"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$num=$_GET["num"];
	$erreur="";
	

	//par soucis d'optimisation on évitera les 'or', peux empecher l'utilisation des index, mieux vaut deux requetes
	//la plupart du temps scan du trescalID
	$str="select i.numInstru
	from instrument i
	where i.trescalID='$num';";
	$req=mysqli_query($bdd, $str);
	
	if(mysqli_num_rows($req)==0)
	{
		$str="select i.numInstru
		from instrument i
		where i.numInstru='$num'";
		$req=mysqli_query($bdd, $str);
	
		if(mysqli_num_rows($req)==0)
		{
			$str="select i.numInstru
			from instrument i
			where i.numSerie='$num'";
			$req=mysqli_query($bdd, $str);
		}
	}

	$lg=mysqli_fetch_object($req);
	$output = array(
		"numInstru" => $lg->numInstru,
	);
	echo json_encode( $output );	
}
else
	echo "Erreur de récéption des parametres";