<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET["num"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$num=$_GET["num"];
	$trouv=false;
	
	//par soucis d'optimisation on évitera les 'or', peux empecher l'utilisation des index, mieux vaut deux requetes
	//la plupart du temps scan du trescalID
	$str="SELECT i.numInstru, de.nomDes, s.nomStatut, i.modele, i.marque, i.date_futureInt, i.numSerie, l.nomLocal
	from statut s, instrument_vib_capteur ic, instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	where i.trescalID='$num'
	and i.idStatut_statut=s.idStatut
	and ic.numInstru_instrument=i.numInstru;";
	$req=mysqli_query($bdd, $str);
	
	if(mysqli_num_rows($req)==0)
	{
		$str="SELECT i.numInstru, de.nomDes, s.nomStatut, i.modele, i.marque, i.date_futureInt, i.numSerie, l.nomLocal
		from statut s, instrument_vib_capteur ic, instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		where i.numInstru='$num'
		and i.idStatut_statut=s.idStatut
		and ic.numInstru_instrument=i.numInstru;";
		$req=mysqli_query($bdd, $str);
		
		if(mysqli_num_rows($req)==0)
		{
			$str="SELECT i.numInstru, de.nomDes, s.nomStatut, i.modele, i.marque, i.date_futureInt, i.numSerie, l.nomLocal
			from statut s, instrument_vib_capteur ic, instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
			LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
			where i.numSerie='$num'
			and i.idStatut_statut=s.idStatut
			and ic.numInstru_instrument=i.numInstru;";
			$req=mysqli_query($bdd, $str);
			if(mysqli_num_rows($req)>0)
				$trouv=true;
		}
		else
			$trouv=true;
	}
	else
		$trouv=true;
	
	if($trouv)
	{
		$lg=mysqli_fetch_object($req);
		
		$dejaUtil=false;
		//on verifie si le capteur n'est pas deja utilisé sur un autre pot
		$str="select c.idInstruCapt_instrument_vib_capteur 
		from concerneBidon c, instrument_vib_capteur iv
		where c.idInstruCapt_instrument_vib_capteur=iv.idInstruCapt
		and iv.numInstru_instrument='".$lg->numInstru."';";
		$req=mysqli_query($bdd, $str);
		echo mysqli_error($bdd);
		if(mysqli_num_rows($req)!=0)
			$dejaUtil=true;
			
		$output = array(
			"numInstru" => $lg->numInstru,
			"nomDes" => $lg->nomDes,
			"nomStatut" => $lg->nomStatut,
			"modele" => $lg->modele,
			"marque" => $lg->marque,
			"date_futureInt" => $lg->date_futureInt,
			"numSerie" => $lg->numSerie,
			"nomLocal" => $lg->nomLocal,
			"dejaUtil" => $dejaUtil
		);
		echo json_encode( $output );
	}
}