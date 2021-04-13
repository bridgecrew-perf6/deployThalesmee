<?php
require('top.php');
require('../conf/connexion_param.php');

if(isset($_POST["idUser"]))
{
	$idUser=$_POST["idUser"];
	if($idUser!=1)
	{
		//On essaie supprime l'employe
		$str="delete from utilisateur where idUser=$idUser;";
		$req=mysqli_query($bdd, $str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de suppression de l'utilisateur</strong></div>";
		else //tout a fonctionné
		{		
			echo '<div class="text-center">';
				echo "<div class='alert alert-success'><strong>Suppression effectuée</strong></div>";
				echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
			echo '</div>';
		}
	}
	else
	{
		echo '<div class="text-center">';
			echo "<div class='alert alert-warning'><strong>On ne supprime pas l'admin, il ne vous a rien fait</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
		echo '</div>';
	}
}
else
	header("Location: index.php");

require('bottom.php');
?>