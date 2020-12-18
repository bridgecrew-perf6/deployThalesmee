<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
require('../conf/connexion_param.php');

if(isset($_GET['depTel']))
{
	$dep=$_GET['depTel'];
	if ($dep == "-1") echo "";
	else
	{
		$str="SELECT telDep, portableDep from depositaire where idDep='$dep';";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0)
			$lg = mysqli_fetch_object($req);
			if (isset($lg->portableDep) && $lg->portableDep!="") echo $lg->telDep."-".$lg->portableDep;
			else echo $lg->telDep;
	}
	
}
elseif(isset($_GET['term']))
{
	$dep= $_GET['term']; //le widget autocompletion envoi la valeur saisi dans un GET["term"]
	$str="SELECT distinct nomDep from depositaire where nomDep like '%$dep%';";
	$req=mysqli_query($bdd,$str);
	$array = array(); // on créé le tableau
	if(mysqli_num_rows($req)!=0)
	{
		while($lg=mysqli_fetch_object($req)) 
		{
			array_push($array, $lg->nomDep); 
		}
		echo json_encode($array); //conversion du tableau en JSON 
	}
}