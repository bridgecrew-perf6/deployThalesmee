<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php');
$idLabo=$_SESSION['infoUser']['idService'];// service du labo
if (isset($_POST['badge'])){

	$hDeb = intval(str_replace (":", "", $_POST['hDebut']));
	$hFin = intval(str_replace (":", "", $_POST['hFin']));

	$dateDeb=explode('/',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['dateDebut'])));
	$dateFin=explode('/',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['dateFin'])));
	$hDebut=explode(':',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['hDebut'])));
	$hFin=explode(':',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['hFin'])));
	$dateFormDeb= date("Y-m-d H:i:s",mktime($hDebut[0], $hDebut[1], 0, $dateDeb[1], $dateDeb[0], $dateDeb[2]));
	$dateFormFin=date("Y-m-d H:i:s",mktime($hFin[0], $hFin[1], 0, $dateFin[1], $dateFin[0], $dateFin[2]));
	
	if ($dateFormDeb < $dateFormFin){
	
	//htmlspecialchars remplace les caracteres speciaux par leurs équivalent html, évite la plupart des erreurs/failles d'injection sql avec par exemple '
		
	$affaire=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['nom_aff']));
	$equipement=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['nom_eq']));
	$os=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['n_os']));
	$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['remarque']));
	$badge=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['badge']));
	$depositaire=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['depositaire']));
	$telDep=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['tel']));
	$idEs=$_POST['idEssai'];
	
	//Gestion des anomalies
	$str = "select idEssai, quiss_mpti, autre, descriptif, heure, eurosPerdus, status from anomalie where idEssai=$idEs";
	$req2 = @mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req2);

	//Modification des anomalies
	if (isset($_POST['anomalie'])){
		
		$choix = $_POST['anomalie'];
		if ($choix == 1){
			
			$sect = $_POST['secteur'];
			$str = "SELECT idLigne FROM ligneproduit WHERE nomLigne = '$sect'";
			$req = mysqli_query($bdd, $str);
			$lg = mysqli_fetch_object($req);
			$sect = $lg->idLigne;
			
			if (mysqli_num_rows($req2)!=0)
			{	
				//Type			
				$quiss = $_POST['quiss/mpti'];
				//Heure
				if (isset($_POST['heure'])) $heure = $_POST['heure'];
				else $heure = '';
				//Coût
				if (isset($_POST['euros'])) $euros = $_POST['euros'];
				else $euros = '';	
				//Desscriptif
				if (isset($_POST['descriptif'])) $decr=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['descriptif']));
				else $decr = '';	
				//Status
				if (isset($_POST['statusEcart'])) $status = intval($_POST['statusEcart']);
				else $status = 0;	
			
				$str = "update anomalie set quiss_mpti ='$quiss', autre='$sect', descriptif='$decr', heure='$heure', eurosPerdus = '$euros', status=$status where idEssai=$idEs;";
				$req2 = @mysqli_query($bdd, $str);
			
			}else
			{
				//Type
				$quiss = $_POST['quiss/mpti'];
				//Heure
				if (isset($_POST['heure'])) $heure = $_POST['heure'];
				else $heure = '';
				//Coût
				if (isset($_POST['euros'])) $euros = $_POST['euros'];
				else $euros = '';	
				//Descriptif
				if (isset($_POST['descriptif'])) $decr=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['descriptif']));
				else $decr = '';	
				//Status
				if (isset($_POST['statusEcart'])) $status = intval($_POST['statusEcart']);
				else $status = 0;	
				
				$str="insert into anomalie value ($idEs, '$quiss', '$sect' ,'$decr', '$heure', '$euros', $status)";
				$req=mysqli_query($bdd,$str);
			}
		}
		
	}else
	{		
		$str = "delete from anomalie where idEssai=$idEs"; //Suppression de l'anomalie
		$req2 = @mysqli_query($bdd, $str);
		$str="delete from cause_anomalie where idEssai_ESSAI = $idEs"; //Suppression de la cause
		$req=mysqli_query($bdd,$str);	
	}

	//Ligne de produit
	if (isset ($_POST['ligneProd']) && $_POST["ligneProd"] != "-1"){

		$ligne = $_POST['ligneProd'];
		
		$str = "SELECT idLigne FROM ligneproduit WHERE nomLigne = '$ligne'";
		$req = mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$ligne = $lg->idLigne;
		
		$str="update essai set ligneProd=$ligne where idEssai = $idEs";
		$req=mysqli_query($bdd,$str);
		
	}else{
		
		$str="update essai set ligneProd=null where idEssai = $idEs";
		$req=mysqli_query($bdd,$str);
	}

	//Pastille de retard (Orange)
	if (isset ($_POST['pastilleOrange'])){
		
		$choix = $_POST['pastilleOrange'];
		if ($choix == 1){
			
			$str="update essai set pastilleOrange=1 where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);
			$str="update essai set retardME=0 where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);
		}
	}else 
	{
		
		$str="select pastilleOrange from essai where idEssai = $idEs";
		$req=mysqli_query($bdd,$str);
		$lg=mysqli_fetch_object($req);
		if ($lg->pastilleOrange != 0){
			$str="update essai set pastilleOrange=2 where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);
		}
		if (isset ($_POST['retardME'])){
		
		$choix = $_POST['retardME'];
		if ($choix == 3){
			
			$str="update essai set retardME=1 where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);
		}
		}else 
		{
			$str="select retardME from essai where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);
			$lg=mysqli_fetch_object($req);
			if ($lg->retardME != 0){
				$str="update essai set retardME=2 where idEssai = $idEs";
				$req=mysqli_query($bdd,$str);
				
			}
			
		}
	}

	//Pastille rouge (Non Planifié)
	if (isset ($_POST['pastilleRouge'])){
		
		$choix = $_POST['pastilleRouge'];
		if ($choix == 2){
			
		$str="update essai set pastilleRouge=1 where idEssai = $idEs";
		$req=mysqli_query($bdd,$str);
		}

	}else 
	{
		$str="update essai set pastilleRouge=0 where idEssai = $idEs";
		$req=mysqli_query($bdd,$str);
	}	
	
	//Nom du technicien
	$str = "SELECT nomEmp FROM vibtesterpar WHERE idEssai=$idEs;";
	$reqTechActuel=mysqli_query($bdd,$str);
	
	if (isset($_POST['tech']) && $_POST['tech'] != "Non renseigné"){
		
		if(mysqli_num_rows($reqTechActuel)>0)
		{
			$nom = $_POST['tech'];
			$str="update vibtesterpar set nomEmp='$nom' where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);	
		}else{
			
			$nom = $_POST['tech'];
			$str="insert into vibtesterpar value($idEs, '$nom')";
			$req=mysqli_query($bdd,$str);	
			
		}
		
	}else if (isset($_POST['tech']) && $_POST['tech'] == "Non renseigné"){
		
		if(mysqli_num_rows($reqTechActuel)>0)
		{
			
			$str="delete from vibtesterpar where idEssai = $idEs";
			$req=mysqli_query($bdd,$str);	
		}
		
	}
	
	//Famille de produit
	$str = "SELECT famille_Famille FROM famille_essai WHERE idEssai_Essai=$idEs;";
	$reqFamActuel=mysqli_query($bdd,$str);
	
	if (isset($_POST['famille']) && $_POST['famille'] != "Non renseigné"){

		$choix = explode ("-",$_POST['famille']);
		$famille = $choix[1];
		$modele = $choix[0];
		$heure = $_POST['heure_famille'];
		if(mysqli_num_rows($reqFamActuel)>0)
		{
			
			$str="UPDATE famille_essai set famille_FAMILLE='$famille', heure_FAMILLE='$heure', modeleFamille_FAMILLE='$modele', resteHeure= '$heure' where idEssai_ESSAI = $idEs";
			$req=mysqli_query($bdd,$str);
			
		}else{
			
			$str="INSERT into famille_essai value($idEs, '$famille', '$heure', '$modele', '$heure', NULL)";
			$req=mysqli_query($bdd,$str);	
			
		}
		
		
	}else if (isset($_POST['famille']) && $_POST['famille'] == "Non renseigné"){
		
		if(mysqli_num_rows($reqFamActuel)>0)
		{
			
			$str="delete from famille_essai where idEssai_ESSAI = $idEs";
			$req=mysqli_query($bdd,$str);	
		}
	}

	//Cause anomalie
	$str = "SELECT nomCause FROM cause_anomalie WHERE idEssai_ESSAI=$idEs;";
	$reqCauActuel=mysqli_query($bdd,$str);

	if (isset($_POST["anomalie"]) && isset($_POST['cause']) && $_POST['cause'] != "Non renseigné"){ //Si renseigné
		
		$cause = $_POST['cause'];
		if(mysqli_num_rows($reqCauActuel)>0) //Et que avant la cause était renseigné
		{
			
			$str="update cause_anomalie set nomCause='$cause' where idEssai_ESSAI = $idEs"; //Update
			$req=mysqli_query($bdd,$str);
			
		}else{
			
			$str="insert into cause_anomalie value($idEs, '$cause')"; //Sinon insert
			$req=mysqli_query($bdd,$str);	
			
		}
		
	}else if (isset($_POST['cause']) && $_POST['cause'] == "Non renseigné"){ //Si pas rensigné
		
		if(mysqli_num_rows($reqCauActuel)>0) //Et que l'anomalie a été déjà rensigné auparavanht
		{
			
			$str="delete from cause_anomalie where idEssai_ESSAI = $idEs"; //Supression
			$req=mysqli_query($bdd,$str);	
		}
	}
	
	//Moyen
	if($_POST['moyen']!=-1)
		$moyen=$_POST['moyen'];
	else
		$moyen="";
	
	//OFs
	$tabnOf=array();
	if(isset($_POST['n_of']))
	{
		$tabnOf=$_POST['n_of'];
		$tabModele=$_POST['modele'];	
		$tabArticle=$_POST['article'];
	}
	
	//construction des dates format sql
	$dateDeb=explode('/',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['dateDebut'])));
	$dateFin=explode('/',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['dateFin'])));
	$hDebut=explode(':',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['hDebut'])));
	$hFin=explode(':',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['hFin'])));
	
	//date renvoi une date dans un format, et mktime crée un timestamp selon les arguments fournies
	// -> dans l'ordre : heure / minute / seconde / mois / jours / année
	$dateFormDeb=date("Y-m-d H:i:s",mktime($hDebut[0], $hDebut[1], 0, $dateDeb[1], $dateDeb[0], $dateDeb[2]));
	$dateFormFin=date("Y-m-d H:i:s",mktime($hFin[0], $hFin[1], 0, $dateFin[1], $dateFin[0], $dateFin[2]));

	//Stockage du dépositaire
	$idDep=$_POST["depositaire"];
	if ($idDep == "-1") $idDep = "NULL";
	
	$str="UPDATE essai set badge='$badge', affaire='$affaire',equipement='$equipement', os='$os', commentaire='$remarque', idMoyen_MOYEN='$moyen', idDep_depositaire='$idDep', date_debut='$dateFormDeb', date_fin='$dateFormFin' where idEssai=$idEs ;";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=@mysqli_query($bdd,$str);
	
	$str="select max(idEtat_ETAT) as etat from etatessai where idEssai_ESSAI= $idEs ;";
	$req=@mysqli_query($bdd,$str);
	$etat=mysqli_fetch_object($req)->etat;
	if ($etat <= 22){
		$str="UPDATE essai set badge='$badge', affaire='$affaire',equipement='$equipement', os='$os', commentaire='$remarque', idMoyen_MOYEN='$moyen', idDep_depositaire=$idDep, date_debut='$dateFormDeb', date_fin='$dateFormFin', date_debut_prevu='$dateFormDeb', date_fin_prevu='$dateFormFin' where idEssai=$idEs ;";
		$req=@mysqli_query($bdd,$str);
	}

	//on rajoute les nouveaux match
	$stop=false; //sert a stoper immediatement la boucle en cas d'erreur, permet aussi de tester si il y a eu des erreurs
	
	//on supprime les anciens match
	$str="delete from tester where idEssai_essai=$idEs ;";
	$req=mysqli_query($bdd,$str);
	
	for($i=0; !$stop && $i < count($tabnOf);$i++){
		$noOF=htmlspecialchars(mysqli_real_escape_string($bdd,$tabnOf[$i]));
		$modele=$tabModele[$i];
		$article=$tabArticle[$i];
		//on verifie que l'OF existe
		$str="select noOF as nb from EQUIPEMENT_OF where noOF='$noOF';";
		$req=@mysqli_query($bdd,$str);
		if(!$req){
			echo '<div class="alert alert-danger"><strong>Erreur de verification de l\'of</strong></div>';
			$stop=true;
		}
		else{
			if(mysqli_num_rows($req)==0) //nouvel of
			{
				$str="insert into EQUIPEMENT_OF values('$noOF',$modele, '$article');";
				$req=mysqli_query($bdd,$str);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de création de l\'of</strong></div>';
					$stop=true;
				}
			}
			else{
				$str="update EQUIPEMENT_OF set idModele_TYPE_MODELE=$modele, article='$article' where noOF='$noOF';";
				$req=mysqli_query($bdd,$str);
				echo mysqli_error($bdd);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de mis à jours de l\'of</strong></div>';
					$stop=true;
				}
			}
			
			//insertion du match essai OF
			$str="insert into tester values('$noOF',$idEs);";
			
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo '<div class="alert alert-danger"><strong>Erreur de d\'ajout du test entre l\'of crée et l\'essai</strong></div>';
				$stop=true;
			}				
		}
		
	}

	$str = "SELECT duree_planifie, deux_huit, samedi FROm essai WHERE idEssai=$idEs";
	$req=@mysqli_query($bdd,$str);
	$lg = mysqli_fetch_object($req);
	$deux_huit = $lg->deux_huit;
	$samedi = $lg->samedi;
	$duree_planifie = $lg->duree_planifie;
	$change = false;

	$duree = dureePrimavera($dateFormDeb, $dateFormFin);

	if (isset($_POST["samedi"]) && $samedi == 0)
	{
		$samedi = samedi ($dateFormDeb, $dateFormFin, $duree);
		if ($samedi != 0)
		{
			if($duree == 0) $duree = $samedi;
			$dateFin = dateFin(round($duree*9,1), $dateFormDeb, 1);
			if ($etat <= 22){
				$str="UPDATE essai set duree_planifie = $duree where idEssai=$idEs ;";
				$req=@mysqli_query($bdd,$str);
			}

			$str="UPDATE essai set date_debut_prevu='$dateFormDeb', date_fin_prevu='$dateFin', duree_actuelle = $duree, date_fin='$dateFin', samedi = $samedi where idEssai=$idEs ;";
			$req=@mysqli_query($bdd,$str);
		}else
		{
			echo '<div class="alert alert-danger"><strong>L\'essai ne se trouve pas pendant un samedi. Veuillez changer la date de l\'essai.</strong></div>';
				$stop=true;
		}
		
		$change = true;

	}else if (!isset($_POST["samedi"]) && $samedi != 0)
	{
		$duree += $samedi;
		$dateFin = dateFin(round($duree*9,1), $dateFormDeb, 0);
		if ($etat <= 22){
			$str="UPDATE essai set duree_planifie = $duree where idEssai=$idEs ;";
			$req=@mysqli_query($bdd,$str);
		}
		$change = true;
		$str="UPDATE essai set date_debut_prevu='$dateFormDeb', date_fin_prevu='$dateFin', duree_actuelle = $duree, date_fin='$dateFin', samedi = 0 where idEssai=$idEs ;";
		$req=@mysqli_query($bdd,$str);
	}


	if (isset($_POST["deux_huit"]) && $deux_huit == 0)
	{

		$duree = $duree_planifie/2;
		$dateFin = dateFin(round($duree*9,1), $dateFormDeb, $samedi);
		if ($etat <= 22){
			$str="UPDATE essai set duree_planifie = $duree where idEssai=$idEs ;";
			$req=@mysqli_query($bdd,$str);
		}
		$str="UPDATE essai set date_debut_prevu='$dateFormDeb', date_fin_prevu='$dateFin', duree_actuelle = $duree, date_fin='$dateFin', deux_huit = 1 where idEssai=$idEs ;";
		$req=@mysqli_query($bdd,$str);
		$change = true; 

	}else if (!isset($_POST["deux_huit"]) && $deux_huit == 1) {

		$duree = $duree_planifie*2;
		$dateFin = dateFin(round($duree*9,1), $dateFormDeb, $samedi);
		if ($etat <= 22){
			$str="UPDATE essai set duree_planifie = $duree where idEssai=$idEs ;";
			$req=@mysqli_query($bdd,$str);
		}

		$str="UPDATE essai set date_debut_prevu='$dateFormDeb', date_fin_prevu='$dateFin', duree_actuelle = $duree, date_fin='$dateFin', deux_huit = 0 where idEssai=$idEs ;";
		$req=@mysqli_query($bdd,$str);
		$change = true;
	}

	if ($change == false)
	{
		if ($etat <= 22){
			$str="UPDATE essai set duree_planifie = $duree where idEssai=$idEs ;";
			$req=@mysqli_query($bdd,$str);
		}
		$str="UPDATE essai set duree_actuelle = $duree, date_debut='$dateFormDeb', date_fin='$dateFormFin' where idEssai=$idEs ;";
		$req=@mysqli_query($bdd,$str);
	}



	
	if(!$stop && !isset($_GET["back"]))
	{
		echo '<script src="../js/success.js"></script>';
	}else if (isset($_GET["back"]) && !$stop){
		
		echo '<script>function redirection(){
	
			document.location.href="'.$_GET["back"].'.php";
		}

		swal({
			
			title : "Informations validées",
			text : "Redirection dans quelques instants",
			icon : "success"
			
		});
		setTimeout(redirection, 1000);</script>';
	}
				
}else {
	echo '<div class="text-center">';
	echo '<div class="alert alert-danger"><strong>Erreur de saisie de la date</strong></div>';
	echo '</div>';
}
}
elseif(!isset($_GET["idEssai"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récéption du numéro de l\'essai</strong></div>';
else{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idEssai=$_GET["idEssai"];
	
	//on recupere les infos
	$str="SELECT e.idEssai, e.badge, e.affaire, e.equipement, e.os,e.ligneProd, e.commentaire, et.idEtat_ETAT, e.date_debut, e.date_fin, e.idDep_depositaire, d.nomDep, d.telDep, e.pastilleOrange, e.pastilleRouge, e.retardME, e.deux_huit, e.samedi, e.duree_planifie, e.duree_actuelle 
	FROM etatEssai et, essai e LEFT JOIN depositaire d on e.idDep_depositaire=d.idDep
	where idEssai =$idEssai 
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEssai_ESSAI=e.idEssai;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de l'essai</strong></div>";
	else{
		
		//Gestion des anomalies
		$str = "select idEssai, quiss_mpti, autre, descriptif, heure, eurosPerdus, status from anomalie where idEssai=$idEssai";
		$req2 = @mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req2);
		if (mysqli_num_rows($req2)==0){
			$anomalie = false;
			$autre = '';
			$decriptif = '';
			$heure = '';
			$euros = '';
			$status=0;
			$quiss = '';
			
		}else{
			$anomalie = true;
			$autre = $lg->autre;
			$str = "SELECT nomLigne FROM ligneproduit WHERE idLigne = $autre";
			$req_ligne = mysqli_query($bdd, $str);
			$lg_ligne = mysqli_fetch_object($req_ligne);
			$autre = $lg_ligne->nomLigne;
			$decriptif = $lg->descriptif;
			$heure = $lg->heure;
			$euros = $lg->eurosPerdus;
			$status=$lg->status;
			$quiss = $lg->quiss_mpti;

		}
		
		$lg=mysqli_fetch_object($req);
		$dep=$lg->idDep_depositaire;
		$badge=$lg->badge;
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$depositaire=$lg->nomDep;
		$telDep=$lg->telDep;
		$os=$lg->os;
		$remarque=$lg->commentaire;
		$pastilleOrange=$lg->pastilleOrange;
		$pastilleRouge=$lg->pastilleRouge;
		$retardME=$lg->retardME;
		$ligneProd = $lg->ligneProd;
		$deux_huit = $lg->deux_huit;
		$samedi = $lg->samedi;
		
		$date_debut=date('d/m/Y',strtotime($lg->date_debut));
		$date_fin=date('d/m/Y',strtotime($lg->date_fin));
		$heure_debut=date('H:i',strtotime($lg->date_debut));
		$heure_fin=date('H:i',strtotime($lg->date_fin));
		
		$anomalie = false;
		$str="SELECT idEssai from anomalie where idEssai = $idEssai;";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0){
			
			$lg=mysqli_fetch_object($req);
			$anomalie=true;
		}
		
		//Récupération des lignes de produit
		$str = "SELECT idLigne, nomLigne FROM ligneproduit";
		$req_ligne = mysqli_query($bdd, $str);
		$req_ligne2 = mysqli_query($bdd, $str);
		
		//Famille de produit
		$str="SELECT famille_FAMILLE, heure_FAMILLE, modeleFamille_FAMILLE FROM famille_essai WHERE idEssai_ESSAI = $idEssai;";
		$req=mysqli_query($bdd,$str);
		$famille_saisie = false;
		if(mysqli_num_rows($req)!=0){
			
			$lg=mysqli_fetch_object($req);
			$famille = $lg->famille_FAMILLE;
			$heure_famille = $lg->heure_FAMILLE;
			$modeleFamille = $lg->modeleFamille_FAMILLE;
			$famille_saisie=true;
		}
		
		$str_famille = "SELECT idFamille, nomFamille, modeleFamille FROM famille;";
		$req_famille = mysqli_query($bdd,$str_famille);

		//Cause anomalie
		$str="SELECT nomCause FROM cause_anomalie WHERE idEssai_ESSAI = $idEssai;";
		$req=mysqli_query($bdd,$str);
		$cause = false;
		if(mysqli_num_rows($req)!=0){
			
			$lg=mysqli_fetch_object($req);
			$nomCause= $lg->nomCause;
			$cause=true;
		}
		
		$str_cause = "SELECT idCause, nomCause FROM cause;";
		$req_cause = mysqli_query($bdd,$str_cause);

		$str="SELECT m.nomMoyen, m.idMoyen
		FROM essai e, moyen m
		where e.idMoyen_MOYEN=m.idMoyen
		and e.idEssai=$idEssai;";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0)
		{
			$lg=mysqli_fetch_object($req);
			$moyen=$lg->nomMoyen;
			$idMoyenActuel=$lg->idMoyen;
		}
		
		$str = "SELECT nomEmp, prenomEmp FROM utilisateur, employe WHERE idEmp_EMPLOYE = idEmp and `idService_SERVICE`=$idLabo and idEmp != 4 and idEmp != 3 and idEmp != 5 and actif=1;";
		$reqTech=mysqli_query($bdd,$str);
		
		$str = "SELECT nomEmp FROM vibtesterpar WHERE idEssai=$idEssai;";
		$reqTechActuel=mysqli_query($bdd,$str);
		if(mysqli_num_rows($reqTechActuel)>0)
		{
			$lg=mysqli_fetch_object($reqTechActuel);
			$nomTech=$lg->nomEmp;

		}else{
			
			$nomTech="Non renseigné";
		}
		
		//Récupération des moyens
		$str="SELECT idMoyen, nomMoyen from Moyen where idService_SERVICE=$idLabo;";
		$reqEquip=mysqli_query($bdd,$str);

		//Récupération des dépositaires
		$str="SELECT idDep, nomDep, prenomDep FROM depositaire WHERE actif = 1";
		$req_dep = mysqli_query($bdd, $str);
		
		//on recupere les of concernés
		$str="SELECT e.noOF, m.nomModele, m.idModele, e.article FROM equipement_of e, type_modele m
		where noOF in (select noOF_equipement_of from tester where idEssai_Essai=$idEssai)
		and e.idModele_TYPE_MODELE=m.idModele;";
		$req=@mysqli_query($bdd,$str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de des of</strong></div>";
		else{
?>
			<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
			<link rel="stylesheet" href="../bootstrap/css/select2.css">
			<link href="../calendrier/calendrier.css" rel="stylesheet" />
			<script type="text/javascript" src="../bootstrap/dist/js/select2.js"></script>
			<style type="text/css">
			.select2{
				display: block;
				width: 100%;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
			}

			.select2-container--default .select2-selection--single{
				background-color: transparent !important ;
				border:0px !important;
			}

			.select2-container--default .select2-selection--single .select2-selection__rendered{
				line-height: 1.42857143 !important;
			}

			.select2-container .select2-selection--single .select2-selection__rendered{
				padding-left: 0px;
			}

			.select2{
				margin-bottom: 5px;
			}

			</style>
			<div class="container-fluid">
				<div class="page-header">

					<h2>Modification de l'essai n°<?php echo $idEssai; ?></h2>
				</div>
				<form method="post" action="modifEssai.php<?php  if (isset ($_GET["back"])) echo "?back=".$_GET["back"]; ?>">
					<div class="container-fluid theme-showcase" role="main">
						<h4>Informations générales</h4>
						<div class="jumbotron">
							<div class="row">
								<div class="col-md-4">
									<div class="row">
										<div class="col-md-8">
											<input name="nom_aff" title="Nom de l'affaire" type="text" class="form-control" placeholder="Nom de l'affaire" value="<?php echo $affaire; ?>" required  />
										</div>
										<div class="col-md-4">
											<select class="form-control" name="ligneProd" id="ligneProd">
												<option value="-1" selected >Ligne de produit</option>
											<?php
												//Affichage des boutons radio en fonction de la liste des lignes de produit
												while ($lg_ligne = mysqli_fetch_object($req_ligne)){
													
													$ligne = $lg_ligne -> nomLigne;
													echo "<option "; 
													if ($ligneProd == $lg_ligne -> idLigne) echo "selected";
													echo " value='$ligne' >$ligne</option>";
												}
											?>
											</select>
										</div>
									</div>
									<input name="nom_eq" title="Nom de l'équipement" type="text" class="form-control" placeholder="Nom de l'équipement" value="<?php echo $equipement; ?>"  />
									<div class="row">
										<div class="col-md-8"> 
											<select onchange="change_heure()" class="form-control" name="famille" id="famille">
												<option value="Non renseigné" selected >Famille d'équipement</option>
										
												<?php

												if (isset($famille) && isset($modeleFamille))
													echo '<option selected value="'.$modeleFamille."-".$famille.'" >'.$famille." ".$modeleFamille.'</option>';

												while($lgFamille=mysqli_fetch_object($req_famille))
												{
													$nomFam = $lgFamille->nomFamille;
													$mod = $lgFamille->modeleFamille;
													if(!isset($famille) || $nomFam!=$famille)
														echo '<option value="'.$mod."-".$nomFam.'" >'.$nomFam." ".$mod.'</option>';		
												}
												?>		
											</select>
										</div>
										<div class="col-md-4">
											<input name="heure_famille" class="form-control" type="text" value =<?php if (isset($heure_famille)) echo $heure_famille; else echo "0";?> required>
										</div>
									</div>

									<label class="checkbox-inline">
										<input type="checkbox" id="pastilleOrange" value="1" name="pastilleOrange"<?php if ($pastilleOrange ==1) echo 'checked'; ?>><span style="float:left;" class="bar-retard"></span>
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="pastilleRouge" value="2" name="pastilleRouge"<?php if ($pastilleRouge ==1) echo 'checked'; ?>><span style="float:left;" class="bar-non-planifie"></span>
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="retardME" value="3" name="retardME"<?php if ($retardME ==1 && $pastilleOrange!=1) echo 'checked'; ?>><span style="float:left;" class="iconeRetardME">&#x26A0;</span>
									</label>
									
								</div>
								<div class="col-md-4">
									<select class="form-control js-example-basic-single" required name="depositaire">
										<option value="-1">Dépositaire</option>
										<?php
											while($lg_dep = mysqli_fetch_object($req_dep))
											{
												if ($dep == $lg_dep->idDep)

													echo '<option selected value="'.$lg_dep->idDep.'">'.$lg_dep->nomDep.' '.$lg_dep->prenomDep.'</option>';
												else
													echo '<option value="'.$lg_dep->idDep.'">'.$lg_dep->nomDep.' '.$lg_dep->prenomDep.'</option>';
											}
										?>
									</select>
									<input id="tel" name="tel" title="Téléphone" type="text" class="form-control" placeholder="Téléphone" value="<?php echo $telDep; ?>" />
									<input name="badge" title="Badge valise" type="text" class="form-control" placeholder="Badge valise" value="<?php echo $badge; ?>" autofocus />
								</div>
								<div class="col-md-4">					
									<?php

									echo '<select class="form-control" name="tech" id="tech">';
									echo '<option value="Non renseigné" selected >Nom du technicien ME</option>';
									while($lgTech=mysqli_fetch_object($reqTech))
									{
										$nom = $lgTech->nomEmp;
										$prenom = $lgTech->prenomEmp;
										$nom_prenom = $nom." ".$prenom;
										if(isset($nomTech) && $nom_prenom==$nomTech)
											echo '<option selected value="'.$nom." ".$prenom.'" >'.$nom." ".$prenom.'</option>';	
										else
											echo '<option value="'.$nom." ".$prenom.'" >'.$nom." ".$prenom.'</option>';		
									}			
									echo '</select>';
									?>

									<select class="form-control" name="moyen" id="moyen">';
									<option value="-1" selected >Choisir un moyen</option>';
									<?php
									while($lg=mysqli_fetch_object($reqEquip))
									{
										$idMoyen=$lg->idMoyen;
										$nomMoyen=$lg->nomMoyen;
										if(isset($idMoyenActuel) && $idMoyen==$idMoyenActuel)
											echo "<option selected value='$idMoyen' >$nomMoyen</option>";	
										else
											echo "<option value='$idMoyen' >$nomMoyen</option>";	
									}			
									echo '</select>';									
									?>
									<input id="n_os" name="n_os" title="N° d'OS" type="text" class="form-control" placeholder="N° d'OS" value="<?php echo $os; ?>" required />
								</div>						
							</div>
						</div>
					</div>
					<center>
						<input type="submit" class="btn btn-lg btn-success" value="Valider" />
						<?php
						if (isset($_GET["back"])){
						?>
						<input type="button" class="btn btn-lg btn-primary" value="Annuler" onclick="document.location.href='detailsEssai.php?idEssai=<?php echo $idEssai; ?>&back=<?php echo $_GET["back"]; ?>'"/>
						<?php
						}else {
						?>
						<input type="button" class="btn btn-lg btn-primary" value="Annuler" onclick="document.location.href='detailsEssai.php?idEssai=<?php echo $idEssai; ?>'"/>
						<?php
						}
						?>
					</center>
					<div class="container-fluid theme-showcase" role="main">
						<h4>Dates</h4>
						<div class="jumbotron">
							<div class="row">
								<div class="col-md-4">
									<div class="autre-form" >
										<span style="float:left;">Date début: <input  placeholder="01/01/2014" value="<?php echo $date_debut; ?>" type="text" name="dateDebut" id="dateDebut" class="calendrier"  maxlength="10" size="8" required/></span>
										<input id="hDebut" value="<?php echo $heure_debut; ?>"  name="hDebut" style="float:left;width:25%" title="Heure début" type="text" class="form-control" maxlength="5" placeholder="08:00" required />
									</div>
								</div>
								<div class="col-md-4">
									<div class="autre-form">
										<span style="float:left;">Date fin: <input placeholder="01/01/2014" maxlength="10" value="<?php echo $date_fin; ?>" type="text" name="dateFin" id="dateFin" class="calendrier"  size="8" required/></span>
										<input id='hFin' value="<?php echo $heure_fin; ?>" style="float:left;width:25%" name="hFin" title="Heure fin" type="text" class="form-control" maxlength="5" placeholder="18:00" required />
									</div>
								</div>
								<div class="col-md-2" style="padding-top : 10px;">
									<label class="checkbox-inline">
										<input type="checkbox" id="deux_huit" value="1" name="deux_huit" <?php if ($deux_huit == 1) echo " checked"; ?>><span>Deux-huit</span>
									</label>
								</div>
								<div class="col-md-2" style="padding-top : 10px;">
									<label class="checkbox-inline">
										<input type="checkbox" id="samedi" value="1" name="samedi" <?php if ($samedi != 0) echo " checked"; ?>><span>Samedi</span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="container-fluid theme-showcase" role="main">
						<h4 class="sub-header">N°OF concernés</h4>
						<div class="jumbotron" >
							<table class="table table-striped table-tri"  >
								<thead>
									<tr>
										<th >N°OF</th>
										<th >Type modèle</th>
										<th >Article</th>
										<th >Supprimer</th>
									</tr>
								</thead>
								<tbody id="tabOf">
									<?php
									$first=true;
									while($lg=mysqli_fetch_object($req)){
										$id="";
										if($first)//ajoute l'id au premier input (pour la douchette)
										{
											$id="n_of1";
											$first=false;
										}
										$noOf=$lg->noOF;
										$nomModele=$lg->nomModele;
										$idModele=$lg->idModele;
										$article=$lg->article
										?>
										<tr>
											<td><input id='<?php echo $id; ?>' class='form-control' placeholder='N°OF' value='<?php echo $noOf; ?>' type='text' name='n_of[]' title='N°OF' required/></td>
											<td>
												<select class='form-control' name='modele[]'>
													<option value='-1' disabled>Type Modèle</option>
													<option <?php if($idModele== 5) echo "selected" ?> value='5' >EQM</option>
													<option <?php if($idModele== 3) echo "selected" ?> value='3' >PFM</option>
													<option <?php if($idModele== 4) echo "selected" ?> value='4' >FM</option>
													<option <?php if($idModele== 1) echo "selected" ?> value='1' >EM</option>
													<option <?php if($idModele== 2) echo "selected" ?> value='2' >QM</option>
													<option <?php if($idModele== 6) echo "selected" ?> value='6' >EBT</option>
													<option <?php if($idModele== 7) echo "selected" ?> value='7' >EXT</option>
												</select>
											</td>
											<td><input  class='form-control' placeholder='Article' value='<?php echo $article; ?>' type='text' name='article[]' title='Article'/></td>
											<td><img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' /></td>
										</tr>
										<?php
									}							
									?>	
								</tbody>								
							</table>
							<center>
								<input type="button" class="btn  btn-primary" value="Ajouter un OF" onclick="ajout_of()"/>		
							</center>
						</div>
					</div>
					<div class="container-fluid theme-showcase" role="main">

						<h4>Anomalie <input type="checkbox" id="anomalie" value="1" name="anomalie"<?php if ($anomalie == true) echo 'checked'; ?>></h4>

						<div <?php if ($anomalie == true) echo 'style="display:block"'; else echo 'style="display:none"'; ?> id="anom" class="jumbotron">
							<div class="row" style="margin-bottom:10px;">
								<div class="col-md-2" >
								<div class="autre-form">
									<span style="float:left;">Traçabilité ok:&nbsp;
										<input type="checkbox" id="statusEcart" value="1" name="statusEcart" <?php if ($status == 1) echo 'checked'; ?>>&nbsp;
									</span>
								</div>
								</div>
							
							</div>
							
							
							
							<div class="row">
								<div class="col-md-2">
									<span>Secteur d'origine: </span>
								</div>
								
								<?php
								
								while ($lg_ligne = mysqli_fetch_object($req_ligne2)){
									
									echo '<div class="col-md-1">
									<div class="btnRadio"><input name="secteur" type="radio" value='.$lg_ligne->nomLigne.' ';
									if ($autre == $lg_ligne->nomLigne) echo 'checked';
									if ($anomalie == false) echo 'checked';
									echo '> '.$lg_ligne->nomLigne.'</div></div>';
								}
								
								?>
								
							
							</div>
							<div class="row vertical-align" style="margin-top:10px; margin-bottom:10px;">
								<div class="col-md-2">
									<div class="btnRadio" >
										<input name="quiss/mpti" type="radio" value="QUISS" <?php if ($quiss == 'QUISS') echo 'checked' ?> <?php if ($quiss == '') echo 'checked' ?>> QUISS
									</div>
								</div>
								<div class="col-md-2">
									<div class="btnRadio" >
										<input name="quiss/mpti" type="radio" value="MPTI/NC" <?php if ($quiss == 'MPTI/NC') echo 'checked' ?>> MPTI / NC
									</div>
								</div>

								<div class="col-md-4">
									<div class="autre-form" >
										Heure(s) perdue(s): <input style="padding:0" placeholder="Heure(s) perdue(s)" value="<?php echo $heure; ?>" type="text" name="heure" size=15 /> heure(s)
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="autre-form" >
										Euro(s) perdu(s): <input style="padding:0" name="euros" title="euros" type="text" placeholder="KEuro(s) perdu(s)" size=14 value="<?php echo $euros; ?>"/> Keuro(s)
									</div>
								</div>

							</div>
							<div class="row">
								<div class="col-md-4">
									<select class="form-control" name="cause" id="cause">
										<option value="Non renseigné" selected >Cause d'anomalie</option>
								
										<?php
										if (isset($nomCause))
											echo '<option selected value="'.$nomCause.'" >'.$nomCause.'</option>';	
										while($lgCause=mysqli_fetch_object($req_cause))
										{
											$cas = $lgCause->nomCause;
											if(!isset($nomCause) || $cas!=$nomCause)
												echo '<option value="'.$cas.'" >'.$cas.'</option>';	
													
										}
										?>		
									</select>
								</div>
							</div>

							<textarea name="descriptif" title="Descriptif" class="form-control" placeholder="Descriptif"><?php echo $decriptif;?></textarea>
							
						
						</div>
					</div>
					
					<div class="container-fluid theme-showcase" role="main">
						<h4 class="sub-header">Remarques</h4>
						<div class="jumbotron">
							<textarea name="remarque" title="Remarques" class="form-control" placeholder="Remarques"><?php echo $remarque;?></textarea>
						</div>
					</div>
					<input type="hidden" value="<?php echo $idEssai; ?>" name="idEssai"/>
					
				</form>
			</div><!-- /.container -->
			<script src="../jquery-ui/js/jquery-ui.min.js"></script>
			<script src="../js/creer_modifierEssai.js"></script>
			<script src="../calendrier/calendrier.js"></script>
			
					
<?php		
		}
	}
}

require('bottom.php');
?>