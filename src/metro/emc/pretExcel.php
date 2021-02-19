<?php
function getRowcount($text, $width=55) {
    $rc = 0;
    $line = explode("\n", $text);
    foreach($line as $source) {
        $rc += intval((strlen($source) / $width) +1);
    }
    return $rc;
}
session_start();
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php');
include '../Classes/PHPExcel.php';
include '../Classes/PHPExcel/Writer/Excel2007.php';

if(isset($_GET["idPret"]))
{
	$idPret=$_GET["idPret"];
	$table=", concernePret c";
	$cond="and c.idPret_pret=$idPret";
	$str="select nomPret,nomCorresp,nomLocal 
	from pret p 
	LEFT JOIN localisation l ON p.idLocal_localisation=l.idLocal
	where idPret=$idPret;";
}
elseif(isset($_GET["histo_idPret"]))
{
	$idPret=$_GET["histo_idPret"];
	$table=", histo_concernePret c";
	$cond="and c.idPret_histo_pret=$idPret";
	$str="select nomPret,nomCorresp,nomLocal 
	from histo_pret h 
	LEFT JOIN localisation l ON h.idLocal_localisation=l.idLocal 
	where idPret=$idPret;";
}


$req=mysqli_query($bdd,$str);
$lg=mysqli_fetch_object($req);

$nomPret=$lg->nomPret;
$nomCorresp=$lg->nomCorresp;
$lieu=$lg->nomLocal;


$str="select i.numInstru, fonction, i.modele, i.marque, c.datePret,c.dateRetour
from instrument i, instrument_emc ie
LEFT OUTER JOIN designation_emc d ON ie.idDes_designation_emc=d.idDes
$table
where i.numInstru=c.numInstru_instrument
and ie.numInstru_instrument=i.numInstru
$cond ;";
$reqInfo=mysqli_query($bdd, $str);
ECHO mysqli_error($bdd);
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
$sheet->setCellValue('A3','N°Immo');
$sheet->setCellValue('B3','Fonction');
$sheet->setCellValue('C3','Modèle');
$sheet->setCellValue('D3','Fournisseur');
$sheet->setCellValue('E3','Date out');
$sheet->setCellValue('F3','Date in prévue');

$i=3;
//d'abord les non capteurs
while($lg=mysqli_fetch_object($reqInfo))
{
	$i++; //evite un i--
	$sheet->setCellValue('A'.$i,$lg->numInstru);
	$sheet->setCellValue('B'.$i,html_entity_decode(htmlspecialchars_decode($lg->fonction)));
	$sheet->setCellValue('C'.$i,html_entity_decode(htmlspecialchars_decode($lg->modele)));
	$sheet->setCellValue('D'.$i,html_entity_decode(htmlspecialchars_decode($lg->marque)));
	$sheet->setCellValue('E'.$i,dateSQLToFr($lg->datePret));
	$sheet->setCellValue('F'.$i,dateSQLToFr($lg->dateRetour));
}
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);

$sheet->getStyle('A4:F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//styles des cellules
$sheet->getStyle("A3:F".$i)->getBorders()->applyFromArray($style_bord);
$sheet->getStyle("A3:F".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$sheet->getStyle("A3:F3")->getFont()->setBold(true); 

$numrows = getRowcount($nomPret);

$sheet->setCellValue('A1',$nomPret);
$sheet->getRowDimension(1)->setRowHeight($numrows * 12.75 + 2.25);
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1:F1')->getAlignment()->setWrapText(true);

$lieuNom="Correspondant: ".$nomCorresp;
$numrows = getRowcount($lieuNom);

$sheet->setCellValue('A2',$lieuNom);
$sheet->getRowDimension(1)->setRowHeight($numrows * 12.75 + 2.25);
$sheet->mergeCells('A2:F2');
$sheet->getStyle('A2:F2')->getAlignment()->setWrapText(true); 

$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');
