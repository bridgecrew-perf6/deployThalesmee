<?php
require('top.php');
require('../conf/connexion_param.php');

if(isset($_POST["idUser"]))
{
	$idUser=$_POST["idUser"];
	
	//on recupere le service de l'utilisateur
	$str="select s.idService from utilisateur u, employe e, service s
	where u.idEmp_Employe=e.idEmp
	and e.idService_service=s.idservice
	and u.idUser=$idUser";
	$req=@mysqli_query($bdd, $str);
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Erreur de récupération de l\'utilisateur</strong></div>';
	else
	{
		$idService=mysqli_fetch_object($req)->idService;
	
		//test si l'utilisateur est resp de labo -> resultat dans booleen resp
		$str="select idService from service s, utilisateur u
		where s.idEmp_EMPLOYE=u.idEmp_EMPLOYE
		and idUser=$idUser;";
		$req=mysqli_query($bdd, $str);
		if(!$req) //une erreur dans la requete renvera false
			echo '<div class="alert alert-danger"><strong>Erreur de test si responsable</strong></div>';
		else{
			$resp=true;
			if(mysqli_num_rows($req)==0)
				$resp=false;
		
			//si l'utilisateur n'est pas responsable de labo et est bien un employé (donc affecté a un service de de type 1/2/3 ou demandeur)
			if(!$resp && ($idService==1 || $idService==2 || $idService==3 || $idService==7))
			{
				//On supprime l'employe, le On delete cascade s'occupe de supprimer l'utilisateur
				$str="delete from employe where idEmp =
				(select idEmp_EMPLOYE from utilisateur where idUser=$idUser);";
				$req=mysqli_query($bdd, $str);

				if(!$req)
					echo "<div class='alert alert-danger'><strong>Erreur de suppression de l'employé, il est surement associé a une demande de procédure</strong></div>";
				else //tout a fonctionné
				{
					echo '<center>';
						echo "<div class='alert alert-success'><strong>Suppression éffectuée</strong></div>";
						echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
					echo '</center>';
				}
			}

		}
	}

}
else
	header("Location: index.php");

require('bottom.php');
?>