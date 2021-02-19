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
			
			$nomDem=$res->nomEmp;
			
			//Recuperation des references employes
			$str="select f.nomFonc, e.nomEmp from referencerEmp r, EMPLOYE e, fonctionemp f
			where r.idDP_DEMANDE_PROCEDURE=$idDP 
			and r.idEmp_EMPLOYE=e.idEmp
			and r.idFonc_fonctionemp=f.idFonc;";
			$reqEmp=mysqli_query($bdd,$str);
			

			//Recuperation des laboratoires concernes par la DP + commentaires de ceux ci
			$str="select b.nomService from PROCEDURES a, SERVICE b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idService=a.idService_SERVICE;";
			$reqLabo=mysqli_query($bdd,$str);
			
			//Recuperation des modeles a tester
			$str="select a.nbEquipATester, a.nbMinEquipParTest, a.nbMaxEquipParTest ,b.nomModele as nom from vouloirTester a, TYPE_MODELE b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idModele=a.idModele_TYPE_MODELE;";
			$reqML=mysqli_query($bdd,$str);
			
			
			//Recuperation des articles
			$str="select a.comTypeArt as com, b.noArticle, b.designation_3IT from concernerArt a, EQUIPEMENT_ART b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.noArticle=a.noArticle_EQUIPEMENT_ART;";
			$reqArt=mysqli_query($bdd,$str);
			

			//Recuperation des documents 3IT
			$str="select b.reference, b.issue, b.rev, b.type_, c.nom from referencerDoc a,  DOCUMENT_3IT b, TYPE_DOC c where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idDoc=a.idDoc_DOCUMENT_3IT and c.idTypeDoc=b.idTypeDoc_TYPE_DOC;";
			$reqDoc=mysqli_query($bdd,$str);
				
			
			//Recuperation des documents 3IT avec match article Interface Elec
			$str="select a.noArticle_EQUIPEMENT_ART as num, b.reference, b.issue, b.rev, b.type_, c.comTypeArt as com from referencerDocArt a,  DOCUMENT_3IT b , concernerArt c where a.idDP_DEMANDE_PROCEDURE=$idDP and a.noArticle_EQUIPEMENT_ART=c.noArticle_EQUIPEMENT_ART and c.idDP_DEMANDE_PROCEDURE=a.idDP_DEMANDE_PROCEDURE and b.idDoc=a.idDoc_DOCUMENT_3IT and b.idTypeDoc_TYPE_DOC=16;";
			$reqDocArtIE=mysqli_query($bdd,$str);
				
			
			//Recuperation des documents 3IT avec match article Interface Meca
			$str="select a.noArticle_EQUIPEMENT_ART as num, b.reference, b.issue, b.rev, b.type_, c.comTypeArt as com from referencerDocArt a,  DOCUMENT_3IT b , concernerArt c where a.idDP_DEMANDE_PROCEDURE=$idDP and a.noArticle_EQUIPEMENT_ART=c.noArticle_EQUIPEMENT_ART and c.idDP_DEMANDE_PROCEDURE=a.idDP_DEMANDE_PROCEDURE and b.idDoc=a.idDoc_DOCUMENT_3IT and b.idTypeDoc_TYPE_DOC=17;";
			$reqDocArtIM=mysqli_query($bdd,$str);
				
			
			//Recuperation des documents Clients
			$str="select a.comTypeArt as com, b.idSpec, b.nomFichier, b.reference, b.issue, b.rev, c.nom from fournirSpec a,  SPEC_CLIENT b, TYPE_DOC c where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idSpec=a.idSpec_SPEC_CLIENT and c.idTypeDoc=b.idTypeDoc_TYPE_DOC;";
			$reqDocClient=mysqli_query($bdd,$str);
			
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
			$pdf->Cell(17,5,'Min',1,0,'C',true);
			$pdf->Cell(17,5,'Max',1,0,'C',true);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqML)){ 
				$pdf->Cell(17,5,$lg->nom,1,0,'C');
				$pdf->Cell(17,5,$lg->nbEquipATester,1,0,'C');
				$pdf->Cell(17,5,$lg->nbMinEquipParTest,1,0,'C');
				$pdf->Cell(17,5,$lg->nbMaxEquipParTest,1,0,'C');
				$pdf->Ln(5);
			}
			//Articles
			$pdf->Ln(10);
			$pdf->Cell(35,0,'Articles concernés:');
			$pdf->Ln(5);
			$pdf->Cell(35,5,'No Article',1,0,'C',true);
			$pdf->Cell(70,5,'Type article',1,0,'C',true);
			$pdf->Cell(50,5,'Désignation 3IT',1,0,'C',true);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqArt)){ 
				$pdf->Cell(35,5,$lg->noArticle,1,0,'C');
				$pdf->Cell(70,5,$lg->com,1,0,'C');
				$pdf->Cell(50,5,$lg->designation_3IT,1,1,'C');
			}
			
			//Documents 3IT :	
			$pdf->Ln(10);
			$yDoc=$pdf->getY();
			$pdf->Cell(35,0,'Documents 3IT:');
			$pdf->Ln(5);
			$pdf->Cell(50,5,'Nom',1,0,'C',true);
			$pdf->Cell(100,5,'Référence',1,0,'C',true);
			$pdf->Cell(14,5,'Type',1,0,'C',true);
			$pdf->Cell(14,5,'Issue',1,0,'C',true);
			$pdf->Cell(18,5,'Rév',1,0,'C',true);
			$pdf->Ln(5);
			
			while($lg=mysqli_fetch_object($reqDoc)){ 	
				$pdf->Cell(50,5,$lg->nom,1,'L');
				$pdf->Cell(100,5,$lg->reference,1,0,'C');
				$pdf->Cell(14,5,$lg->type_,1,0,'C');	
				$pdf->Cell(14,5,$lg->issue,1,0,'C');		
				$pdf->Cell(18,5,$lg->rev,1,1,'C');			
			}
			//partie droite
			//essai
			$pdf->setY($yDebut);
			$nomTot="Essai : ";
			while($lg=mysqli_fetch_object($reqLabo)){ 
				$nom=$lg->nomService;
				$nomTot.=" ".$nom;
			}
			$pdf->SetLeftMargin(120);
			$pdf->Cell(20,0,$nomTot);
			$pdf->Ln(5);
			$pdf->Cell(30,0,'Demandeur');
			$pdf->Cell(90,0,$nomDem);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqEmp)){ 
				$pdf->Cell(30,0,$lg->nomFonc);
				$pdf->Cell(90,0,$nomEmp=$lg->nomEmp);
				$pdf->Ln(5);
			}
			
			
			//page suivante (ajout d'une page)
			$pdf->AddPage();
			$pdf->SetLeftMargin(5);
			
			//spec client
			$pdf->Ln(10);
			$pdf->Cell(35,0,'Documents Client:');
			$pdf->Ln(5);
			$pdf->Cell(60,5,'Type',1,0,'C',true);
			$pdf->Cell(70,5,'Référence',1,0,'C',true);
			$pdf->Cell(15,5,'Issue',1,0,'C',true); 
			$pdf->Cell(10,5,'Rev',1,0,'C',true); 
			$pdf->Cell(25,5,'Type Article',1,0,'C',true); 
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqDocClient)){
				$idSpec=$lg->idSpec;
				$nomFicher=$lg->nomFichier;
				$lien=$idSpec.$nomFicher;
				
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(60,5,$lg->nom,1,'L');
				$newY=$pdf->GetY();
				$pdf->SetXY($x+60,$y);
				$pdf->SetTextColor(0,0,200);
				$pdf->Cell(70,$newY-$y,$lg->reference,1,0,'C','',"http://thalesmee/demande/download.php?link=$lien&nomOr=$nomFicher");
				$pdf->SetTextColor(0,0,0);
				$pdf->Cell(15,$newY-$y,$lg->issue,1,0,'C');
				$pdf->Cell(10,$newY-$y,$lg->rev,1,0,'C');
				$pdf->Cell(25,$newY-$y,$lg->com,1,1,'C');
			}
			
			//Interface Electrique par Article 
			$pdf->Ln(10);
			$yInt=$pdf->getY();	
			$pdf->Cell(35,0,'Interface électrique par article:');
			$pdf->Ln(5);
			$pdf->Cell(60,5,'No Article',1,0,'C',true);
			$pdf->Cell(30,5,'Référence',1,0,'C',true);
			$pdf->Cell(15,5,'Type',1,0,'C',true);
			$pdf->Cell(15,5,'Issue',1,0,'C',true);
			$pdf->Cell(10,5,'Rev',1,0,'C',true);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqDocArtIE)){ 
				$no=$lg->num;
				$com=$lg->com;
				if ($com!=""){$no="$com / $no";}
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(60,5,$no,1,'L');
				$newY=$pdf->GetY();
				$pdf->SetXY($x+60,$y);
				$pdf->Cell(30,$newY-$y,$lg->reference,1,0,'C');
				$pdf->Cell(15,$newY-$y,$lg->type_,1,0,'C');
				$pdf->Cell(15,$newY-$y,$lg->issue,1,0,'C');
				$pdf->Cell(10,$newY-$y,$lg->rev,1,1,'C');
			}
			
			//interface mécanique
			$pdf->Ln(10);
			$pdf->Cell(35,0,'Interface Mécanique par Article:');
			$pdf->Ln(5);
			$pdf->Cell(60,5,'No Article',1,0,'C',true);
			$pdf->Cell(30,5,'Référence',1,0,'C',true);
			$pdf->Cell(15,5,'Type',1,0,'C',true);
			$pdf->Cell(15,5,'Issue',1,0,'C',true);
			$pdf->Cell(10,5,'Rev',1,0,'C',true);
			$pdf->Ln(5);
			while($lg=mysqli_fetch_object($reqDocArtIM)){ 
				$no=$lg->num;
				$com=$lg->com;
				if ($com!=""){$no="$com / $no";}
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(60,5,$no,1,'L');
				$newY=$pdf->GetY();
				$pdf->SetXY($x+60,$y);
				$pdf->Cell(30,$newY-$y,$lg->reference,1,0,'C');
				$pdf->Cell(15,$newY-$y,$lg->type_,1,0,'C');
				$pdf->Cell(15,$newY-$y,$lg->issue,1,0,'C');
				$pdf->Cell(10,$newY-$y,$lg->rev,1,1,'C');
			}
			
			//remarques si besoin
			if ($remarque!=""){
				$pdf->Ln(10);
				$pdf->SetTextColor(150,0,0);
				$pdf->MultiCell(0,5,"Remarques: ".$remarque);
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