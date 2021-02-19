<?php
require('top.php');
if(!isset($_SESSION['idDP']))
	echo '<div class="alert alert-danger"><strong>Erreur de récuperation de la demande</strong></div>';
else
{

	//la verification du numéro de l'etape a deja été faite dans la page précédente, la verification de parametres en post oblige a passer par la page précédente
	//Recuperation des articles
	$idDP=$_SESSION['idDP'];
	
	$erreur="";//gestion d'erreur	
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	//Suppression des anciens docs clients de la demande de procedure
	$str="delete from fournirSpec where idDP_DEMANDE_PROCEDURE=$idDP;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		$erreur.='<div class="alert alert-danger"><strong>Erreur de suppression des doc clients</strong></div>';
	else
	{
		
		//Spec Elec et Env
		if (isset($_POST['ref_elec_env'])){$refElecEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ref_elec_env'])));}
		if (isset($_POST['issue_elec_env'])){$issueElecEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['issue_elec_env'])));}
		if (isset($_POST['rev_elec_env'])){$revElecEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['rev_elec_env'])));}else{$revElecEnv="";}
		if (isset($_POST['com_elec_env'])){$comElecEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['com_elec_env'])));}else{$comElecEnv="";}
			
		if($refElecEnv!=""  && $issueElecEnv!="")
		{
			$str="select idSpec from SPEC_CLIENT where reference='$refElecEnv' and issue='$issueElecEnv' and rev='$revElecEnv' and idTypeDoc_TYPE_DOC=20 ;";
			$req=mysqli_query($bdd,$str);
			if(!$req)
				$erreur.='<div class="alert alert-danger"><strong>Erreur de selection du type de document</strong></div>';
			else
			{
				$idSpec=(mysqli_fetch_object($req)->idSpec);
				$str="insert into fournirSpec values ('$comElecEnv',$idSpec,$idDP);";
				$req=mysqli_query($bdd,$str);
			}
		}
		
		
		if (isset($_POST['ref_elec'])){$refElec=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ref_elec'])));}else{$refElec="";}
		if (isset($_POST['issue_elec'])){$issueElec=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['issue_elec'])));}
		if (isset($_POST['rev_elec'])){$revElec=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['rev_elec'])));}else{$revElec="";}
		if (isset($_POST['com_elec'])){$comElec=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['com_elec'])));}else{$comElec="";}
		
		if($refElec != "" && $issueElec!="")
		{
			$str="select idSpec from SPEC_CLIENT where reference='$refElec' and issue='$issueElec' and rev='$revElec' and idTypeDoc_TYPE_DOC=21 ;";
			$req=mysqli_query($bdd,$str);
			if(!$req)
				$erreur.='<div class="alert alert-danger"><strong>Erreur de selection du type de document</strong></div>';
			else
			{
				$idSpec=(mysqli_fetch_object($req)->idSpec);
				$str="insert into fournirSpec values ('$comElec',$idSpec,$idDP);";
				$req=mysqli_query($bdd,$str);
			}
		}
		
		
		//Spec Env
		if (isset($_POST['ref_env'])){$refEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ref_env'])));}
		if (isset($_POST['issue_env'])){$issueEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['issue_env'])));}
		if (isset($_POST['rev_env'])){$revEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['rev_env'])));}else{$revEnv="";}
		if (isset($_POST['com_env'])){$comEnv=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['com_env'])));}else{$comEnv="";}

		if($refEnv!="" && $issueEnv!="")
		{
			$str="select idSpec from SPEC_CLIENT where reference='$refEnv' and issue='$issueEnv' and rev='$revEnv' and idTypeDoc_TYPE_DOC=22 ;";
			$req=mysqli_query($bdd,$str);
			if(!$req)
				$erreur.='<div class="alert alert-danger"><strong>Erreur de selection du type de document</strong></div>';
			else
			{
				$idSpec=(mysqli_fetch_object($req)->idSpec);
				$str="insert into fournirSpec values ('$comEnv',$idSpec,$idDP);";
				$req=mysqli_query($bdd,$str);
			}
		}
		
		//Les autres documents facultatifs
		if (isset($_POST['ref_doc']))
		{
			$tabRef=$_POST['ref_doc'];	
			$tabIssue=$_POST['issue_doc'];	
			$tabRev=$_POST['rev_doc'];	
			$tabType=$_POST['idType'];	
			$tabCom=$_POST['com_doc'];	
			for ($i=0;$i<count($tabRef);$i++)
			{
				$refDoc=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$tabRef[$i])));
				$issueDoc=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$tabIssue[$i])));
				$revDoc=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$tabRev[$i])));
				$tpDoc=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$tabType[$i])));
				$comDoc=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$tabCom[$i])));
				
				$str="select idSpec from SPEC_CLIENT where reference='$refDoc' and issue='$issueDoc' and rev='$revDoc' and idTypeDoc_TYPE_DOC=$tpDoc ;";
				$req=mysqli_query($bdd,$str);
				if(!$req)
					$erreur.='<div class="alert alert-danger"><strong>Erreur de selection du type de document</strong></div>';
				else
				{
					$idSpec=(mysqli_fetch_object($req)->idSpec);
					$str="insert into fournirSpec values ('$comDoc',$idSpec,$idDP);";
					$req=mysqli_query($bdd,$str);
				}
			}
		}
	}
	if($erreur=="") //si aucune erreur
	{
		//on met a jour la validite de la demande
		$str="update DEMANDE_PROCEDURE set validiteDP=3 where idDP=$idDP;";
		$req=mysqli_query($bdd,$str);
		if(!$req) //si erreur
			echo '<div class="alert alert-danger"><strong>Erreur de mis à jour de l\'étape</strong></div>';
		else //sinon redirection
			echo "<script>document.location.href='validationDP.php'</script>";
	}
	else
		echo $erreur;
}
require('bottom.php');
?>

