<?php
function rempLigne($sheet,$ligne,$idEssai,$affaire,$equipement,$depositaire,$os,$remarque,$nomMoyen,$fifo,$retard_interne,$planifie,$date_debut,$date_fin,$of,$tabEtat)
{
	$sheet->setCellValue('A'.$ligne,htmlspecialchars_decode($idEssai));
	$sheet->setCellValue('B'.$ligne,htmlspecialchars_decode($affaire));
	$sheet->setCellValue('C'.$ligne,htmlspecialchars_decode($equipement));
	$sheet->setCellValue('D'.$ligne,htmlspecialchars_decode($of));
	$sheet->setCellValue('E'.$ligne,htmlspecialchars_decode($os));
	$sheet->setCellValue('F'.$ligne,htmlspecialchars_decode($nomMoyen));
	$sheet->setCellValue('G'.$ligne,htmlspecialchars_decode($date_debut));
	$sheet->setCellValue('H'.$ligne,htmlspecialchars_decode($date_fin));
	$sheet->setCellValue('I'.$ligne,htmlspecialchars_decode($depositaire));
	$sheet->setCellValue('J'.$ligne,htmlspecialchars_decode($fifo));
	$sheet->setCellValue('K'.$ligne,htmlspecialchars_decode($retard_interne));
	$sheet->setCellValue('L'.$ligne,htmlspecialchars_decode($planifie));
	
	if(isset($tabEtat[20]))
		$sheet->setCellValue('M'.$ligne,htmlspecialchars_decode($tabEtat[20]));
	if(isset($tabEtat[21]))
		$sheet->setCellValue('N'.$ligne,htmlspecialchars_decode($tabEtat[21]));
	if(isset($tabEtat[22]))
		$sheet->setCellValue('O'.$ligne,htmlspecialchars_decode($tabEtat[22]));
	if(isset($tabEtat[23]))
		$sheet->setCellValue('P'.$ligne,htmlspecialchars_decode($tabEtat[23]));
	if(isset($tabEtat[24]))
		$sheet->setCellValue('Q'.$ligne,htmlspecialchars_decode($tabEtat[24]));
	if(isset($tabEtat[25]))
		$sheet->setCellValue('R'.$ligne,htmlspecialchars_decode($tabEtat[25]));

	$sheet->setCellValue('S'.$ligne,htmlspecialchars_decode($remarque));	
}

