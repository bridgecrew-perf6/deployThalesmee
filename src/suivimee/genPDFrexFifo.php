<?php
require('../fpdf/fpdf.php');//la version de fpdf a été modifié pour accepter l'utf-8
/*
a) méthode _escape
ajouter utf8_decode() autour de la variable $s

b) méthode Cell
supprimer la création de la variable $txt2
transformer $txt2 en $this->_escape($txt) dans le sprintf qui suit
*/


// Ce fichier genere un fichier pdf du rex fifo
session_start(); 

if (isset($_SESSION['infoUser'])){
	// l utilisateur a bien ete identifie
	
	$idService=$_GET["idService"];
	$dateDeb=$_GET["dateDeb"];
	$dateFin=$_GET["dateFin"];
	
	//generation du pdf
	
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFillColor(128,128,128);
	
	//titre
	$pdf->SetFont('Arial','B',16);
	$pdf->SetFont('Arial','B',12);

	
	if (isset($_GET["arg"])){
		
		if ($_GET["arg"]== 'procedure'){
			
			$pdf->Cell(60,5,"Retour suivi de procédure",0,1,'C');
			$pdf->Image("http://localhost/graph/suivi_proc.php",5,30,100,0,"PNG");
			$pdf->Image("http://localhost/graph/duree_redac.php?idService=$idService",105,30,100,0,"PNG");
			$pdf->Image("http://localhost/graph/ecart_proc.php?idService=$idService",5,110,100,0,"PNG");

			
			
		}else {
			
			$pdf->Cell(100,5,"Retard de livraison ",0,1,'C');
			$pdf->Image("http://localhost/graph/retardEnregistre.php?idService=$idService",5,30,100,0,"PNG");
			
			
			
		}
		
		
	}else if ($_GET["fifo"]==1){
		
		$pdf->Cell(60,5,"Retour d'experience FIFO",0,1,'C');
		$pdf->Image("http://localhost/graph/rex_fifo_test.php?idService=$idService&fifo=1&dateDeb=$dateDeb&dateFin=$dateFin",5,30,100,0,"PNG");
		//$pdf->Image("http://localhost/graph/rex_fifo_of.php?idService=$idService&dateDeb=$dateDeb&dateFin=$dateFin",105,30,100,0,"PNG");
		$pdf->Image("http://localhost/graph/attente_equip_av.php?idService=$idService&fifo=1&dateDeb=$dateDeb&dateFin=$dateFin",5,110,100,0,"PNG");
		$pdf->Image("http://localhost/graph/attente_equip_fin.php?idService=$idService&fifo=1&dateDeb=$dateDeb&dateFin=$dateFin",105,110,100,0,"PNG");
	}else {
		$pdf->Cell(60,5,"Retour d'experience non FIFO",0,1,'C');
		$pdf->Image("http://localhost/graph/attente_equip_av.php?idService=$idService&fifo=0&dateDeb=$dateDeb&dateFin=$dateFin",5,30,100,0,"PNG");
		$pdf->Image("http://localhost/graph/attente_equip_fin.php?idService=$idService&fifo=0&dateDeb=$dateDeb&dateFin=$dateFin",105,30,100,0,"PNG");
		$pdf->Image("http://localhost/graph/attente_equip_av.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin",5,110,100,0,"PNG");
		$pdf->Image("http://localhost/graph/attente_equip_fin.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin",105,110,100,0,"PNG");
	}
	
	$pdf->Output();
	
}else{
	// l utilisateur n 'est pas reconnu
	echo "<script>alert(\"Erreur d'authentification.\");</script>";
}
?>