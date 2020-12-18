<?php
/* Ce fichier permet de modifier le status d'un employé
* Pour le modifier de actif à inactif ou inversement
*/
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_GET['idUser']) && isset($_GET['logUser'])){ //Si tous les paramètres sont envoyés

	//Stockage des valeurs
	$idUser = $_GET['idUser'];
	$logUser = $_GET["logUser"];
	
	$str = "SELECT idEmp_EMPLOYE FROM utilisateur WHERE idUser = $idUser"; //Récupération de l'identifiant de l'employé
	$req = mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	$idEmp = $lg->idEmp_EMPLOYE; //Stockage de l'identifiant

	$str = "SELECT actif FROM employe WHERE idEmp = $idEmp"; //Selection du status de l'emplyé (actif ou inactif)
	$req = mysqli_query($bdd, $str);

	if (mysqli_fetch_object($req)->actif == 1){ // Si il est actif
		
		
		$str = "UPDATE employe SET actif = 0 WHERE idEmp = $idEmp"; //Changement à inactif
		$req = mysqli_query($bdd, $str);
		echo "inactif";

	}else{
		
		$str = "SELECT idEmp_EMPLOYE FROM utilisateur WHERE logUser = '$logUser'"; //Selection des identifiant des employé portant le même log que le user à modifier
		$req = mysqli_query($bdd, $str);
		while ($lg = mysqli_fetch_object($req)) //POur chaques
		{
			$str = "UPDATE employe SET actif = 0 WHERE idEmp = $lg->idEmp_EMPLOYE"; //Actif à 0. Ainsi si un employé est dans plusieurs service, seul 1 sera actif
			$req_emp = mysqli_query($bdd, $str);
		}
		
		$str = "UPDATE employe SET actif = 1 WHERE idEmp = $idEmp"; //Status à actif pour l'employé concerné
		$req = mysqli_query($bdd, $str);
		echo "actif";
	}


}

?>