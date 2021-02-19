<?php 
session_start();

if($_SESSION['infoUser']['categUser']==3 || $_SESSION['infoUser']['categUser']==6 || $_SESSION['infoUser']['categUser']==1)
{
	
	//Fonction php qui pour un essai $p_idE renvoie un tableau avec les 19 info ci-dessus
	function infoSuiviEssai($p_idE,$bdd){
		$tab=array();
		
			$str1="
			select s.nomService as labo, e.affaire, e.equipement, e.os, d.nomDep
			from essai e, service s, depositaire d
			where e.idEssai=$p_idE
			and e.idDep_depositaire=d.idDep
			and e.idService_SERVICE=s.idService
			;";
			
			$req1=mysqli_query($bdd,$str1);
			
			echo mysqli_error($bdd);
			while($lg1=mysqli_fetch_object($req1)){
			
				$tab[0]="";
				$tab[1]="";
				$tab[2]="$p_idE";
				$tab[3]=$lg1->labo;
				$tab[4]=$lg1->nomDep;
				$tab[5]=$lg1->affaire;
				$tab[6]=$lg1->equipement;
				$tab[7]=$lg1->os;
			}
			
			$str2="
			select e.nomEtat
			from ETAT e, etatEssai ep
			where ep.idEssai_ESSAI=$p_idE
			and ep.idEtat_ETAT=e.idEtat
			and ep.dateEtat=(select max(dateEtat)
							from etatEssai
							where idEssai_ESSAI=$p_idE
							)		
			;";
			$req2=mysqli_query($bdd,$str2);
			
			
			while($lg2=mysqli_fetch_object($req2)){$tab[8]=$lg2->nomEtat;}
			
			$tab[9]=infoDate($p_idE,22,$bdd);
			$tab[10]=infoDate($p_idE,23,$bdd);
			$tab[11]=infoDate($p_idE,24,$bdd);
			$tab[12]=infoDate($p_idE,25,$bdd);		

			
						
		return $tab;		
	}
			
	function infoDate($p_id,$p_etat,$bdd){
		// retourne la date de l etat $p_etat de la l essai $p_id
		$str="
		select max(dateEtat) as max
		from etatEssai
		where idEssai_ESSAI=$p_id and idEtat_ETAT=$p_etat
		;";
		
		$req=mysqli_query($bdd,$str);
		
		
		while($lg=mysqli_fetch_object($req)){$d=$lg->max;}

		return $d;
	}

	
	
	
	// l utilisateur a la droit d etre sur cette page
	require('../conf/connexion_param.php'); 	//connexion a la bdd
	require('../fonction.php'); 
	$idLabo=$_SESSION['infoUser']['idService'];

	//Condition d'export
	if ($idLabo==5){
		// suivi MEE, donc des 3 labo
		$condition="in (1,2,3)";
	}else{

		$condition="=$idLabo";
	}


	//on recupere toutes les essais (jusqua 500 jours)
	$str="
	select e.idEssai
	from ESSAI e, etatEssai et
	where e.idService_SERVICE $condition
	and et.idEtat_ETAT=22
	and et.idEssai_ESSAI=e.idEssai 
	and datediff(now(),et.dateEtat)<500
	order by et.dateEtat
	;";

	$reqSuiviEssai=mysqli_query($bdd,$str);
	

	//titre des colonnes de l'export
	$titre=array();
	$titre[0]="OF";
	$titre[1]="Modele";
	$titre[2]="idEssai";
	$titre[3]="Laboratoire";
	$titre[4]="Depositaire";
	$titre[5]="Affaire";
	$titre[6]="Equipement";
	$titre[7]="OS";
	$titre[8]="DernierEtat";
	$titre[9]="ReceptionEq";
	$titre[10]="DebutReelEssai";
	$titre[11]="FinReelEssai";
	$titre[12]="RetourEq";




	$suiviEssai=array();
	$suiviEssai[0]=$titre;//titre

	$i=0;// i eme of

	while($lg=mysqli_fetch_object($reqSuiviEssai)){
		//pour chaque essai
		
		$id=$lg->idEssai;//id essai
		
		// on recupere les 19 info propres a l essai
		$infoEssai=infoSuiviEssai($id,$bdd);
		
		// on recupere les of de l essai
		$str="select noOF_EQUIPEMENT_OF as nof from tester where idEssai_ESSAI=$id;";
		$reqOF=mysqli_query($bdd,$str);
		
		
		while($lg1=mysqli_fetch_object($reqOF)){
			//pour chaque of on met dans le tab les valeurs de l essai, et on ecrase of en mettant la bonne valeur
			
			$this_OF=$lg1->nof;
			$i++;
			
			$infoEssai[0]=$this_OF;
			
			$str="
			select tp.nomModele
			from TYPE_MODELE tp, EQUIPEMENT_OF eq
			where tp.idModele=eq.idModele_TYPE_MODELE
			and eq.noof='$this_OF'
			;";
			$req=mysqli_query($bdd,$str);
			
			
			while($lg=mysqli_fetch_object($req)){$infoEssai[1]=$lg->nomModele;}

			//modele
			$suiviEssai[$i]=$infoEssai;
		}
		
	}

	exportCSV($suiviEssai);
}
else // acces interdit
	echo "<script>alert(\"Accès non autorisé.\");document.location.href=\"../index.php\";</script>";	
?>