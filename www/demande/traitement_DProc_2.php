<?php
require('../fonction.php');

// Insertion des employes grace a la fonction insererRefEmp($nomEmp,$fonction)
function insererRefEmp($nomEmp,$fonction,$id,$bdd){

	//on regarde si l employe est deja dans la base
	$str="select idemp from EMPLOYE where nomEmp='$nomEmp';";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		return '<div class="alert alert-danger"><strong>Erreur de test de l\'employé</strong></div>';
	else
	{
		if(mysqli_num_rows($req)==0){
			// l employe n est pas dans la base
			
			// on insere l 'employe dans la base
			$str="insert into EMPLOYE values (null,'$nomEmp',null,null,null,null,null,null, 1);";
			$req=mysqli_query($bdd,$str);
			if(!$req)
				return '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'employé</strong></div>';
			else
				$idEmp=mysqli_insert_id($bdd);
			
		}else{
			// on recupere l identifiant de l employe
			$idEmp =mysqli_fetch_object($req)->idemp;
		}

		// on insere le match dp/employe
		$str="insert into referencerEmp values ('$fonction',$idEmp,$id);";
		$req=mysqli_query($bdd,$str);
		echo mysqli_error($bdd);
		if(!$req)
			return '<div class="alert alert-danger"><strong>Erreur d\'ajout du match dp/employé</strong></div>';
	}
	return "";
}

