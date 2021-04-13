<?php
require('../fpdf/fpdf.php');//la version de fpdf a été modifié pour accepter l'utf-8
require('../conf/connexion_param.php');// connexion a la base
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
	$target=$_GET["target"];
	
	//generation du pdf
	
	$pdf = new FPDF();
	$pdf->AddPage("L");
	$pdf->SetFillColor(128,128,128);
	
	//titre
	$pdf->SetFont('Arial','B',16);
	$pdf->SetFont('Arial','B',12);

	
	if (isset($_GET["arg"])){
		
		if ($_GET["arg"]== 'procedure'){
			
			$pdf->Cell(20,20,"",0,1,'C');
			if ($_GET["idGraphe"] == "1"){
				
				$url = "http://localhost/graph/suivi_proc.php?idService=$idService";
				
			}else if ($_GET["idGraphe"] == "2"){
				
				$url = "http://localhost/graph/duree_redac.php?idService=$idService";
			
			}else if ($_GET["idGraphe"] == "3"){
				
				$url = "http://localhost/graph/ecart_proc.php?idService=$idService";
			
			}

		}else {
			
			$pdf->Cell(20,20,"",0,1,'C');
			$url = "http://localhost/graph/retardEnregistre.php?idService=$idService&dateDeb=$dateDeb&dateFin=$dateFin";

		}
		
		
	}else if ($_GET["fifo"]==1){
		
		$pdf->Cell(20,20,"",0,1,'C');
		//$pdf->Image("http://localhost/graph/rex_fifo_of.php?idService=$idService&dateDeb=$dateDeb&dateFin=$dateFin",105,30,100,0,"PNG");
		
		//
		
		if ($_GET["idGraphe"] == "8"){
			
			$url = "http://localhost/graph/attente_equip_fin.php?idService=$idService&fifo=1&dateDeb=$dateDeb&dateFin=$dateFin";
			
		}else if ($_GET["idGraphe"] == "9"){
			
			$url = "http://localhost/graph/attente_equip_av.php?idService=$idService&fifo=1&dateDeb=$dateDeb&dateFin=$dateFin";
			
		}else if ($_GET["idGraphe"] == "10"){
			
			$url = "http://localhost/graph/rex_fifo_test.php?idService=$idService&fifo=1&dateDeb=$dateDeb&dateFin=$dateFin";
			
		}		
		
		
	}else {
		$pdf->Cell(10,10,"",0,1,'C');
		
		if ($_GET["idGraphe"] == "6"){
		
			$url = "http://localhost/graph/attente_equip_av.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin";
		
		}else if ($_GET["idGraphe"] == "7"){
			
			$url = "http://localhost/graph/attente_equip_fin.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin";
			
		}else if ($_GET["idGraphe"] == "16"){
			
			$url = "http://localhost/graph/attente_equip_av_annuel.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";
			
		}else if ($_GET["idGraphe"] == "17"){
			
			$url = "http://localhost/graph/attente_equip_fin_annuel.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";
			
		}else if ($_GET["idGraphe"] == "18"){
			
			$url = "http://localhost/graph/fpy.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";
			
		}else if ($_GET["idGraphe"] == "19"){
			
			$url = "http://localhost/graph/fiabilitePrevision.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";
			
		}else if ($_GET["idGraphe"] == "20"){
			
			$url = "http://localhost/graph/occupation.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";
			
		}else if ($_GET["idGraphe"] == "21"){
			
			$url = "http://localhost/graph/occupation_annuel.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";

		}else if ($_GET["idGraphe"] == "22"){
			
			$url = "http://localhost/graph/cause_anomalie.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";

		}else if ($_GET["idGraphe"] == "23"){
			
			$url = "http://localhost/graph/retard_fin_test.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";

		}else if ($_GET["idGraphe"] == "24"){
			
			$url = "http://localhost/graph/retard_test.php?idService=$idService&fifo=2&dateDeb=$dateDeb&dateFin=$dateFin&target=$target";
		}
		
	}
	
	//Si l'utilisateur veut les essais planifiés
	if (isset ($_GET['Tous'])){
		
		$url .= "&Tous=1";
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
	
	if (isset ($_GET['moyenne'])){
		
		$url .= "&moyenne=1";
	}
	
	if (isset ($_GET['median'])){
		
		$url .= "&median=1";
	}

	if (isset ($_GET['moyenneRetard'])){
		
		$url .= "&moyenneRetard=1";
	}
	
	if (isset ($_GET['medianRetard'])){
		
		$url .= "&medianRetard=1";
	}
	
	if (isset ($_GET['global'])){
		
		$url .= "&global=1";
	}
	
	if (isset ($_GET['i2pt'])){
		
		$url .= "&i2pt=1";
	}
	
	$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $idService";
	$req = mysqli_query($bdd, $str);
	$moyen = "";
	
	while ($lg=mysqli_fetch_object($req)){
		
		$moy = str_replace(" ", "-", $lg->nomMoyen);
		if (isset ($_GET[$moy])){
		
			$url .= "&".$moy."=1";
		}
	}
	
	$pdf->Image($url, 10,10,0,0,"PNG");
	$pdf->Output();
	
}else{
	// l utilisateur n 'est pas reconnu
	echo "<script>alert(\"Erreur d'authentification.\");</script>";
}
?>