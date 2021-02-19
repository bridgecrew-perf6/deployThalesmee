<?php
	session_start();
	session_destroy();
	//cookies inutilisable avec la config des navigateurs de Thales
	/*if(isset($_COOKIE['pseudo']) && isset($_COOKIE['mdp']))
	{
		setcookie('pseudo', NULL, -1);
		setcookie('mdp', NULL, -1);
	}*/
	header("Location: connexion.php");
	exit();
?>