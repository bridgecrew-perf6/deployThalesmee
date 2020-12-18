<?php 
/*Ce fichier contient les parametres pour se connecter a la base de donnees en local.*/


$add="127.0.0.1";	/*serveur local*/
$log="mee";	/*utilisateur mee*/
$pass="pipo";	/*mot de passe*/
$nom_db="bdmee_prod"; /*nom de la base de donnees */

$bdd = mysqli_connect($add, $log, $pass, $nom_db);
if(mysqli_connect_errno()) //echec de connexion
{
	echo '<div class="alert alert-danger"><strong>Échec de connexion a la base de données</strong></div>';
}
else
{
	//encodage en utf8
	mysqli_set_charset ( $bdd , "utf8" );
}

//on ne ferme pas une page php par "? >" 
//cela peut entrainer une generation d'espace blanc en html, et dans ce cas fait planter les generations de graphiques
