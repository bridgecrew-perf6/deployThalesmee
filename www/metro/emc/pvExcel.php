<?php
function getRowcount($text, $width=55) {
    $rc = 0;
    $line = explode("\n", $text);
    foreach($line as $source) {
        $rc += intval((strlen($source) / $width) +1);
    }
    return $rc;
}

require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php'); //connexion a la bdd
$idPV=$_GET["idPV"];

$str="select titrePv from PV where idPv=$idPV;";
$req=mysqli_query($bdd,$str);
$titrePV=mysqli_fetch_object($req)->titrePv;

$str="select d.fonction, i.numSerie, i.modele, i.marque, i.date_futureInt, GROUP_CONCAT(t.nomtest SEPARATOR ',') as cTest
from pvtest p, test t, instrument i, instrument_emc ie, designation_emc d
where idPv_pv=$idPV
and ie.numInstru_instrument=i.numInstru
and ie.idDes_designation_emc=d.idDes
and p.numInstru_instrument=i.numInstru
and t.idTest=p.idTest_test
group by i.numInstru;";
$req=mysqli_query($bdd,$str);

//creation de l'excel
include '../Classes/PHPExcel.php';
include '../Classes/PHPExcel/Writer/Excel2007.php';

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
$sheet->setCellValue('A2','Fonction');
$sheet->setCellValue('B2','Type/Modèle');
$sheet->setCellValue('C2','Fabricant');
$sheet->setCellValue('D2','Numéro de série');
$sheet->setCellValue('E2','Prochaine cal');
$sheet->setCellValue('F2','Test');


$i=2;
while($lg=mysqli_fetch_object($req))
{
	$i++; //evite un i-- ou des i-1
	$sheet->setCellValue('A'.$i,htmlspecialchars_decode($lg->fonction));
	$sheet->setCellValue('B'.$i,htmlspecialchars_decode($lg->modele));
	$sheet->setCellValue('C'.$i,htmlspecialchars_decode($lg->marque));
	$sheet->setCellValue('D'.$i,htmlspecialchars_decode($lg->numSerie));
	$sheet->setCellValue('E'.$i,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('F'.$i,htmlspecialchars_decode($lg->cTest));
}

//styles des cellules
$sheet->getStyle("A2:F".$i)->getBorders()->applyFromArray($style_bord);
$sheet->getStyle("A2:F".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$sheet->getStyle("A2:F2")->getFont()->setBold(true); 
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);

//auto size row ><


$numrows = getRowcount($titrePV);

$sheet->setCellValue('A1',"PV n°".$idPV.": ".$titrePV);
$sheet->getRowDimension(1)->setRowHeight($numrows * 12.75 + 2.25);
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1:F1')->getAlignment()->setWrapText(true); 


$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');


