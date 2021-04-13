<?php
require('top.php');

if(!isset($_GET["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération des parametres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$numInstru=$_GET["numInstru"];
	$motif = $_GET["motif"];
	//On supprime l'instrument
	
	$str="SELECT `marque`,`modele`,`numSerie`, nomDes, nomLocal,`numInstru` 
	FROM `instrument`, designation, localisation WHERE `idDes_designation`=idDes and `idLocal_localisation`= idLocal and `numInstru`='$numInstru';";
	$req=mysqli_query($bdd, $str);

	$date =  date("d/m/y");
	$lg=mysqli_fetch_object($req);
	$str="insert into instrument_vib_suppr values(NULL,'".$lg->marque."','".$lg->modele."','".$lg->nomLocal."','".$lg->nomDes."',
	'".$lg->numSerie."','".$lg->numInstru."','$date','$motif')";
	$req=mysqli_query($bdd, $str);
	if(!$req){
		
		echo "<div class='alert alert-danger'><strong>Instrument non ajouté à la corbeille</strong></div>";
	}

	$str="delete from instrument where numInstru = '$numInstru';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de suppression de l'instrument</strong></div>";
	else //tout a fonctionné
	{
		echo '<script src="../js/success.js"></script>';
	}
}

require('bottom.php');