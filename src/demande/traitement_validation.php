<?php
require('top.php');
if(isset($_SESSION['idDP']) && isset($_POST['valid']) && $_POST['valid']==0)//l'utilisateur a envoyé la demande
{
	//l'étape a été verifié dans l'autre partie de la page
	require('../conf/connexion_param.php'); //connexion a la bdd
	require ('../fonction.php');
	$idDP=$_SESSION["idDP"];
	//on met a jour la validite de la demande
	$str="update DEMANDE_PROCEDURE set validiteDP=4 where idDP=$idDP;";
	$reqEmp=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de mis a jour de la validité de la demande</strong></div>';
	else
	{
		//on met a jour la date de demande
		$str="update DEMANDE_PROCEDURE set dateDemandeDP_redigerDP=date_format(now(),'%Y-%m-%d') where idDP=$idDP;";
		$req=mysqli_query($bdd,$str);
		
		if(!$req)
			echo '<div class="alert alert-danger"><strong>Erreur de mis a jour de la date de demande</strong></div>';
		else
		{
			
			
			//on insere les procedures en attente d'affectation
			$str="select idProc from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP;";
			$reqProc=mysqli_query($bdd,$str);
			
			while($lg=mysqli_fetch_object($reqProc)){
				$idProc=$lg->idProc;//recupere la proc
				//insertion de l etat 12 a la date d aujourd hui
				$str="insert into etatProc values (date_format(now(),'%Y-%m-%d %H:%i:%s'),12,$idProc)
				on duplicate key update idproc_procedures=$idProc;";
				mysqli_query($bdd,$str);
								
			}
			
			
			//envoi d'un mail aux responsables de labo
			
			//on recupere les résponsables de labo concernés par la demande
			$str="select u.logUser, p.idService_service, max(et.idetat_etat) as etat, p.idEmp_EMPLOYE,p.idProc from PROCEDURES p, employe e, utilisateur u, etatproc et
			where p.idDP_DEMANDE_PROCEDURE=$idDP
			and p.idService_service=e.idService_service
			and u.idEmp_employe=e.idemp
			and u.categUser=3
            and et.idproc_procedures=p.idproc
            group by u.logUser, p.idService_service;";
			$req=mysqli_query($bdd,$str);
			
			$resMail=0; //0 -> la fonction mail a correctement été executé
			while($resMail==0 && ($lg=mysqli_fetch_object($req)))
			{	
				$etat=$lg->etat;
				if($etat==12) //nouvelle proc
				{
					$obj="Nouvelle demande de procédure";
					$dest=$lg->logUser;				
					$corps="Bonjour,\r\nUne nouvelle demande de procédure vient d'être crée, veuillez vous connecter sur l'outil de demandes de procédure (http://thalesmee) pour l'affecter à un rédacteur.";
				}
				else //modif d'une ancienne on avertie celui a qui est affecté la procédure
				{
					$idEmpRedac=$lg->idEmp_EMPLOYE; //idemp de la personne a qui est affecté la proc
					$idProc=$lg->idProc; 
					$obj="Modification d'une procédure";
					
					$str="select u.logUser from utilisateur u, employe e
					where e.idEmp=u.idEmp_EMPLOYE
					and e.idEmp=$idEmpRedac";
					$reqRedac=mysqli_query($bdd,$str);
					$dest=mysqli_fetch_object($reqRedac)->logUser;
					$corps="Bonjour,\r\nUne procédure (n°$idProc) vient d'être modifiée, veuillez vous connecter sur l'outil de demandes de procédure (http://thalesmee) pour voir les modifications.";
				}
				$emet=$_SESSION['infoUser']['login'];
				$cc="";
				$resMail=envoi_mail($obj, $dest, $emet, $cc, $corps,$lg->idService_service);
			}
			if($resMail!=0)
				echo '<div class="alert alert-danger"><strong>Erreur d\'envoi de mail</strong></div>';
			
			echo "<center><div class='alert alert-success'><strong>La demande $idDP a été validée avec succès</strong></div>";
			echo '<input type="button" class="btn btn-lg btn-primary"  value="Accueil" id="valider" onclick="document.location.href=\'index.php\';"/></center>';
		}
	}
}
else
	echo '<div class="alert alert-danger"><strong>Erreur de récuperation de la demande</strong></div>';
require('bottom.php');