if(isset($_POST["dateDebut"]))
{
	require ('../conf/connexion_param.php'); //connexion a la bdd
	require ('../fonction.php');
	require ('../Classes/PHPExcel.php');
	require ('../Classes/PHPExcel/Writer/Excel2007.php');
	
	$dateDebut=dateFrToSQL($_POST["dateDebut"]);
	$dateFin=dateFrToSQL($_POST["dateFin"]);
	$idLabo=$_POST["idLabo"];
	
	$str="SELECT e.idEssai, e.affaire, e.equipement, e.os, e.commentaire, et.idEtat_ETAT, e.date_debut, e.date_fin, d.nomDep ,m.nomMoyen, fifo, retard_interne, planifie
	FROM etatEssai et, essai e 
	LEFT JOIN depositaire d on e.idDep_depositaire=d.idDep
	LEFT JOIN moyen m on e.idMoyen_MOYEN=m.idMoyen
	where e.date_debut >= '$dateDebut'
	and e.date_fin <= '$dateFin'
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEtat_ETAT!=26
	and et.idEssai_ESSAI=e.idEssai
	and e.idService_service='$idLabo'
	order by date_debut;";
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	//creation de l'excel
	$excel = new PHPExcel;
	$sheet = $excel->getActiveSheet();
	
	//remplissage des cellules titres
	$sheet->setCellValue('A1','N° de l\'essai');
	$sheet->setCellValue('B1','Affaire');
	$sheet->setCellValue('C1','Equipement');
	$sheet->setCellValue('D1','OF');
	$sheet->setCellValue('E1','OS');
	$sheet->setCellValue('F1','Moyen');
	$sheet->setCellValue('G1','Date de début');
	$sheet->setCellValue('H1','Date de fin');
	$sheet->setCellValue('I1','Dépositaire');
	$sheet->setCellValue('J1','Fifo');
	$sheet->setCellValue('K1','Retard interne');
	$sheet->setCellValue('L1','planifie');
	$sheet->setCellValue('M1','Date plannification');
	$sheet->setCellValue('N1','Date réservation');
	$sheet->setCellValue('O1','Date de récéption de l\'équipement');
	$sheet->setCellValue('P1','Date de lancement de l\'essai');
	$sheet->setCellValue('Q1','Date de fin de l\'essai');
	$sheet->setCellValue('R1','Date du retour de l\'équipement');
	$sheet->setCellValue('S1','Remarque');
	$ligne=2;
	while($lg=mysqli_fetch_object($req))
	{
		$idEssai=$lg->idEssai;
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$depositaire=$lg->nomDep;
		$os=$lg->os;
		$remarque=$lg->commentaire;
		$nomMoyen=$lg->nomMoyen;
		
		if($lg->fifo==1)
			$fifo="Oui";
		else
			$fifo="Non";
		
		if($lg->retard_interne==1)
			$retard_interne="Oui";
		else
			$retard_interne="Non";
			
		if($lg->planifie==1)
			$planifie="Oui";
		else
			$planifie="Non";

		
		$date_debut=dateSQLToFrWithHours($lg->date_debut);
		$date_fin=dateSQLToFrWithHours($lg->date_fin);
		
		//recupération des of
		$str="select noOF_equipement_of from tester
		where idEssai_ESSAI='$idEssai';";
		$reqOF=mysqli_query($bdd,$str);
		$of="";
		while($lgOF=mysqli_fetch_object($reqOF))
			$of.=$lgOF->noOF_equipement_of." ";
			
		//recuperation des etats
		$str="select dateEtat, idEtat_ETAT from etatEssai
		where idEssai_ESSAI='$idEssai'
		order by idEtat_ETAT;";
		$reqEtat=mysqli_query($bdd,$str);
		$tabEtat=array();
		while($lgEtat=mysqli_fetch_object($reqEtat))
			$tabEtat[$lgEtat->idEtat_ETAT]=dateSQLToFrWithHours($lgEtat->dateEtat);
		
		rempLigne($sheet,$ligne,$idEssai,$affaire,$equipement,$depositaire,$os,$remarque,$nomMoyen,$fifo,$retard_interne,$planifie,$date_debut,$date_fin,$of,$tabEtat);
		$ligne++;
	
	}
	
	$sheet->getColumnDimension('A')->setAutoSize(true);
	$sheet->getColumnDimension('B')->setAutoSize(true);
	$sheet->getColumnDimension('C')->setAutoSize(true);
	$sheet->getColumnDimension('D')->setAutoSize(true);
	$sheet->getColumnDimension('E')->setAutoSize(true);
	$sheet->getColumnDimension('F')->setAutoSize(true);
	$sheet->getColumnDimension('G')->setAutoSize(true);
	$sheet->getColumnDimension('H')->setAutoSize(true);
	$sheet->getColumnDimension('I')->setAutoSize(true);
	$sheet->getColumnDimension('J')->setAutoSize(true);
	$sheet->getColumnDimension('K')->setAutoSize(true);
	$sheet->getColumnDimension('L')->setAutoSize(true);
	$sheet->getColumnDimension('M')->setAutoSize(true);
	$sheet->getColumnDimension('N')->setAutoSize(true);
	$sheet->getColumnDimension('O')->setAutoSize(true);
	$sheet->getColumnDimension('P')->setAutoSize(true);
	$sheet->getColumnDimension('Q')->setAutoSize(true);
	$sheet->getColumnDimension('R')->setAutoSize(true);
	$sheet->getColumnDimension('S')->setAutoSize(true);
	
	$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        )
    );
	$sheet->getStyle("A1:S".$ligne)->applyFromArray($style);

	header("Content-Type: application/force-download;charset=UTF-16LE");
	header('Content-Transfer-Encoding: binary'); 
	header("Content-disposition: attachment; filename=export.xlsx");
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->setIncludeCharts(TRUE);
  
	function saveViaTempFile($objWriter)
	{
		$filePath = sys_get_temp_dir()."\\ProjetBidon.xlsx";
		ob_end_clean();
		$objWriter->save($filePath);
		readfile($filePath);
	}

	saveViaTempFile($writer);
	//$writer->save('php://output');
}
else
{
	require('top.php'); 
	$idLabo=$_SESSION['infoUser']['idService'];// service du labo
	$dateDebut="01/01/".date('Y');
	$dateFin="31/12/".date('Y');
	
	
	?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2>Export Excel</h2>
		</div>
		<form method="post" action="exportExcel.php">
			<div class="container theme-showcase" role="main">
				<h4>Indiquer les dates encadrants l'export</h4>
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-4">
							<div class="autre-form" >
								<span style="float:left;">Date début: <input placeholder="01/01/2014" value="<?php echo $dateDebut; ?>" type="text" name="dateDebut" id="dateDebut" class="calendrier"  size="8" required/></span>
							</div>
						</div>
						<div class="col-md-4>
							<div class="autre-form">
								<span style="float:left;">Date fin: <input placeholder="01/01/2014" value="<?php echo $dateFin; ?>" type="text" name="dateFin" id="dateFin" class="calendrier"  size="8" required/></span>
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="text-center">
				<input type="hidden" value="<?php echo $idLabo; ?>" name="idLabo" />
				<input type="submit" class="btn btn-lg btn-primary" value="Valider" />
				<input type="button" class="btn btn-lg btn-primary" onclick="document.location.href='./index.php'" value="Retour" />
			</div>
		</form>
	</div>
	<script src="../js/creer_modifierEssai.js"></script>
<?php
require('bottom.php');
}
?>