require('top.php');
//on verifi que le numéro est bien transmi + reception des parametres
if(!isset($_SESSION['idDP']) || !isset($_POST["IRP"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récuperation des parametres</strong></div>';
else
{
	//la verification du numéro de l'etape a deja été faite dans la page précédente, la verification de parametres en post oblige a passer par la page précédente
	//Recuperation des articles
	$idDP=$_SESSION['idDP'];
	
	$erreur="";//gestion d'erreur	
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	$str="select noArticle_EQUIPEMENT_ART  , comTypeArt  from concernerArt where idDP_DEMANDE_PROCEDURE=$idDP  order by noArticle_EQUIPEMENT_ART;";
	$reqArt=mysqli_query($bdd,$str);
	$tabArt=array();
	$i=0;
	while($lg=mysqli_fetch_object($reqArt)){
		$tabArt[$i][0]=$lg->noArticle_EQUIPEMENT_ART;
		$tabArt[$i][1]=$lg->comTypeArt;
		$i++;
	}		
	$nbArt=$i;
	
	//recup des labo concernés
	$str="select idService_SERVICE from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP;";
	$req=mysqli_query($bdd, $str);
	
	$isEMC=false;$isVIB=false;$isVTH=false;
	while($lg=mysqli_fetch_object($req)){ 
		if($lg->idService_SERVICE==1)
			$isEMC=true;
		elseif($lg->idService_SERVICE==2)
			$isVIB=true;
		elseif($lg->idService_SERVICE==3)
			$isVTH=true;
	}
	
	
	// Suppression des anciens match de la DP, si modification
	$str="delete from referencerDoc where idDP_DEMANDE_PROCEDURE=$idDP;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		$erreur.= '<div class="alert alert-danger"><strong>Erreur de suppression des anciens match DP</strong></div>';
	else
	{
		//Plan de test
		$PTref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['PTref'])));
		$PTtype=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['PTtype'])));
		$PTissue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['PTissue'])));
		$PRev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['PTrev'])));

		//insertion dans la base grace a la fonction insertDOC_3IT($ref,$type,$issue,$tpdoc,$idDP)
		$erreur.=insertDOC_3IT($PTref,$PTtype,$PTissue,$PRev,11,$idDP,$bdd);
		
		//Analyse Elec Doc Facultatif
		if ($isEMC){
			$AEref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AEref'])));
			$AEtype=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AEtype'])));
			$AEissue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AEissue'])));
			$AErev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AErev'])));

			//insertion dans la base grace a la fonction insertDOC_3IT($ref,$type,$issue,$tpdoc,$idDP)
			$erreur.=insertDOC_3IT($AEref,$AEtype,$AEissue,$AErev,12,$idDP,$bdd);
		}
			
		//Analyse Meca Doc Facultatif
		if ($isVIB){
			$AMref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AMref'])));
			$AMtype=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AMtype'])));
			$AMissue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AMissue'])));
			$AMrev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['AMrev'])));

			//insertion dans la base grace a la fonction insertDOC_3IT($ref,$type,$issue,$tpdoc,$idDP)
			$erreur.=insertDOC_3IT($AMref,$AMtype,$AMissue,$AMrev,13,$idDP,$bdd);
		}
			
		//Analyse VTH Doc Facultatif
		if ($isVTH){
			$ATref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ATref'])));
			$ATtype=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ATtype'])));
			$ATissue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ATissue'])));
			$ATrev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['ATrev'])));

			//insertion dans la base grace a la fonction insertDOC_3IT($ref,$type,$issue,$tpdoc,$idDP)
			$erreur.=insertDOC_3IT($ATref,$ATtype,$ATissue,$ATrev,14,$idDP,$bdd);
			
			//Doc definition cyclage
			$DCref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['DCref'])));
			$DCtype=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['DCtype'])));
			$DCissue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['DCissue'])));
			$DCrev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['DCrev'])));

			//insertion dans la base grace a la fonction insertDOC_3IT($ref,$type,$issue,$tpdoc,$idDP)
			$erreur.=insertDOC_3IT($DCref,$DCtype,$DCissue,$DCrev,15,$idDP,$bdd);
		}
		
		
		// Suppression des anciens match de la DP de type 16 ou 17
		$str="delete from referencerDocArt where idDP_DEMANDE_PROCEDURE=$idDP and iddoc_document_3it in
		(select iddoc from document_3it where idtypedoc_type_doc=16 or idtypedoc_type_doc=17);";
		$req=mysqli_query($bdd,$str);
		echo mysqli_error($bdd);
		if(!$req)
			$erreur.= '<div class="alert alert-danger"><strong>Erreur de suppression des anciens match DP de type 16 et 17</strong></div>';
		else
		{
			$tabIMeca=array();
			$tabIElec=array();
			//Pour chaque article
			for($i=0;$i< $nbArt;$i++){

				$this_art=$tabArt[$i][0];
				
				$Mref="Mref_".$this_art;
				$Mtype="Mtype_".$this_art;
				$Missue="Missue_".$this_art;
				$Mrev="Mrev_".$this_art;
				
				$Eref="Eref_".$this_art;
				$Etype="Etype_".$this_art;
				$Eissue="Eissue_".$this_art;
				$Erev="Erev_".$this_art;
			
				if ($isEMC){			
					//on recupere les infos de l'interface elec
					$ref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Eref"])));
					$type=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Etype"])));
					$issue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Eissue"])));
					$rev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Erev"])));
				
					//insertion dans la base a l'aide de la fonction insertDOC_3IT_Art($ref,$type,$issue,$tpdoc,$idDP,$noArt)
					$erreur.=insertDOC_3IT_Art($ref,$type,$issue,$rev,16,$idDP,$this_art,null,$bdd);
				}
				
				
				
			
				//on recupere les infos de l'interface meca
				$ref2=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Mref"])));
				$type2=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Mtype"])));
				$issue2=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Missue"])));
				$rev2=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["$Mrev"])));

				//insertion dans la base a l'aide de la fonction insertDOC_3IT_Art($ref,$type,$issue,$tpdoc,$idDP,$noArt)
				$erreur.=insertDOC_3IT_Art($ref2,$type2,$issue2,$rev2,17,$idDP,$this_art,null,$bdd);
			}
			
			
			
			// Suppression des anciens match de la DP, si modification
			$str="delete from referencerEmp where idDP_DEMANDE_PROCEDURE=$idDP;";
			$req=mysqli_query($bdd,$str);
			
			
			//IRP
			$nom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['IRP'])));
			
			$erreur.=insererRefEmp($nom,2,$idDP,$bdd);
			

			/*---------------------------------------------------------------------------------------------------*/
						
			//QA	
			$nom2=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['QA'])));
			$erreur.=insererRefEmp($nom2,1,$idDP,$bdd);
				

			/*---------------------------------------------------------------------------------------------------*/

			//ICAL EMC
			if ($isEMC){
				if(isset($_POST['icalEMC']))
					$nom3=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['icalEMC'])));
				else
					$nom3="";
				$erreur.=insererRefEmp($nom3,3,$idDP,$bdd);	
			}
			/*---------------------------------------------------------------------------------------------------*/

			//ICAL VIB
			if ($isVIB){
				if(isset($_POST['icalVIB']))
					$nom4=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['icalVIB'])));
				else
					$nom4="";
				$erreur.=insererRefEmp($nom4,4,$idDP,$bdd);
		
			}
			/*---------------------------------------------------------------------------------------------------*/

			//ICAL VTH
			if ($isVTH){
				if(isset($_POST['icalVTH']))
					$nom5=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['icalVTH'])));
				else
					$nom5="";
				$erreur.=insererRefEmp($nom5,5,$idDP,$bdd);		
			}
		}
	}
	if($erreur=="") //si aucune erreur
	{
		//on met a jour la validite de la demande
		$str="update DEMANDE_PROCEDURE set validiteDP=2 where idDP=$idDP;";
		$req=mysqli_query($bdd,$str);
		mysqli_close($bdd);
		if(!$req) //si erreur
			echo '<div class="alert alert-danger"><strong>Erreur de mis à jour de l\'étape</strong></div>';
		else //sinon redirection
			echo "<script>document.location.href='DProc_3.php'</script>";
	}
	else
		echo $erreur;
}
require('bottom.php');
?>