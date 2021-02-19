<?php
require('top.php');
if(!isset($_GET["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$numInstru=$_GET["numInstru"];
	
	$str="select exists(select * from instrument_emc where numInstru_instrument='$numInstru') as t1, 
	exists(select * from instrument_vib where numInstru_instrument='$numInstru') as t2,
	exists(select * from instrument_vib_capteur where numInstru_instrument='$numInstru') as t3,
	exists(select * from instrument_vth where numInstru_instrument='$numInstru') as t4;";
	$req=mysqli_query($bdd, $str);
	$lg=mysqli_fetch_object($req);
	
	if($lg->t1==1)//emc
	{
		include "../emc/detailsInstru.php";
	}
	elseif($lg->t2==1 || $lg->t3==1)//vib
	{
		include "../vib/detailsInstru.php";
	}
	elseif($lg->t4==1)//vth
	{
		include "../vth/detailsInstru_vth.php";
	}
	else echo "iuhjih";
}
require('bottom.php');