<?php 
session_start();
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET['essai']))
{
	$_SESSION['essaiFiltre'] = explode(",",$_GET['essai']);
}
elseif(isset($_GET['moyen']))
{
	$_SESSION['moyenFiltre'] = explode(",",$_GET['moyen']);
}
