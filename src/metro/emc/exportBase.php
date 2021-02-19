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

$str="SELECT numInstru, nomEquip, fonction, marque, modele, caracteristique, date_futureInt, nomLocal, CASE when trescalid IS NULL then 'Interne' ELSE 'Trescal' END as ti
from instrument_emc ie 
LEFT OUTER JOIN designation_emc d ON ie.idDes_designation_emc=d.idDes 
LEFT OUTER JOIN equipement_emc ee ON d.idEquip_equipement_emc=ee.idEquip, 
instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal 
where i.numInstru=ie.numInstru_instrument 
and i.idStatut_statut!=4
order by nomEquip DESC ,fonction ASC, marque,modele;";
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
 
$style_BordEquip =  array(
	 'style' => PHPExcel_Style_Border::BORDER_THICK,
	 'color' => array(
		 'rgb' => 'FF3333'
	 )
 );
 
 $style_BordFonc =  array(
	 'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
	 'color' => array(
		 'rgb' => '3399FF'
	 )
 );

//ici sinon ecrasement des styles de bordures défini plus loin
$num=mysqli_num_rows($req)+1; //ligne de titre
$sheet->getStyle("A1:I".$num)->getBorders()->applyFromArray($style_bord);
 
//remplissage des cellules
$sheet->setCellValue('A1','Numéro');
$sheet->setCellValue('B1','Equipement');
$sheet->setCellValue('C1','Fonction');
$sheet->setCellValue('D1','Fabricant');
$sheet->setCellValue('E1','Modéle');
$sheet->setCellValue('F1','Caractéristiques');
$sheet->setCellValue('G1','Date FI');
$sheet->setCellValue('H1','Localisation');
$sheet->setCellValue('I1','Trescal | Interne');


$i=1;
while($lg=mysqli_fetch_object($req))
{
	$i++; //ici car evite le i-- a la fin
	$sheet->setCellValue('A'.$i,htmlspecialchars_decode($lg->numInstru));
	$sheet->setCellValue('B'.$i,htmlspecialchars_decode($lg->nomEquip));
	$sheet->setCellValue('C'.$i,htmlspecialchars_decode($lg->fonction));
	$sheet->setCellValue('D'.$i,htmlspecialchars_decode($lg->marque));
	$sheet->setCellValue('E'.$i,htmlspecialchars_decode($lg->modele));
	$sheet->setCellValue('F'.$i,htmlspecialchars_decode($lg->caracteristique));
	$sheet->setCellValue('G'.$i,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('H'.$i,htmlspecialchars_decode($lg->nomLocal));
	$sheet->setCellValue('I'.$i,$lg->ti);
	
	if(!(isset($equip)))
	{
		$equip=$lg->nomEquip;
		$fonc=$lg->fonction;
	}
	elseif($lg->nomEquip!=$equip)
	{
		$sheet->getStyle('A'.$i.':I'.$i)->getBorders()->getTop()->applyFromArray($style_BordEquip);
		$equip=$lg->nomEquip;
		$fonc=$lg->fonction;
	}
	elseif($lg->fonction!=$fonc)
	{
		$sheet->getStyle('A'.$i.':I'.$i)->getBorders()->getTop()->applyFromArray($style_BordFonc);
		$fonc=$lg->fonction;
	}
}

//styles des cellules

$sheet->getStyle("A1:I".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$sheet->getStyle("A1:I1")->getFont()->setBold(true); 
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setAutoSize(true);
$sheet->getColumnDimension('I')->setAutoSize(true);

//auto size row ><
$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=BaseInstrument.xlsx ');
$writer->save('php://output');


