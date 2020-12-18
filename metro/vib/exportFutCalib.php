<?php

require ('../conf/connexion_param.php'); //connexion a la bdd
require ('../fonction.php');
require ('../Classes/PHPExcel.php');
require ('../Classes/PHPExcel/Writer/Excel2007.php');

$dDeb=dateFrToSQL($_GET["dDeb"]);
$dFin=dateFrToSQL($_GET["dFin"]);

$str="SELECT numInstru, nomDes, nomStatut, marque, modele, numSerie, date_futureInt, nomLocal 
from statut s, instrument_vib iv, instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal 
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes 
where i.numInstru = iv.numInstru_instrument 
and i.idStatut_statut=s.idStatut 
and i.idStatut_statut!=4 
and i.date_futureInt >= '$dDeb' 
and i.date_futureInt < '$dFin'
UNION ALL 
SELECT numInstru, nomDes, nomStatut, marque, modele, numSerie, date_futureInt, nomLocal 
FROM statut s, instrument_vib_capteur ivc, instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal 
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes 
where i.numInstru = ivc.numInstru_instrument 
and i.idStatut_statut=s.idStatut 
and i.idStatut_statut!=4 
and i.date_futureInt >= '$dDeb' 
and i.date_futureInt < '$dFin'
order by date_futureInt;";
$req=mysqli_query($bdd,$str);

//creation de l'excel
$excel = new PHPExcel;

$sheet = $excel->getActiveSheet();

$style_bord = array(
	'allborders' => array(
		'style' => PHPExcel_Style_Border::BORDER_THIN ,
		'color' => array(
			'rgb' => '000000'
		)
	)
 );


 
//remplissage des cellules
$sheet->setCellValue('A2','Numéro');
$sheet->setCellValue('B2','Désignation');
$sheet->setCellValue('C2','Statut');
$sheet->setCellValue('D2','Fournisseur');
$sheet->setCellValue('E2','Modèle');
$sheet->setCellValue('F2','N°Série');
$sheet->setCellValue('G2','Next cal');
$sheet->setCellValue('H2','Localisation');


$i=2;
while($lg=mysqli_fetch_object($req))
{
	$i++; //evite un i-- ou des i-1
	$sheet->setCellValue('A'.$i,$lg->numInstru);
	$sheet->setCellValue('B'.$i,$lg->nomDes);
	$sheet->setCellValue('C'.$i,$lg->nomStatut);
	$sheet->setCellValue('D'.$i,$lg->marque);
	$sheet->setCellValue('E'.$i,$lg->modele);
	$sheet->setCellValue('F'.$i,$lg->numSerie);
	$sheet->setCellValue('G'.$i,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('H'.$i,$lg->nomLocal);
	
}

//styles des cellules
$sheet->getStyle("A2:H".$i)->getBorders()->applyFromArray($style_bord);
$sheet->getStyle("A2:H".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$sheet->getStyle("A2:H2")->getFont()->setBold(true); 
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setAutoSize(true);

//auto size row ><


$sheet->setCellValue('A1','Calibration du '.dateSQLToFr($dDeb).' au '.dateSQLToFr($dFin));
$sheet->mergeCells('A1:H1');
$sheet->getStyle('A1:H1')->getAlignment()->setWrapText(true); 


$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');
