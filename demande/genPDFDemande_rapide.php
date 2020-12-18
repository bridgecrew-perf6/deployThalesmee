<?php
require('../fpdf/fpdf.php');//la version de fpdf a été modifié pour accepter l'utf-8
/*
a) méthode _escape
ajouter utf8_decode() autour de la variable $s

b) méthode Cell
supprimer la création de la variable $txt2
transformer $txt2 en $this->_escape($txt) dans le sprintf qui suit
*/

// Ce fichier genere un fichier pdf de la demande en cours

session_start(); 
require('../conf/connexion_param.php'); 	//Fichier contenant les parametres de la base de donnees 


if (isset($_SESSION['infoUser'])){
	// l utilisateur a bien ete identifie
	
	

	if (isset($_GET['idDP'])){
		$idDP=$_GET['idDP'];
		//Recuperation des info generales
		//Recuperation des info generales
		$str="select d.affaire, d.equipement, d.plateforme, d.delai, d.remarque, d.OS, d.dateDemandeDP_redigerDP, e.nomEmp
		from DEMANDE_PROCEDURE d, employe e
		where idDP=$idDP
		and d.idEmp_EMPLOYE=e.idEmp; ";
		$req=mysqli_query($bdd,$str);
		$res=mysqli_fetch_object($req);
		
		if (mysqli_num_rows($req)==1){
	
			$affaire=$res->affaire;
			$equipement=$res->equipement;
			$plateforme=$res->plateforme;
			$delais=$res->delai;
			//on passe au format français
			$delais=date('d/m/Y',strtotime($delais));
			$remarque=html_entity_decode(htmlspecialchars_decode($res->remarque));
			$OS=$res->OS;
			$dateDemande=$res->dateDemandeDP_redigerDP;
			//on passe au format français
			if($dateDemande!=null)
				$dateDemande=date('d/m/Y',strtotime($dateDemande));
			
			//variables qui serviront à tester si la procédure à été choisi pour la demande de procedure (permet un pdf plus dynamique, on n'affiche que le nécessaire ex: si pas de proc EMC -> pas de colonne EMC)
			$isEMC=false; $isVTH=false; $isVIB=false;

			
			$nomDem=$res->nomEmp;
			
			//Recuperation des references employes
			$str="select f.nomFonc, e.nomEmp from referencerEmp r, EMPLOYE e, fonctionemp f
			where r.idDP_DEMANDE_PROCEDURE=$idDP 
			and r.idEmp_EMPLOYE=e.idEmp
			and r.idFonc_fonctionemp=f.idFonc;";
			$reqEmp=mysqli_query($bdd,$str);
			

			//Recuperation des laboratoires concernes par la DP
			$str="select b.nomService from PROCEDURES a, SERVICE b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idService=a.idService_SERVICE;";
			$reqLabo=mysqli_query($bdd,$str);
			
			//Recuperation des modeles a tester
			$str="select a.* ,b.nomModele as nom from vouloirTester a, TYPE_MODELE b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idModele=a.idModele_TYPE_MODELE;";
			$reqML=mysqli_query($bdd,$str);
			
			
			//Recuperation des articles
			$str="select a.comTypeArt as com, b.* from concernerArt a, EQUIPEMENT_ART b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.noArticle=a.noArticle_EQUIPEMENT_ART;";
			$reqArt=mysqli_query($bdd,$str);
			
			//Recup des remarques par labo
			$str="select remarque_labo from procedures where idDP_DEMANDE_PROCEDURE=$idDP;";
			$reqRemLavbo=mysqli_query($bdd,$str);
				
			//generation du pdf
			
			//partie gauche
			$pdf = new FPDF();
			$pdf->AddPage();
			$pdf->SetFillColor(128,128,128);
			
			//titre
			$pdf->SetFont('Arial','B',16);
			$pdf->Cell(60,5,'Recapitulatif demande: '.$idDP,0,1,'C');
			
			
			//infos générales
			$pdf->SetLeftMargin(5);
			$pdf->SetFont('Arial','B',12);
			$pdf->Ln(10);
			$pdf->Cell(50,0,'Informations générales:');
			$pdf->Ln(8);
			$yDebut=$pdf->getY();
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(35,0,'Affaire');
			$pdf->Cell(90,0,$affaire);
			$pdf->Ln(5);
			$pdf->Cell(35,5,'Equipement');
			$pdf->MultiCell(70,5,$equipement);
			$pdf->Ln(5);
			$pdf->Cell(35,0,'Plateforme');
			$pdf->Cell(70,0,$plateforme);
			$pdf->Ln(5);
			$pdf->Cell(35,0,'OS');
			$pdf->Cell(70,0,$OS);
			$pdf->Ln(5);
			$pdf->Cell(35,0,'Date Demande');
			$pdf->Cell(70,0,$dateDemande);
			$pdf->Ln(5);
			$pdf->Cell(35,0,'Date Besoin');
			$pdf->Cell(70,0,$delais);
			
			//Modèles concernés			
			$pdf->Ln(20);
			$yMod=$pdf->getY();	
			$pdf->Cell(35,0,'Modèles concernés:');
			$pdf->Ln(5);
			$pdf->Cell(17,5,'Modèle',1,0,'C',true);
			$pdf->Cell(17,5,'Nombre',1,0,'C',true);
			$pdf->Cell(17,5,'Max',1,0,'C',true);
			$pdf->Cell(17,5,'Min',1,0,'C',true);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqML)){ 
				$pdf->Cell(17,5,$lg->nom,1,0,'C');
				$pdf->Cell(17,5,$lg->nbEquipATester,1,0,'C');
				$pdf->Cell(17,5,$lg->nbMinEquipParTest,1,0,'C');
				$pdf->Cell(17,5,$lg->nbMaxEquipParTest,1,0,'C');
				$pdf->Ln(5);
			}
			
			//Articles
			$num=0;
			$tab3=array();
			
			$pdf->Ln(20);
			$pdf->Cell(35,0,'Articles concernés:');
			$pdf->Ln(5);
			$pdf->Cell(10,5,'N°',1,0,'C',true);
			$pdf->Cell(30,5,'No Article',1,0,'C',true);
			$pdf->Cell(70,5,'Type article',1,0,'C',true);
			$pdf->Cell(50,5,'Désignation 3IT',1,0,'C',true);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqArt)){ 
				$tab3[1][$num]=$lg->noArticle;
				$num++;
				$pdf->Cell(10,5,$num,1,0,'C');
				$pdf->Cell(30,5,$lg->noArticle,1,0,'C');
				$pdf->Cell(70,5,$lg->com,1,0,'C');
				$pdf->Cell(50,5,$lg->designation_3IT,1,1,'C');
			}
			$nbArt=$num;

			//partie droite
			//essai
			$pdf->setY($yDebut);
			$nomTot="Essai: ";
			while($lg=mysqli_fetch_object($reqLabo)){ 
				$nom=$lg->nomService;
				$nomTot.=$nom;
			}
			$pdf->SetLeftMargin(120);
			$pdf->Cell(20,0,$nomTot);
			$pdf->Ln(5);
			$pdf->Cell(30,0,'Demandeur');
			$pdf->Cell(90,0,$nomDem);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqEmp)){ 
				$pdf->Cell(20,0,$lg->nomFonc);
				$pdf->Cell(20,0,$nomEmp=$lg->nomEmp);
				$pdf->Ln(5);
			}
			
			//ajout page

			$pdf->AddPage();
			$pdf->SetLeftMargin(5);
			//ici on recupere les type de procedure crée, 1 EMC 2 VIB 3 VTH inversé 2-3 pour l'affichage (afin que le tableau de recap n'affiche que le nécessaire)
			$str="select idService_SERVICE from procedures where idDP_DEMANDE_PROCEDURE=$idDP;";
			$reqTypeProc=mysqli_query($bdd,$str);
			
			while($lg=mysqli_fetch_object($reqTypeProc)){
				$type=$lg->idService_SERVICE;
				if($type==1)
					$isEMC=true;
				elseif($type==2)
					$isVIB=true;
				elseif($type==3)
					$isVTH=true;
			}


			//Recuperation des infos concernant les articles
			for($i=0; $i<$num;$i++)
			{
				$str="select d.reference, d.issue, d.rev , d.idTypeDoc_TYPE_DOC
				from referencerDocArt r, document_3IT d, type_doc t
				where r.noArticle_EQUIPEMENT_ART=".$tab3[1][$i]."
				and r.idDP_DEMANDE_PROCEDURE=$idDP
				and r.idDoc_DOCUMENT_3IT=d.idDoc
				and d.idTypeDoc_TYPE_DOC=t.idTypeDoc
				and t.categorie='PROCEDURE'
				order by d.idTypeDoc_TYPE_DOC;";
				$reqArtInfo=mysqli_query($bdd,$str);
				
				
				while($lg=mysqli_fetch_object($reqArtInfo)){
					
					if($isEMC && $lg->idTypeDoc_TYPE_DOC ==31)
						$cpt=3;
					elseif($isVTH && $lg->idTypeDoc_TYPE_DOC ==33)
						$cpt=6;
					elseif($isVIB && $lg->idTypeDoc_TYPE_DOC ==32)
						$cpt=9;
						
						
					$tab3[$cpt][$i]=$lg->reference;
					$tab3[($cpt+1)][$i]=$lg->issue;
					$tab3[($cpt+2)][$i]=$lg->rev;

				}
			}
			
			
			$remEMC=""; $remVTH=""; $remVIB="";
			//recupération des remarques d'évolution EMC VTH ET VIB
			$str="select distinct r.commentaire, d.idtypedoc_type_doc from referencerDocArt r, document_3it d 
			where idDP_DEMANDE_PROCEDURE=$idDP
			and (d.idtypedoc_type_doc=31 or d.idtypedoc_type_doc=32 or d.idtypedoc_type_doc=33)
			and r.idDoc_DOCUMENT_3IT=d.iddoc;";
			$reqComProc=mysqli_query($bdd,$str);
			
			while($lg=mysqli_fetch_object($reqComProc)){
				if($lg->commentaire!="")
				{
					if($isEMC && $lg->idtypedoc_type_doc ==31)
						$remEMC=explode('$$',$lg->commentaire);
					elseif($isVTH && $lg->idtypedoc_type_doc ==32)
						$remVTH=explode('$$',$lg->commentaire);
					elseif($isVIB && $lg->idtypedoc_type_doc ==33)
						$remVIB=explode('$$',$lg->commentaire);
				}
			}
			
			$articles=$tab3;

			$pdf->Ln(25);
			$pdf->Cell(35,0,'Procédures:');
			$pdf->Ln(5);
			$pdf->Cell(10,10,'N°',1,0,'C',true);
			
			if($isEMC)
				$pdf->Cell(45,5,'Procédure EMC',1,0,'C',true);
			if($isVTH)
				$pdf->Cell(45,5,' Procédure VTH',1,0,'C',true);
			if($isVIB)
				$pdf->Cell(45,5,' Procédure VIB',1,0,'C',true);
			$pdf->Ln(5);

			$pdf->Cell(10,0);
			if($isEMC){
				$pdf->Cell(26,5,'Ref',1,0,'C',true);
				$pdf->Cell(11,5,'Issue',1,0,'C',true);
				$pdf->Cell(8,5,'Rev',1,0,'C',true);
			}
			if($isVTH){
				$pdf->Cell(26,5,'Ref',1,0,'C',true);
				$pdf->Cell(11,5,'Issue',1,0,'C',true);
				$pdf->Cell(8,5,'Rev',1,0,'C',true);
			}
			if($isVIB){
				$pdf->Cell(26,5,'Ref',1,0,'C',true);
				$pdf->Cell(11,5,'Issue',1,0,'C',true);
				$pdf->Cell(8,5,'Rev',1,0,'C',true);
			}
			$pdf->Ln(5);
			
			for ($i=0;$i<$nbArt;$i++){
				$pdf->Cell(10,5,$i+1,1,0,'C');
				
				if($isEMC){
					$pdf->Cell(26,5,$articles[3][$i],1,0,'C');
					$pdf->Cell(11,5,$articles[4][$i],1,0,'C');
					$pdf->Cell(8,5,$articles[5][$i],1,0,'C');
				}
				if($isVTH){
					$pdf->Cell(26,5,$articles[6][$i],1,0,'C');
					$pdf->Cell(11,5,$articles[7][$i],1,0,'C');
					$pdf->Cell(8,5,$articles[8][$i],1,0,'C');
				}
				if($isVIB){
					$pdf->Cell(26,5,$articles[9][$i],1,0,'C');
					$pdf->Cell(11,5,$articles[10][$i],1,0,'C');
					$pdf->Cell(8,5,$articles[11][$i],1,0,'C');
				}
				$pdf->Ln(5);
			}
			
			
			
			//remarques si besoin
			if ($remarque!=""){
				$pdf->Ln(10);
				$pdf->SetTextColor(150,0,0);
				$pdf->MultiCell(0,5,"Remarques: ".$remarque);
			}
			
			if($isEMC && $remEMC!="")
			{
				$pdf->Ln(10);
				$pdf->SetTextColor(150,0,0);
				$pdf->Cell(35,5,"Remarques EMC:");
				$pdf->SetTextColor(0,0,0);
				for($i=0;$i<count($remEMC);$i++)
				{
					$pdf->MultiCell(0,5,$remEMC[$i]);
				}
			}
			if($isVTH && $remVTH!="")
			{
				$pdf->Ln(10);
				$pdf->SetTextColor(150,0,0);
				$pdf->Cell(35,0,"Remarques VTH:");
				$pdf->SetTextColor(0,0,0);
				for($i=0;$i<count($remVTH);$i++)
				{
					$pdf->MultiCell(0,5,$remVTH[$i]);
				}
			}
			if($isVIB && $remVIB!="")
			{
				$pdf->Ln(10);
				$pdf->SetTextColor(150,0,0);
				$pdf->Cell(35,0,"Remarques VIB:");
				$pdf->SetTextColor(0,0,0);
				for($i=0;$i<count($remVIB);$i++)
				{
					$pdf->MultiCell(0,5,$remVIB[$i]);
				}
			}
			
			$pdf->SetTextColor(0,0,200);
			while($lg=mysqli_fetch_object($reqRemLavbo)){ 
				$rem_lab=$lg->remarque_labo;
				if($rem_lab!="")
				{
					$pdf->Ln(10);
					$pdf->MultiCell(0,5,$rem_lab);
				}
			}

			//envoi du pdf au navigateur
			
			
			$pdf->Output();

	
		}else{
			//etape precedente non effectuee avec succes
			echo "<script>alert(\"Erreur etape precedente.\");</script>";
		}
	}else{
		//numero demande non transmis			
		echo "<script>alert(\"Erreur transmission numero demande.\");</script>";
	} 
}else{
	// l utilisateur n 'est pas reconnu
	echo "<script>alert(\"Erreur d'authentification.\");</script>";
}
?>