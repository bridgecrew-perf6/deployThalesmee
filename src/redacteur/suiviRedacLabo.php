<?php 
session_start();


if(isset($_SESSION["infoUser"]) && ($_SESSION['infoUser']['categUser']==3 || $_SESSION['infoUser']['categUser']==4 || $_SESSION['infoUser']['categUser']==6 || $_SESSION['infoUser']['categUser']==1))
{
	// l utilisateur a bien ete identifie et l utilisateur a la droit d etre sur cette page
	require('../conf/connexion_param.php'); 	// connexion a la base
	require('../fonction.php'); 	
	$idLabo=$_SESSION['infoUser']['idService'];
	
	//Condition d'export
	if ($idLabo==5){
		// suivi MEE, donc des 3 labo
		$condition="in (1,2,3)";
	}else{
		// suivi Resp Labo du user
		$condition="= $idLabo";
	}

	//on recupere toutes les procedures concernees par une DP valide
	$str="
	select p.idProc 
	from PROCEDURES p, DEMANDE_PROCEDURE d
	where p.idDP_DEMANDE_PROCEDURE=d.idDP
	and d.validiteDP=4
	and idService_SERVICE $condition
	order by d.delai desc
	;";
	
	$reqSuiviProc=mysqli_query($bdd,$str);
	
	//titre des colonnes de l'export
	$titre=array();
	$titre[0]="idProc";
	$titre[1]="Laboratoire";
	$titre[2]="Redacteur";
	$titre[3]="idDP";
	$titre[4]="Demandeur";
	$titre[5]="Affaire";
	$titre[6]="Equipement";
	$titre[7]="OS";
	$titre[8]="DateBesoin";
	$titre[9]="DateDerniereModifDemande";
	$titre[10]="DernierEtat";
	$titre[11]="DateDemande";
	$titre[12]="DateAffectation";
	$titre[13]="DateMiseRelecture";
	$titre[14]="DateMiseSignature";
	$titre[15]="DateValidation";


	
	$suiviLabo=array();
	$suiviLabo[0]=$titre;//titre
	
	$i=0;// i eme procedure
	
	while($lg=mysqli_fetch_object($reqSuiviProc)){
		//on recupere les info de la procedure dans un tableau
		
		$id=$lg->idProc;//id procedure
		$i++;
		$suiviLabo[$i]=infoSuiviProc($id,$bdd);	

	}
	
	//$_SESSION['tabRes']=$suiviLabo;
	//on redirige vers l'export csv
	//echo "<script>document.location.href=\"exportResCSV.php\";</script>";
	$var=exportCSV($suiviLabo);
}
else // acces interdit
	echo "<script>alert(\"Accès non autorisé.\");document.location.href=\"../index.php\";</script>";		

	
		
//Fonction php qui pour une procedure $p_idProc renvoie un tableau avec les 14 info ci-dessus
function infoSuiviProc($p_idProc,$bdd){
	$tab=array();
	
		$str1="
		select d.affaire, d.equipement, d.OS, d.delai, d.dateDemandeDP_redigerDP , d.idDP, e1.nomEmp as nomD,e1.prenomEmp as prenomD,e2.nomEmp as nomR,e2.prenomEmp as prenomR,s.nomService
		from (DEMANDE_PROCEDURE d, PROCEDURES p, EMPLOYE e1, SERVICE s ) left join EMPLOYE e2 on e2.idEmp=p.idEmp_EMPLOYE
		where p.idProc=$p_idProc
		and p.idDP_DEMANDE_PROCEDURE=d.idDP
		and p.idService_SERVICE=s.idService
		and d.idEmp_EMPLOYE=e1.idEmp
		;";
		
		$req1=mysqli_query($bdd,$str1);
		
		while($lg1=mysqli_fetch_object($req1)){
		
			$tab[0]=$p_idProc;//idProc
			$tab[1]=$lg1->nomService;//laboratoire
			$nr=$lg1->nomR;
			$pr=$lg1->prenomR;
			$tab[2]="$nr $pr";//redacteur
		
			$tab[3]=$lg1->idDP;//idDP
			
			$nd=$lg1->nomD;
			$pd=$lg1->prenomD;
			$tab[4]="$nd $pd";//demandeur
			
			$tab[5]=$lg1->affaire;//affaire
			$tab[6]=$lg1->equipement;//equipement
			$tab[7]=$lg1->OS;//OS
			$tab[8]=$lg1->delai;//delais
			$tab[9]=$lg1->dateDemandeDP_redigerDP;//date demande de la derniere modification
		}
		
		$str2="
		select e.nomEtat
		from ETAT e, etatProc ep
		where ep.idProc_PROCEDURES=$p_idProc
		and ep.idEtat_ETAT=e.idEtat
		and ep.dateEtat=(select max(dateEtat)
						from etatProc
						where idProc_PROCEDURES=$p_idProc
						)		
		;";
		$req2=mysqli_query($bdd,$str2);
		
		while($lg2=mysqli_fetch_object($req2)){
			
			$tab[10]=$lg2->nomEtat;
		
		}
		
		$tab[11]=infoDate($p_idProc,12,$bdd);
		$tab[12]=infoDate($p_idProc,13,$bdd);
		$tab[13]=infoDate($p_idProc,15,$bdd);
		$tab[14]=infoDate($p_idProc,16,$bdd);
		$tab[15]=infoDate($p_idProc,17,$bdd);		
		
	return $tab;
}
		
function infoDate($p_id,$p_etat,$bdd){
	// retourne la date de l etat $p_etat de la procedure $p_id
	$str="
	select max(dateEtat) as max
	from etatProc
	where idProc_PROCEDURES=$p_id and idEtat_ETAT=$p_etat
	;";
	
	$req=mysqli_query($bdd,$str);
	
	$d=mysqli_fetch_object($req)->max;

	return $d;
}

?>