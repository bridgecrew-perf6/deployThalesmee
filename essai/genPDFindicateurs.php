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
	
	$num=$_GET["num"];
	$deb=$_GET["dateDeb"];
	$fin=$_GET["dateFin"];
	$service=$_GET["idService"];
	
	//generation du pdf
	
	$pdf = new FPDF();
	$pdf->AddPage('L');
	$pdf->SetFillColor(128,128,128);
	
	//titre
	$pdf->SetFont('Arial','B',16);
	$pdf->SetFont('Arial','B',12);

	
	
	$pdf->Cell(20,20,"",0,1,'C');
	if ($num == 1){
		$url = "http://localhost/graph/nombre_anomalie.php?num=".$num."&idService=".$service."&dateDeb=".$deb."&dateFin=".$fin;
	}else if ($num == 2){
		$url = "http://localhost/graph/cumulatif_anomalie.php?num=".$num."&idService=".$service."&dateDeb=".$deb."&dateFin=".$fin;
	}else if ($num ==3){
		$url = "http://localhost/graph/pourcentage_anomalie.php?num=".$num."&idService=".$service."&dateDeb=".$deb."&dateFin=".$fin;
	}else{
		$url = "http://localhost/graph/pourcentage_cumulatif_anomalie.php?num=".$num."&idService=".$service."&dateDeb=".$deb."&dateFin=".$fin;
	}
	
	//Si l'utilisateur veut les essais planifiés
	if (isset ($_GET['I2PT'])){
		
		$url .= "&I2PT=1";
	}
	//Si l'utilisateur veut les essais reservés
	if (isset ($_GET['I2PA'])){
		
		$url .= "&I2PA=1";
	}
	//Si l'utilisateur veut les essais en attente
	if (isset ($_GET['LPA'])){
		
		$url .= "&LPA=1";
	}
	
	if (isset ($_GET['LPH'])){
		
		$url .= "&LPH=1";
	}
	
	if (isset ($_GET['Autre'])){
		
		$url .= "&Autre=1";
	}
	
	if (isset ($_GET['LPE'])){
		
		$url .= "&LPE=1";
	}
	
	if (isset ($_GET['Total'])){
		
		$url .= "&Total=1";
	}
	
	if (isset ($_GET['HorsDite'])){
		
		$url .= "&HorsDite=1";
	}
	
	$pdf->Image($url,15,5,0,0, "PNG");
	
	
	$pdf->Output();
	
}else{
	// l utilisateur n 'est pas reconnu
	echo "<script>alert(\"Erreur d'authentification.\");</script>";
}
?>