<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET["num"]))
{
	require('../fonction.php'); 
	require('../conf/connexion_param.php'); //connexion a la bdd
	$num=$_GET["num"];
	$erreur="";
	

	//par soucis d'optimisation on évitera les 'or', peux empecher l'utilisation des index, mieux vaut deux requetes
	//la plupart du temps scan du trescalID
	$str="select i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_futureInt
	from instrument i
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	where i.trescalID='$num';";
	$req=mysqli_query($bdd, $str);
	
	if(mysqli_num_rows($req)==0)
	{
		$str="select i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_futureInt
		from instrument i
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		where i.numInstru='$num'";
		$req=mysqli_query($bdd, $str);
	
		if(mysqli_num_rows($req)==0)
		{
			$str="select i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_futureInt
			from instrument i
			LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
			where i.numSerie='$num'";
			$req=mysqli_query($bdd, $str);
		}
	}

	$lg=mysqli_fetch_object($req);
	$output = array(
		"numInstru" => $lg->numInstru,
		"nomDes" => $lg->nomDes,
		"marque" => $lg->marque,
		"modele" => $lg->modele,
		"numSerie" => $lg->numSerie,
		"nextEtal" => dateSQLToFr($lg->date_futureInt),
	);
	echo json_encode( $output );	
}
else
	echo "Erreur de récéption des parametres";