<?php 
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php'); //connexion a la bdd

if(!isset($_POST["idDP"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de la demande</strong></div>';
else
{	
	$idDP=$_POST["idDP"];
	//on recupere l'ancienne dp pour copier les infos
	$str="select affaire, equipement, plateforme, delai, remarque, os from DEMANDE_PROCEDURE where idDP=$idDP;";
	$reqAncDP=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de récupération du de l\'ancienne demande</strong></div>';
	else
	{
		$lg=mysqli_fetch_object($reqAncDP);
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$plateforme=$lg->plateforme;
		$delai=$lg->delai;
		$remarque=$lg->remarque;
		$os=$lg->os;
		
		//insertion de la nouvelle DP
		$str="insert into DEMANDE_PROCEDURE values (null,'$affaire','$equipement','$plateforme','$delai','$remarque','$os',4,date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEmp);";
		$req=mysqli_query($bdd,$str);
		$iddpNouvDP=mysqli_insert_id($bdd); //recuperation de l'id de la nouvelle demande
		
		//on copie aussi les liens provenants de ces tables -> concernerart fournirspec referenceremp vouloirtester
		$str="insert into concernerart select comTypeArt, noArticle_equipement_art, '$iddpNouvDP' from concernerart where idDP_demande_procedure=$idDP;";
		mysqli_query($bdd,$str);

		$str="insert into fournirspec select comTypeArt, idSpec_spec_client, '$iddpNouvDP' from fournirspec where idDP_demande_procedure=$idDP;";
		mysqli_query($bdd,$str);
		
		$str="insert into referenceremp select idFonc_fonctionemp, idemp_employe, '$iddpNouvDP' from referenceremp where idDP_demande_procedure=$idDP;";
		mysqli_query($bdd,$str);
		
		$str="insert into vouloirtester SELECT `nbEquipATester`,`nbMinEquipParTest`,`nbMaxEquipParTest`,`idModele_TYPE_MODELE`,'$iddpNouvDP' from vouloirtester where idDP_demande_procedure=$idDP;";
		mysqli_query($bdd,$str);
		
		//emc
		if(isset($_POST["choix_1"]) && $_POST["refEMC"]!="" && $_POST["issEMC"]!="" )
		{
			//htmlspecialchars et mysqli_real_escape_string($bdd,permettent d'eviter de nombreuses failles sql + des erreurs en cas de saisi de quotes ou autres caracteres spéciaux
			$ref=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["refEMC"]));
			$iss=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["issEMC"]));
			if(isset($_POST["revEMC"]))
				$rev=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["revEMC"]));
			else
				$rev="";
			if(isset($_POST["comEMC"]))
				$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["comEMC"]));
			else
				$remarque="";
			$str="select noArticle_equipement_art from concernerart where idDP_demande_procedure =$iddpNouvDP;";
			$req=mysqli_query($bdd,$str);
			while($lg=mysqli_fetch_object($req))
			{
				$art=$lg->noArticle_equipement_art;
				insertDOC_3IT_Art($ref,'0',$iss,$rev,'31',$iddpNouvDP,$art,$remarque,$bdd);
			}
			//on crée les nouvelles procédures
			$str="insert into PROCEDURES values (null,null,$iddpNouvDP,1,null,null);";
			mysqli_query($bdd,$str);
			$idproc=mysqli_insert_id($bdd);
			

			//insertion de l etat 12 a la date d aujourd hui
			$str="insert into etatProc values (date_format(now(),'%Y-%m-%d %H:%i:%s'),12,$idproc);";
			mysqli_query($bdd,$str);		
		}
		//vib
		if(isset($_POST["choix_2"]) && $_POST["refVIB"]!="" && $_POST["issVIB"]!="" )
		{
			$ref=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["refVIB"]));
			$iss=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["issVIB"]));
			if(isset($_POST["revVIB"]))
				$rev=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["revVIB"]));
			else
				$rev="";
			if(isset($_POST["comVIB"]))
				$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["comVIB"]));
			else
				$remarque="";			
			
			$str="select noArticle_equipement_art from concernerart where idDP_demande_procedure =$iddpNouvDP;";
			$req=mysqli_query($bdd,$str);
			while($lg=mysqli_fetch_object($req))
			{
				$art=$lg->noArticle_equipement_art;
				insertDOC_3IT_Art($ref,'0',$iss,$rev,'32',$iddpNouvDP,$art,$remarque,$bdd);
			}
			//on crée les nouvelles procédures
			$str="insert into PROCEDURES values (null,null,$iddpNouvDP,2,null,null);";
			mysqli_query($bdd,$str);
			$idproc=mysqli_insert_id($bdd);

			//insertion de l etat 12 a la date d aujourd hui
			$str="insert into etatProc values (date_format(now(),'%Y-%m-%d %H:%i:%s'),12,$idproc);";
			mysqli_query($bdd,$str);
			
		}
		if(isset($_POST["choix_3"]) && $_POST["refVTH"]!="" && $_POST["issVTH"]!="" )
		{
			$ref=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["refVTH"]));
			$iss=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["issVTH"]));			
			if(isset($_POST["revVTH"]))
				$rev=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["revVTH"]));
			else
				$rev="";
			if(isset($_POST["comVTH"]))
				$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["comVTH"]));
			else
				$remarque="";	
			
			$str="select noArticle_equipement_art from concernerart where iddp_demande_procedure =$iddpNouvDP;";
			$req=mysqli_query($bdd,$str);
			while($lg=mysqli_fetch_object($req))
			{
				$art=$lg->noArticle_equipement_art;
				insertDOC_3IT_Art($ref,'0',$iss,$rev,'33',$iddpNouvDP,$art,$remarque,$bdd);
			}
			//on crée les nouvelles procédures

			$str="insert into PROCEDURES values (null,null,$iddpNouvDP,3,null,null);";
			mysqli_query($bdd,$str);
			$idproc=mysqli_insert_id($bdd);
			

			//insertion de l etat 12 a la date d aujourd hui
			$str="insert into etatProc values (date_format(now(),'%Y-%m-%d %H:%i:%s'),12,$idproc);";
			mysqli_query($bdd,$str);			
		}
		
		//envoi d'un mail aux responsables de labo

		//on recupere les résponsables de labo concernés par la demande
		$str="select u.logUser, p.idService_service, max(et.idetat_etat) as etat, p.idEmp_EMPLOYE,p.idProc from PROCEDURES p, employe e, utilisateur u, etatproc et
		where p.idDP_DEMANDE_PROCEDURE=$iddpNouvDP
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
		
		echo "<center><div class='alert alert-success'><strong>L'évolution a bien été traité. Nouveau numéro de demande: $iddpNouvDP</strong></div>";
		echo "<input type='button' class='btn btn-lg btn-primary' value='Retour' onclick='document.location.href=\"index.php\"' /></center>";
				
	}
}
?>


<?php
require('bottom.php');
?>