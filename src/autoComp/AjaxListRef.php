<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
require('../conf/connexion_param.php');

if(isset($_GET['ref']))
{
	$ref=$_GET['ref'];
	$str="select issue,rev from spec_client where reference='$ref' order by issue,rev desc;";
	$req=mysqli_query($bdd,$str);
	if(mysqli_num_rows($req)!=0)
	{
		$lg=mysqli_fetch_object($req);
		echo $lg->issue."/".$lg->rev;
		
	}

}
elseif(isset($_GET['term']))
{
	$ref= $_GET['term']; //le widget autocompletion envoi la valeur saisi dans un GET["term"]
	$str="select reference from spec_client where reference like '$ref%';";
	$req=mysqli_query($bdd,$str);
	$array = array(); // on créé le tableau
	if(mysqli_num_rows($req)!=0)
	{
		while($lg=mysqli_fetch_object($req)) 
		{
			array_push($array, ucfirst(mb_strtolower($lg->reference))); 
		}
		echo json_encode($array); //conversion du tableau en JSON 
	}
}