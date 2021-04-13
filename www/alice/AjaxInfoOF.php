<?php
//header('content-type: text/html; charset=utf-8');
if(!isset($_POST["OF"]))
	echo "1Erreur de reception de l'of";
else
{
	require("connexionAlice.php");
	
	$OF=$_POST["OF"];
	$sql="select a.[Affaire], a.[Libellé] as lib, [n°dossier] as os from [Table Article] a, [Table des OF] o
	where o.[OF]='$OF'
	and a.[n°dossier]=o.[article]";
	$res=$dbh->prepare($sql);
	$res->execute();
	
	$lg=$res->fetch(PDO::FETCH_ASSOC);
	if(count($lg)!=0)
		echo "0".$lg["Affaire"]."/".$lg["lib"]."/".$lg["os"];
}