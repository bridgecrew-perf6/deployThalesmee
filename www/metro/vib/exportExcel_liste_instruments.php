<?php

require ('../conf/connexion_param.php'); //connexion a la bdd
require ('../fonction.php');
require ('../Classes/PHPExcel.php');
require ('../Classes/PHPExcel/Writer/Excel2007.php');


//creation de l'excel
$excel = new PHPExcel;


//style bordures
$styleBordTab = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$styleBordTitre = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_MEDIUM
    )
  ),
  'font'=> array('bold'=> true)
);

$styleBordSeparation = array(
  'borders' => array(
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_MEDIUM)
  )
);

$titleColor = array(
	'fill' => array (
		'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' =>
			array('rgb' => 'A4A4A4')
	)
);

$subtitleColor = array(
	'fill' => array (
		'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' =>
			array('rgb' => 'D8D8D8')
	)
);

$center = array(
	'alignment' => array (
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
	)
);

$centerVertical = array(
	'alignment' => array (
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//choix feuille
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Tableau récapitulatif');

$mois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");

//remplissage des cellules
$sheet->setCellValue('B2','Etalonnage mois de '.$mois[date("n")].' '.date("Y"));
$sheet->mergeCells("B2:G3");
$excel->getActiveSheet()->getStyle('B2:G3')->applyFromArray($titleColor);
$excel->getActiveSheet()->getStyle('B2:G3')->applyFromArray($centerVertical);


$sheet->setCellValue('B4','Numéro Immo');
$sheet->setCellValue('C4','Fournisseur');
$sheet->setCellValue('D4','Modèle');
$sheet->setCellValue('E4','N°Série');
$sheet->setCellValue('F4','Prochain étalonnage');
$sheet->setCellValue('G4','Date envoi');

$excel->getActiveSheet()->getStyle('B2:G4')->applyFromArray($center);
$excel->getActiveSheet()->getStyle('B4:G4')->applyFromArray($styleBordTitre);


$filtre = "'".str_replace(",", "','",$_GET["filter"])."'";

if ($filtre == ""){
	
	$str = "SELECT `numInstru`, nomDes, `marque`,`modele`,`numSerie`,`date_futureInt`, nomLocal FROM localisation, designation, instrument WHERE idDes_designation = idDes and idLocal_localisation = idLocal and idStatut_statut != 4 ORDER BY nomDes, modele";
	
}else {
	
	$str = "SELECT `numInstru`, nomDes,`marque`,`modele`,`numSerie`,`date_futureInt` FROM  localisation, designation, instrument WHERE idDes_designation = idDes and idLocal_localisation = idLocal and (numSerie in ($filtre) or numInstru in ($filtre) or TrescalID in ($filtre)) and idStatut_statut != 4 ORDER BY nomDes, modele";
}

$req = mysqli_query($bdd, $str);
$cpt = 5;
$designation ="";
while($lg=mysqli_fetch_object($req))
{
	if ($lg->nomDes != $designation){
		
		$sheet->setCellValue('B'.$cpt,$lg->nomDes);
		$sheet->mergeCells("B".$cpt.":G".$cpt);
		$excel->getActiveSheet()->getStyle("B".$cpt.":G".$cpt)->applyFromArray($subtitleColor);
		$excel->getActiveSheet()->getStyle("B".$cpt.":G".$cpt)->applyFromArray($center);
		$excel->getActiveSheet()->getStyle("B".$cpt.":G".$cpt)->applyFromArray($styleBordTitre);
		$designation = $lg->nomDes;
		$cpt += 1;
		
		$sheet->setCellValue('B'.$cpt,'Numéro Immo');
		$sheet->setCellValue('C'.$cpt,'Fournisseur');
		$sheet->setCellValue('D'.$cpt,'Modèle');
		$sheet->setCellValue('E'.$cpt,'N°Série');
		$sheet->setCellValue('F'.$cpt,'Prochain étalonnage');
		$sheet->setCellValue('G'.$cpt,'Date envoi');

		$excel->getActiveSheet()->getStyle('B'.$cpt.':G'.$cpt)->applyFromArray($center);
		$excel->getActiveSheet()->getStyle('B'.$cpt.':G'.$cpt)->applyFromArray($styleBordTitre);
		$cpt += 1;
		
	}
		
	$sheet->setCellValue('B'.$cpt,$lg->numInstru);
	$sheet->setCellValue('C'.$cpt,$lg->marque);		
	$sheet->setCellValue('D'.$cpt,$lg->modele);
	$sheet->setCellValue('E'.$cpt,$lg->numSerie);
	$sheet->setCellValue('F'.$cpt,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('G'.$cpt,date("d/m/Y"));
	
	$cpt += 1;
}

$excel->getActiveSheet()->getStyle('B2:G3')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('B4:G'.($cpt-1))->applyFromArray($styleBordTab);

for($col = 'B'; $col !== 'H'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

$excel->setActiveSheetIndex(0);
$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');


?>