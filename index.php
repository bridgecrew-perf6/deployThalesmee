<?php
/*cette page s'occupe des redirection vers les espaces personnels de chacun ainsi que vers la page de connexion
le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur*/

session_start();
//si user non identifié - redirection vers la page de connexion
if(!isset($_SESSION['infoUser']) || !isset($_SESSION["infoUser"]["nom"]))
	header('Location: ./connexion.php');
elseif($_SESSION['infoUser']['categUser']==1)
	header('Location: ./admin/index.php');
elseif($_SESSION['infoUser']['categUser']==2)
	header('Location: ./demande/index.php');
elseif($_SESSION['infoUser']['categUser']==5)
	header('Location: ./essai/index.php');
elseif($_SESSION['infoUser']['categUser']==7)
	header('Location: ./visuessai/index.php');
elseif($_SESSION['infoUser']['categUser']==3 || $_SESSION['infoUser']['categUser']==4)
	header('Location: ./redacteur/index.php');
elseif($_SESSION['infoUser']['categUser']==6)
	header('Location: ./suivimee/index.php');
elseif($_SESSION['infoUser']['categUser']==9)
	header('Location: ./indicessai/index.php');
else
	header('Location: ./connexion.php');


?>
