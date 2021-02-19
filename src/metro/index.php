<?php
/*cette page s'occupe des redirection vers les espaces personnels de chacun ainsi que vers la page de connexion
le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur*/

session_start();
//si user non identifié - redirection vers la page de connexion
if(!isset($_SESSION['metro']))
	header('Location: ./connexion.php');
elseif(isset($_SESSION['metro']['categUser']) && $_SESSION['metro']['categUser']==1)//admin
	header('Location: ./admin/index.php');
elseif(isset($_SESSION['metro']['labo']) && $_SESSION['metro']['labo']==1)//emc
	header('Location: ./emc/index.php');
elseif(isset($_SESSION['metro']['labo']) && $_SESSION['metro']['labo']==2)//vib
	header('Location: ./vib/index.php');
elseif(isset($_SESSION['metro']['labo']) && $_SESSION['metro']['labo']==3)//vth
	header('Location: ./vth/index.php');
else
	header('Location: ./deconnexion.php');