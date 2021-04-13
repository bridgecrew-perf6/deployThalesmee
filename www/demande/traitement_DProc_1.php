<?php
require('../fonction.php');

require('top.php');
//on reverifie que les parametres ont bien été transmis (pour optimiser un seul test suffit)
if(!isset($_POST['nom_aff']))
	echo '<div class="alert alert-danger"><strong>Erreur de récuperation des parametres</strong></div>';
else
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	//htmlspecialchars et mysqli_real_escape_string($bdd,evitent les conflit de saisis + sécurisent la bdd contre certaines attaques comme injection sql
	$affaire=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['nom_aff']));
	$n_os=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['n_os']));
	$equipement=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['nom_eq']));
	$plateforme=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['plateforme']));
	$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['remarque']));
	$delai=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['date']));
	//on tranforme la date sous format sql (yyyy-mm-dd)

	$delai=explode("/",$delai);
	$delai=$delai[2]."-".$delai[1]."-".$delai[0];

	//Laboratoires concernes par la demande de procedure
	if (isset($_POST['isEMC'])){$isEMC=true;}else{$isEMC=false;}
	if (isset($_POST['isVIB'])){$isVIB=true;}else{$isVIB=false;}
	if (isset($_POST['isVTH'])){$isVTH=true;}else{$isVTH=false;}

	//Types de modeles concernes par la demande de procedure
	if (isset($_POST['isEM'])){$isEM=true;}else{$isEM=false;}
	if (isset($_POST['isEQM'])){$isEQM=true;}else{$isEQM=false;}
	if (isset($_POST['isPFM'])){$isPFM=true;}else{$isPFM=false;}
	if (isset($_POST['isFM'])){$isFM=true;}else{$isFM=false;}
	
	//recup des tableaux d'articles
	$tabArt=$_POST["article"];
	$tabType=$_POST["type_art"];
	if(isset($_POST["EMC1"]))
	{
		$tabEMC1=$_POST["EMC1"];
		$tabEMC2=$_POST["EMC2"];
		$tabEMC3=$_POST["EMC3"];
	}
	if(isset($_POST["VIB1"]))
	{
		$tabVIB1=$_POST["VIB1"];
		$tabVIB2=$_POST["VIB2"];
		$tabVIB3=$_POST["VIB3"];
	}
	if(isset($_POST["VTH1"]))
	{
		$tabVTH1=$_POST["VTH1"];
		$tabVTH2=$_POST["VTH2"];
		$tabVTH3=$_POST["VTH3"];
	}
	
	$erreur="";//gestion d'erreur	
	
	
	if(!isset($_SESSION["idDP"])) //création d'une nouvelle demande
	{
		$idEmp=$_SESSION['infoUser']['idEmp'];// ID du demandeur
		$str="insert into DEMANDE_PROCEDURE (idDp,idEmp_employe,validiteDP,dateDemandeDP_redigerDP) values (NULL,$idEmp,0,CURDATE())";
		$req=mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
		if(!$req) //une erreur dans la requete renvera false
			$erreur= '<div class="alert alert-danger"><strong>Erreur d\'ajout de la demande</strong></div>';
		else{
			$idDP=mysqli_insert_id($bdd);
			$_SESSION["idDP"]=$idDP;
		}
	}
	else //modif d'une demande existante
		$idDP=$_SESSION['idDP'];
	
	$str="update demande_procedure set affaire='$affaire', os='$n_os', equipement='$equipement', plateforme='$plateforme', delai='$delai', remarque='$remarque' where idDP=$idDP";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	
	if(!$req)
		$erreur.= '<div class="alert alert-danger"><strong>Erreur de mis a jours de la demande</strong></div>';
	else
	{
		// insertion des types de modeles
		// suppression des anciens modeles
		$str="delete from vouloirTester where idDP_DEMANDE_PROCEDURE=$idDP;";
		$req=mysqli_query($bdd,$str);
		if(!$req)
			$erreur.= '<div class="alert alert-danger"><strong>Erreur suppression des anciens modeles</strong></div>';
		else
		{
			//Recuperation des nombres d equipements
			if ($isEM)
			{
				if (isset($_POST["nb_EM"])){$nb_EM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nb_EM"]));}
				if (isset($_POST["max_EM"])){$max_EM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["max_EM"]));}
				if (isset($_POST["min_EM"])){$min_EM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["min_EM"]));}
				$str="insert into vouloirTester values ($nb_EM,$min_EM,$max_EM,1,$idDP);";
				$req=mysqli_query($bdd,$str);
			}
			if ($isEQM)
			{
				if (isset($_POST["nb_EQM"])){$nb_EQM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nb_EQM"]));}
				if (isset($_POST["max_EQM"])){$max_EQM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["max_EQM"]));}
				if (isset($_POST["min_EQM"])){$min_EQM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["min_EQM"]));}
				$str="insert into vouloirTester values ($nb_EQM,$min_EQM,$max_EQM,2,$idDP);";
				$req=mysqli_query($bdd,$str);
			}
			if ($isPFM)
			{
				if (isset($_POST["nb_PFM"])){$nb_PFM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nb_PFM"]));}
				if (isset($_POST["max_PFM"])){$max_PFM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["max_PFM"]));}
				if (isset($_POST["min_PFM"])){$min_PFM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["min_PFM"]));}
				$str="insert into vouloirTester values ($nb_PFM,$min_PFM,$max_PFM,3,$idDP);";
				$req=mysqli_query($bdd,$str);
			}
			if ($isFM)
			{
				if (isset($_POST["nb_FM"])){$nb_FM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nb_FM"]));}
				if (isset($_POST["max_FM"])){$max_FM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["max_FM"]));}
				if (isset($_POST["min_FM"])){$min_FM= (int) htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["min_FM"]));}
				$str="insert into vouloirTester values ($nb_FM,$min_FM,$max_FM,4,$idDP);";
				$req=mysqli_query($bdd,$str);
			}

		
			// insertion des articles;
			// suppression des anciens match DP/art si on modifie la demande de procedure
			$str="delete from concernerArt where idDP_DEMANDE_PROCEDURE=$idDP;";
			$req=mysqli_query($bdd,$str);
			if(!$req)
				$erreur.= '<div class="alert alert-danger"><strong>Erreur suppression des anciens articles</strong></div>';
			else
			{				
				//on ajoute les proc des articles si besoin -> si tous les articles renseigné -> passage immediat au compte rendu SINON -> trois etape ->
				$verifProc=true;
				 mysqli_query($bdd,"delete from referencerDocArt where idDP_DEMANDE_PROCEDURE='$idDP' and idDoc_DOCUMENT_3IT in
				 (select idDoc from DOCUMENT_3IT where idTypeDoc_TYPE_DOC in
					(select idTypeDoc from TYPE_DOC where categorie='PROCEDURE'));");
				for($i=0; $i< count($tabArt);$i++)
				{	
					$this_art=htmlspecialchars(mysqli_real_escape_string($bdd,$tabArt[$i]));
					$this_art_type=htmlspecialchars(mysqli_real_escape_string($bdd,$tabType[$i]));
					if(isset($tabEMC1[$i]))
						$EMC1=htmlspecialchars(mysqli_real_escape_string($bdd,$tabEMC1[$i]));
					if(isset($tabEMC2[$i]))
						$EMC2=htmlspecialchars(mysqli_real_escape_string($bdd,$tabEMC2[$i]));
					if(isset($tabEMC3[$i]))
						$EMC3=htmlspecialchars(mysqli_real_escape_string($bdd,$tabEMC3[$i]));
					if(isset($tabVIB1[$i]))
						$VIB1=htmlspecialchars(mysqli_real_escape_string($bdd,$tabVIB1[$i]));
					if(isset($tabVIB2[$i]))
						$VIB2=htmlspecialchars(mysqli_real_escape_string($bdd,$tabVIB2[$i]));
					if(isset($tabVIB3[$i]))
						$VIB3=htmlspecialchars(mysqli_real_escape_string($bdd,$tabVIB3[$i]));
					if(isset($tabVTH1[$i]))
						$VTH1=htmlspecialchars(mysqli_real_escape_string($bdd,$tabVTH1[$i]));
					if(isset($tabVTH2[$i]))
						$VTH2=htmlspecialchars(mysqli_real_escape_string($bdd,$tabVTH2[$i]));
					if(isset($tabVTH3[$i]))
						$VTH3=htmlspecialchars(mysqli_real_escape_string($bdd,$tabVTH3[$i]));
					
					//on regarde si l'article est deja dans la base (0 si non, 1 si oui)
					$str="select noArticle from EQUIPEMENT_ART where noArticle=$this_art;";
					$req=mysqli_query($bdd,$str);
					echo mysqli_error($bdd);
						
					if (mysqli_num_rows($req)==0){
						// l article n est pas dans la base, on l y insere
						$str="insert into EQUIPEMENT_ART values ($this_art,null);";
						$req=mysqli_query($bdd,$str);
					}	
					// on insere le match DP/Article
					$str="insert into concernerArt values ('$this_art_type',$this_art,$idDP);";
					$req=mysqli_query($bdd,$str);
					
					
					//EMC
					if(isset($EMC1) && $EMC1!="" && $EMC2!=""){
						
						$erreur.=insertDOC_3IT_Art($EMC1,'0',$EMC2,$EMC3,'31',$idDP,$this_art,null,$bdd);
					}elseif($isEMC)
						$verifProc=false;
					
					//VIB
					if(isset($VIB1) && $VIB1!="" && $VIB2!=""){
						$erreur.=insertDOC_3IT_Art($VIB1,'0',$VIB2,$VIB3,'32',$idDP,$this_art,null,$bdd);
					}elseif($isVIB)
						$verifProc=false;
						
					//VTH					
					if(isset($VTH1) && $VTH1!="" && $VTH2!=""){
						
						$erreur.=insertDOC_3IT_Art($VTH1,'0',$VTH2,$VTH3,'33',$idDP,$this_art,null,$bdd);
					}elseif($isVTH)
						$verifProc=false;
				}
				
				// insertion des procedures
				$str="select idService_SERVICE from procedures where idDP_DEMANDE_PROCEDURE=$idDP;";
				$req=mysqli_query($bdd,$str);
				if (mysqli_num_rows($req)==0){ //premiere fois que l'on creer les proc
					
					if ($isEMC)
					{
						$str="insert into PROCEDURES values (NULL,null,$idDP,1,null,null);";
						$req=mysqli_query($bdd,$str);
					}
					if ($isVIB)
					{
						$str="insert into PROCEDURES values (NULL,null,$idDP,2,null,null);";
						$req=mysqli_query($bdd,$str);
						
					}
					if ($isVTH)
					{
						$str="insert into PROCEDURES values (NULL,null,$idDP,3,null,null);";
						$req=mysqli_query($bdd,$str);
					}
				}
				else //modif d'une ancienne demande, on ne supprime que si un service est supprimé de la demande
				{
					$isEMCAV=false;
					$isVIBAV=false;
					$isVTHAV=false;
					while($lg=mysqli_fetch_object($req)){
						$type=$lg->idService_SERVICE;
						if($type==1)
							$isEMCAV=true;
						elseif($type==2)
							$isVIBAV=true;
						elseif($type==3)
							$isVTHAV=true;
					}

					if(!$isEMC)
					{
						$str="delete from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP and idService_Service=1;";
						$req=mysqli_query($bdd,$str);
					}
					elseif(!$isEMCAV)
					{
						$str="insert into PROCEDURES values (NULL,null,$idDP,1,null,null);";
						$req=mysqli_query($bdd,$str);
					}

					if(!$isVIB)
					{
						$str="delete from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP and idService_Service=2;";
						$req=mysqli_query($bdd,$str);
					}
					elseif(!$isVIBAV)
					{
						$str="insert into PROCEDURES values (NULL,null,$idDP,2,null,null);";
						$req=mysqli_query($bdd,$str);
					}
					
					if(!$isVTH)	
					{
						$str="delete from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP and idService_Service=3;";
						$req=mysqli_query($bdd,$str);
					}
					elseif(!$isVTHAV)
					{
						$str="insert into PROCEDURES values (NULL,null,$idDP,3,null,null);";
						$req=mysqli_query($bdd,$str);
					}
				}			
			}
		}	
	}
	if($erreur=="")
	{
		//on met a jour la validite de la demande
		$str="update DEMANDE_PROCEDURE set validiteDP=1 where idDP=$idDP;";
		$req=mysqli_query($bdd,$str);
		mysqli_close($bdd);
	
		// on redirige vers le formulaire 2 si verifProc est false sinon vers la validation rapide
		if(!$verifProc)
			echo "<script>document.location.href='DProc_2.php'</script>";
		else
			echo "<script>document.location.href='validationDP_rapide.php'</script>";
	}
	else
		echo $erreur;
}
require('bottom.php');
?>