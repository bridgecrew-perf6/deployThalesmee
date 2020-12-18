<?php

require('./conf/connexion_param.php'); //connexion a la bdd
/*
$str="delete from etatessai where idessai_essai in
(SELECT idEssai FROM essai WHERE idTachePrim in 
	('LPH15052011321189020','LPH15052011321187020','LPH15052011321186020','LPH15052011321051020','TO1735','TO1809','3E029343',
	'3E038369','N3E51677','TO1862','TO1819','TO1829','TO1839','TO1859','TO1869','TO1879','TO1889','TO1899TO1909','TO1840','TO1850',
	'TO1860','TO1870','TO1880','TO1890','TO1900','TO1910','TO1920','TO1919','TO1930','TO1940','TO1950','TO1929','TO1939','TO1949','TO1959')
);";
$req=mysqli_query($bdd,$str);*/

//

$i=1;
$str="select * from essai e";
$reqEssai=mysqli_query($bdd,$str);
while($lg=mysqli_fetch_object($reqEssai)) 
{
	$idEssai=$lg->idEssai;
	$str="select * from tester where idEssai_ESSAI= '$idEssai';";
	$reqTester=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	$str="select * from etatEssai where idEssai_ESSAI= '$idEssai';";
	$reqEtat=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	$idTachePrim=$lg->idTachePrim;
	$badge=$lg->badge;
	$affaire=$lg->affaire;
	$equipement=$lg->equipement;
	$os=$lg->os;
	$commentaire=$lg->commentaire;
	$idActivite_RESERVATION=$lg->idActivite_RESERVATION;
	$idService_SERVICE=$lg->idService_SERVICE;
	$idMoyen_MOYEN=$lg->idMoyen_MOYEN;
	$fifo=$lg->fifo;
	$retard_interne=$lg->retard_interne;
	$planifie=$lg->planifie;
	$idDep_depositaire=$lg->idDep_depositaire;
	$date_debut=$lg->date_debut;
	$date_fin=$lg->date_fin;
	
	$str="delete from tester where idEssai_ESSAI= '$idEssai';";
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	$str="delete from etatEssai where idEssai_ESSAI= '$idEssai';";
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	
	$str="delete from essai where idEssai= '$idEssai';";
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	
	$str="insert into essai values ('$i','$idTachePrim','$badge','$affaire','$equipement','$os','$commentaire','$idActivite_RESERVATION',
	'$idService_SERVICE','$idMoyen_MOYEN','$fifo','$retard_interne','$planifie','$idDep_depositaire','$date_debut','$date_fin');";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	$idEssai=mysqli_insert_id($bdd);
	if(mysqli_num_rows($reqTester)!=0)
	{
		while($lgTester=mysqli_fetch_object($reqTester))
		{
			$noOF_equipement_of=$lgTester->noOF_EQUIPEMENT_OF;
			$str="insert into tester values('$noOF_equipement_of','$idEssai');";
			$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
			$req=mysqli_query($bdd,$str);
			echo mysqli_error($bdd);
		}
	}
	
	if(mysqli_num_rows($reqEtat)!=0)
	{
		
		while($lgEtat=mysqli_fetch_object($reqEtat))
		{
			$dateEtat=$lgEtat->dateEtat;
			$idEtat_ETAT=$lgEtat->idEtat_ETAT;
			$str="insert into etatessai values('$dateEtat','$idEssai','$idEtat_ETAT');";
			$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
			$req=mysqli_query($bdd,$str);
			echo mysqli_error($bdd);
		}
	}
	$i++;
}
$str="ALTER TABLE essai AUTO_INCREMENT = $i";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);
//2643
//4801
//3454