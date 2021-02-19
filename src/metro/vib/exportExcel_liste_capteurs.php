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

$excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//choix feuille
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Capteurs');

//remplissage des cellules
$sheet->setCellValue('A1','Liste des capteurs');
$sheet->setCellValue('A2','Numéro');
$sheet->setCellValue('B2','Type');
$sheet->setCellValue('C2','Désignation');
$sheet->setCellValue('D2','Fournisseur');
$sheet->setCellValue('E2','Modèle');
$sheet->setCellValue('F2','N°Série');
$sheet->setCellValue('G2','Date FI');
$sheet->setCellValue('H2','Localisation');

$filtre = $_GET["filter"];

if ($filtre == ""){
	
	$str = "SELECT `numInstru`,nomTypeC, nomDes,`marque`,`modele`,`numSerie`,`date_futureInt`, nomLocal FROM instrument_vib_capteur, typecapteur, localisation, designation, instrument WHERE numInstru_instrument = `numInstru` and idTypeC_typeCapteur = `idTypeC` and idDes_designation = idDes and idLocal_localisation = idLocal and idStatut_statut != 4 ORDER BY numInstru";
	
}else {
	
	$str = "SELECT `numInstru`,nomTypeC, nomDes,`marque`,`modele`,`numSerie`,`date_futureInt`, nomLocal FROM instrument_vib_capteur, typecapteur, localisation, designation, instrument WHERE numInstru_instrument = `numInstru` and idTypeC_typeCapteur = `idTypeC` and idDes_designation = idDes and idLocal_localisation = idLocal and numSerie in ($filtre) and idStatut_statut != 4 ORDER BY numInstru";
}

$req = mysqli_query($bdd, $str);
$cpt = 3;
while($lg=mysqli_fetch_object($req))
{
	$sheet->setCellValue('A'.$cpt,$lg->numInstru);
	$sheet->setCellValue('B'.$cpt,$lg->nomTypeC);
	$sheet->setCellValue('C'.$cpt,$lg->nomDes);
	$sheet->setCellValue('D'.$cpt,$lg->marque);
	$sheet->setCellValue('E'.$cpt,$lg->modele);
	$sheet->setCellValue('F'.$cpt,$lg->numSerie);
	$sheet->setCellValue('G'.$cpt,$lg->date_futureInt);
	$sheet->setCellValue('H'.$cpt,$lg->nomLocal);
	$cpt += 1;
}

$excel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:H'.($cpt-1))->applyFromArray($styleBordTab);

for($col = 'A'; $col !== 'I'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

$excel->setActiveSheetIndex(0);
$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');



?>