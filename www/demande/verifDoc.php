<?php
if(isset($_POST["ref"]) && isset($_POST["issue"]) && isset($_POST["rev"]) && isset($_POST["idDoc"]))
{
	// on recherche le document dans la base grace a sa reference, son issue et son type
	require('../conf/connexion_param.php');
	$ref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["ref"])));
	$issue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["issue"])));
	$rev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["rev"])));
	$idDoc=$_POST["idDoc"];

	$str="select idSpec from SPEC_CLIENT where reference='$ref' and issue='$issue' and rev='$rev' and idTypeDoc_TYPE_DOC=$idDoc ;";
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	if(!$req)
		echo "Erreur de recherche du doc";
	else
	{
		if (mysqli_num_rows($req)==0){
			$str="select nom from TYPE_DOC where idTypeDoc=$idDoc;";
			$req=mysqli_query($bdd,$str);
			if(!$req)
				echo 'Erreur de sélection du nom';
			else
			{
				$tpnom=(mysqli_fetch_object($req)->nom);
				echo "[ $tpnom ][ $ref ][ $issue ][ $rev ]";	
			}
		}else //doc dans la base
			echo "";
	}
}
else
	echo "Erreur de récupération des parametres";
?>