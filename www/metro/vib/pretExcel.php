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

$str="select i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_derniereInt ,i.date_futureInt,c.datePret,c.dateRetour
from instrument_vib iv, instrument i
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
$table
where iv.numInstru_instrument=i.numInstru
and c.numInstru_instrument=i.numInstru
$cond ;";
$reqInfo=mysqli_query($bdd, $str);
echo mysqli_error($bdd);
$str="select axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs,
i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_derniereInt ,i.date_futureInt,c.datePret,c.dateRetour
from instrument_vib_capteur ivc, instrument i 
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
$table
where ivc.numInstru_instrument=i.numInstru
and c.numInstru_instrument=i.numInstru
$cond";
$reqInfoCapteur=mysqli_query($bdd, $str);
echo mysqli_error($bdd);

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
$sheet->setCellValue('B3','Désignation');
$sheet->setCellValue('C3','Fournisseur');
$sheet->setCellValue('D3','Modèle');
$sheet->setCellValue('E3','N°série');
$sheet->setCellValue('F3','Axes');
$sheet->setCellValue('G3','Sensibilité mV/g');
$sheet->setCellValue('H3','Étalonnage');
$sheet->setCellValue('I3','Prochain étalonnage');
$sheet->setCellValue('J3','Date de prêt');
$sheet->setCellValue('K3','Date de retour');

$i=3;
//d'abord les non capteurs
while($lg=mysqli_fetch_object($reqInfo))
{
	$i++; //evite un i--
	$sheet->setCellValue('A'.$i,$lg->numInstru);
	$sheet->setCellValue('B'.$i,html_entity_decode(htmlspecialchars_decode($lg->nomDes)));
	$sheet->setCellValue('C'.$i,html_entity_decode(htmlspecialchars_decode($lg->marque)));
	$sheet->setCellValue('D'.$i,html_entity_decode(htmlspecialchars_decode($lg->modele)));
	$sheet->setCellValue('E'.$i,$lg->numSerie);
	
	$sheet->setCellValue('H'.$i,dateSQLToFr($lg->date_derniereInt));
	$sheet->setCellValue('I'.$i,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('J'.$i,dateSQLToFr($lg->datePret));
	$sheet->setCellValue('K'.$i,dateSQLToFr($lg->dateRetour));
	
}
//ensuite les capteurs
while($lg=mysqli_fetch_object($reqInfoCapteur))
{
	
	$i++; //evite un i--
	$sheet->setCellValue('A'.$i,$lg->numInstru);
	$sheet->setCellValue('B'.$i,html_entity_decode(htmlspecialchars_decode($lg->nomDes)));
	$sheet->setCellValue('C'.$i,html_entity_decode(htmlspecialchars_decode($lg->marque)));
	$sheet->setCellValue('D'.$i,html_entity_decode(htmlspecialchars_decode($lg->modele)));
	$sheet->setCellValue('E'.$i,$lg->numSerie);
	
	$sheet->setCellValue('H'.$i,dateSQLToFr($lg->date_derniereInt));
	$sheet->setCellValue('I'.$i,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('J'.$i,dateSQLToFr($lg->datePret));
	$sheet->setCellValue('K'.$i,dateSQLToFr($lg->dateRetour));
	
	$oldI=$i;
	
	$sheet->setCellValue('F'.$i,$lg->axeX);
	$sheet->setCellValue('G'.$i,$lg->sensiX);
	$i++;
	$sheet->setCellValue('F'.$i,$lg->axeY);
	$sheet->setCellValue('G'.$i,$lg->sensiY);
	$i++;
	$sheet->setCellValue('F'.$i,$lg->axeZ);
	$sheet->setCellValue('G'.$i,$lg->sensiZ);
	$i++;
	$sheet->setCellValue('F'.$i,$lg->axeZs);
	$sheet->setCellValue('G'.$i,$lg->sensiZs);
	
	// Calculate the column widths
	foreach(range('A', 'K') as $columnID) {
		$sheet->getColumnDimension($columnID)->setAutoSize(true);
	}
	$sheet->calculateColumnWidths();

	// Set setAutoSize(false) so that the widths are not recalculated
	foreach(range('A', 'K') as $columnID) {
		$sheet->getColumnDimension($columnID)->setAutoSize(false);
	}
	
	$sheet->mergeCells('A'.$oldI.':A'.$i);
	$sheet->mergeCells('B'.$oldI.':B'.$i);
	$sheet->mergeCells('C'.$oldI.':C'.$i);
	$sheet->mergeCells('D'.$oldI.':D'.$i);
	$sheet->mergeCells('E'.$oldI.':E'.$i);
	$sheet->mergeCells('H'.$oldI.':H'.$i);
	$sheet->mergeCells('I'.$oldI.':I'.$i);
	$sheet->mergeCells('J'.$oldI.':J'.$i);
	$sheet->mergeCells('K'.$oldI.':K'.$i);
}
$sheet->getStyle('A4:K'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//styles des cellules
$sheet->getStyle("A3:K".$i)->getBorders()->applyFromArray($style_bord);
$sheet->getStyle("A3:K".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$sheet->getStyle("A3:K3")->getFont()->setBold(true); 

$numrows = getRowcount($nomPret);

$sheet->setCellValue('A1',$nomPret);
$sheet->getRowDimension(1)->setRowHeight($numrows * 12.75 + 2.25);
$sheet->mergeCells('A1:K1');
$sheet->getStyle('A1:K1')->getAlignment()->setWrapText(true);

$lieuNom="Correspondant: ".$nomCorresp."    Lieu: ".$lieu;
$numrows = getRowcount($lieuNom);

$sheet->setCellValue('A2',$lieuNom);
$sheet->getRowDimension(1)->setRowHeight($numrows * 12.75 + 2.25);
$sheet->mergeCells('A2:K2');
$sheet->getStyle('A2:K2')->getAlignment()->setWrapText(true); 

$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');

