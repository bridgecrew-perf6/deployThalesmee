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
	$str="select i.numInstru, fonction, i.modele, i.marque
	from instrument i, instrument_emc ie
	LEFT OUTER JOIN designation_emc d ON ie.idDes_designation_emc=d.idDes
	where i.trescalID='$num'
	and ie.numInstru_instrument=i.numInstru;";
	$req=mysqli_query($bdd, $str);;
	if(mysqli_num_rows($req)==0)
	{
		$str="select i.numInstru, fonction, i.modele, i.marque
		from instrument i, instrument_emc ie
		LEFT OUTER JOIN designation_emc d ON ie.idDes_designation_emc=d.idDes
		where i.numInstru='$num'
		and ie.numInstru_instrument=i.numInstru;";
		$req=mysqli_query($bdd, $str);
		if(mysqli_num_rows($req)==0)
		{
			$str="select i.numInstru, fonction, i.modele, i.marque
			from instrument i, instrument_emc ie
			LEFT OUTER JOIN designation_emc d ON ie.idDes_designation_emc=d.idDes
			where i.numSerie='$num'
			and ie.numInstru_instrument=i.numInstru;";
			$req=mysqli_query($bdd, $str);
		}
	}

	$lg=mysqli_fetch_object($req);
	$output = array(
		"numInstru" => $lg->numInstru,
		"fonction" => $lg->fonction,
		"modele" => $lg->modele,
		"marque" => $lg->marque
	);
	echo json_encode( $output );	
}
else
	echo "Erreur de récéption des parametres";