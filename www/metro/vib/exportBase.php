<?php

require ('../conf/connexion_param.php'); //connexion a la bdd
require ('../fonction.php');
require ('../Classes/PHPExcel.php');
require ('../Classes/PHPExcel/Writer/Excel2007.php');

$str="select numInstru, nomDes, nomStatut, numSerie, marque, modele, date_futureInt, nomLocal 
from statut s, instrument_vib iv, instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
where i.idStatut_statut=s.idStatut
and i.numInstru = iv.numInstru_instrument;";
$req=mysqli_query($bdd,$str);


$str="select axeX, sensiX, axeY, sensiY, axeZ, sensiZ,idTypeC_typeCapteur,
numInstru, nomStatut, numSerie, marque, modele, date_derniereInt,date_futureInt, nomLocal 
from statut s, instrument_vib_capteur iv, instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
where i.idStatut_statut=s.idStatut
and i.numInstru = iv.numInstru_instrument;";
$reqCapteur=mysqli_query($bdd,$str);

$tabPilotes=array();
$tabMono=array();
$tabTri=array();
$tabCouple=array();
$tabForce=array();
$tabForceTri=array();
$tabMicro=array();
$tabChoc=array();
$tabChocTri=array();
$cptPilote=0;
$cptMono=0;
$cptTri=0;
$cptCouple=0;
$cptForce=0;
$cptForceTri=0;
$cptMicro=0;
$cptChoc=0;
$cptChocTri=0;
while($lg=mysqli_fetch_object($reqCapteur))
{
	//pilotes
	if($lg->idTypeC_typeCapteur==1){
		$tabPilotes[$cptPilote]=$lg;
		$cptPilote++;
	}
	elseif($lg->idTypeC_typeCapteur==2){
		$tabMono[$cptMono]=$lg;
		$cptMono++;
	}
	elseif($lg->idTypeC_typeCapteur==3){
		$tabTri[$cptTri]=$lg;
		$cptTri++;
	}
	elseif($lg->idTypeC_typeCapteur==4){
		$tabCouple[$cptCouple]=$lg;
		$cptCouple++;
	}
	elseif($lg->idTypeC_typeCapteur==5){
		$tabForce[$cptForce]=$lg;
		$cptForce++;
	}
	elseif($lg->idTypeC_typeCapteur==6){
		$tabForceTri[$cptForceTri]=$lg;
		$cptForceTri++;
	}
	elseif($lg->idTypeC_typeCapteur==7){
		$tabMicro[$cptMicro]=$lg;
		$cptMicro++;
	}
	elseif($lg->idTypeC_typeCapteur==8){
		$tabChoc[$cptChoc]=$lg;
		$cptChoc++;
	}
	elseif($lg->idTypeC_typeCapteur==9){
		$tabChocTri[$cptChocTri]=$lg;
		$cptChocTri++;
	}	
}

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
$sheet->setTitle('Instruments');

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Instruments');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Désignation');
$sheet->setCellValue('C2','Statut');
$sheet->setCellValue('D2','Fournisseur');
$sheet->setCellValue('E2','Modèle');
$sheet->setCellValue('F2','N°série');
$sheet->setCellValue('G2','Prochain étalonnage');
$sheet->setCellValue('H2','Localisation');

$i=3;
while($lg=mysqli_fetch_object($req))
{
	$sheet->setCellValue('A'.$i,$lg->numInstru);
	$sheet->setCellValue('B'.$i,$lg->nomDes);
	$sheet->setCellValue('C'.$i,$lg->nomStatut);
	$sheet->setCellValue('D'.$i,$lg->marque);
	$sheet->setCellValue('E'.$i,$lg->modele);
	$sheet->setCellValue('F'.$i,$lg->numSerie);
	if($lg->date_futureInt!="")
		$sheet->setCellValue('G'.$i,dateSQLToFr($lg->date_futureInt));
	$sheet->setCellValue('H'.$i,$lg->nomLocal);
	$i++;
}
$excel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:H'.($i-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'I'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}


//choix feuille
$excel->createSheet(1);
$excel->setActiveSheetIndex(1);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Monoaxes');

//pilotes

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Pilote');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');

$l=3;
for($i=0;$i<$cptPilote;$i++)
{
	$sheet->setCellValue('A'.$l,$tabPilotes[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabPilotes[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabPilotes[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabPilotes[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabPilotes[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabPilotes[$i]->sensiX);
	if($tabPilotes[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabPilotes[$i]->date_derniereInt));
	if($tabPilotes[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabPilotes[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabPilotes[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabPilotes[$i]->nomStatut);
	$l++;
}
$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:J'.($l-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

//Monoaxes
$l++; //saute une ligne
//remplissage des cellules
$sheet->mergeCells('A'.$l.':C'.$l);
$sheet->setCellValue('A'.$l,'Monoaxe');
$l++;
$sheet->setCellValue('A'.$l,'N°Immo');
$sheet->setCellValue('B'.$l,'Fournisseur');
$sheet->setCellValue('C'.$l,'Modèle');
$sheet->setCellValue('D'.$l,'N°série');
$sheet->setCellValue('E'.$l,'Axe');
$sheet->setCellValue('F'.$l,'Sensibilité mV/g');
$sheet->setCellValue('G'.$l,'Étalonnage');
$sheet->setCellValue('H'.$l,'Prochain étalonnage');
$sheet->setCellValue('I'.$l,'Localisation');
$sheet->setCellValue('J'.$l,'Statut');
$ligneDeb=$l;
$l++;
for($i=0;$i<$cptMono;$i++)
{
	$sheet->setCellValue('A'.$l,$tabMono[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabMono[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabMono[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabMono[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabMono[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabMono[$i]->sensiX);
	if($tabMono[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabMono[$i]->date_derniereInt));
	if($tabMono[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabMono[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabMono[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabMono[$i]->nomStatut);
	$l++;
}

$excel->getActiveSheet()->getStyle('A'.$ligneDeb.':J'.$ligneDeb)->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A'.($ligneDeb+1).':J'.($l-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}


//Triaxes

//choix feuille
$excel->createSheet(2);
$excel->setActiveSheetIndex(2);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Triaxes');

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Triaxe');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');
$l=3;
$excel->getActiveSheet()->getStyle('A3:J'.($cptTri*3+2))->applyFromArray($styleBordTab);
for($i=0;$i<$cptTri;$i++)
{
	$sheet->setCellValue('E'.$l,$tabTri[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabTri[$i]->sensiX);
	$l++;
	
	$sheet->setCellValue('A'.$l,$tabTri[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabTri[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabTri[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabTri[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabTri[$i]->axeY);
	$sheet->setCellValue('F'.$l,$tabTri[$i]->sensiY);
	if($tabTri[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabTri[$i]->date_derniereInt));
	if($tabTri[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabTri[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabTri[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabTri[$i]->nomStatut);
	
	$l++;
	$sheet->setCellValue('E'.$l,$tabTri[$i]->axeZ);
	$sheet->setCellValue('F'.$l,$tabTri[$i]->sensiZ);
	$excel->getActiveSheet()->getStyle('A'.$l.':J'.$l)->applyFromArray($styleBordSeparation);
	$l++;
}

$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

//Couple

//choix feuille
$excel->createSheet(3);
$excel->setActiveSheetIndex(3);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Couples');

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Couple');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/Nm');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');
$l=3;
$excel->getActiveSheet()->getStyle('A3:J'.($cptCouple*3+2))->applyFromArray($styleBordTab);
for($i=0;$i<$cptCouple;$i++)
{
	$sheet->setCellValue('A'.$l,$tabCouple[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabCouple[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabCouple[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabCouple[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabCouple[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabCouple[$i]->sensiX);
	if($tabCouple[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabCouple[$i]->date_derniereInt));
	if($tabCouple[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabCouple[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabCouple[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabCouple[$i]->nomStatut);
	
	$l++;
	$sheet->setCellValue('E'.$l,$tabCouple[$i]->axeY);
	$sheet->setCellValue('F'.$l,$tabCouple[$i]->sensiY);
	
	$l++;
	$sheet->setCellValue('E'.$l,$tabCouple[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabCouple[$i]->sensiX);
	
	$excel->getActiveSheet()->getStyle('A'.$l.':J'.$l)->applyFromArray($styleBordSeparation);
	$l++;
}

$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}


//Cellule force

//choix feuille
$excel->createSheet(4);
$excel->setActiveSheetIndex(4);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Cellule_Force_Monoaxe');


//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Cellule force mono axe');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');

$l=3;
for($i=0;$i<$cptForce;$i++)
{
	$sheet->setCellValue('A'.$l,$tabForce[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabForce[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabForce[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabForce[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabForce[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabForce[$i]->sensiX);
	if($tabForce[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabForce[$i]->date_derniereInt));
	if($tabForce[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabForce[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabForce[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabForce[$i]->nomStatut);
	$l++;
}
$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:J'.($l-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}


//Couple

//choix feuille
$excel->createSheet(5);
$excel->setActiveSheetIndex(5);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Cellule_Force_triaxes');

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Cellule de force triaxes');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');
$l=3;
$excel->getActiveSheet()->getStyle('A3:J'.($cptForceTri*3+2))->applyFromArray($styleBordTab);
for($i=0;$i<$cptForceTri;$i++)
{
	$sheet->setCellValue('A'.$l,$tabForceTri[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabForceTri[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabForceTri[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabForceTri[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabForceTri[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabForceTri[$i]->sensiX);
	if($tabForceTri[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabForceTri[$i]->date_derniereInt));
	if($tabForceTri[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabForceTri[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabForceTri[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabForceTri[$i]->nomStatut);
	
	$l++;
	$sheet->setCellValue('E'.$l,$tabForceTri[$i]->axeY);
	$sheet->setCellValue('F'.$l,$tabForceTri[$i]->sensiY);
	
	$l++;
	$sheet->setCellValue('E'.$l,$tabForceTri[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabForceTri[$i]->sensiX);
	
	$excel->getActiveSheet()->getStyle('A'.$l.':J'.$l)->applyFromArray($styleBordSeparation);
	$l++;
}

$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}


//Microphone

//choix feuille
$excel->createSheet(6);
$excel->setActiveSheetIndex(6);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Microphone');


//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Microphone');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');

$l=3;
for($i=0;$i<$cptMicro;$i++)
{
	$sheet->setCellValue('A'.$l,$tabMicro[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabMicro[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabMicro[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabMicro[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabMicro[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabMicro[$i]->sensiX);
	if($tabMicro[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabMicro[$i]->date_derniereInt));
	if($tabMicro[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabMicro[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabMicro[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabMicro[$i]->nomStatut);
	$l++;
}
$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:J'.($l-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

//Choc

//choix feuille
$excel->createSheet(7);
$excel->setActiveSheetIndex(7);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Choc_Monoaxe');

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Choc Monoaxes');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');

$l=3;
for($i=0;$i<$cptChoc;$i++)
{
	$sheet->setCellValue('A'.$l,$tabChoc[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabChoc[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabChoc[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabChoc[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabChoc[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabChoc[$i]->sensiX);
	if($tabChoc[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabChoc[$i]->date_derniereInt));
	if($tabChoc[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabChoc[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabChoc[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabChoc[$i]->nomStatut);
	$l++;
}
$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:J'.($l-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

//Choc triaxes

//choix feuille
$excel->createSheet(8);
$excel->setActiveSheetIndex(8);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Choc_Triaxes');

//remplissage des cellules
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1','Choc Triaxes');
$sheet->setCellValue('A2','N°Immo');
$sheet->setCellValue('B2','Fournisseur');
$sheet->setCellValue('C2','Modèle');
$sheet->setCellValue('D2','N°série');
$sheet->setCellValue('E2','Axe');
$sheet->setCellValue('F2','Sensibilité mV/g');
$sheet->setCellValue('G2','Étalonnage');
$sheet->setCellValue('H2','Prochain étalonnage');
$sheet->setCellValue('I2','Localisation');
$sheet->setCellValue('J2','Statut');
$l=3;
$excel->getActiveSheet()->getStyle('A3:J'.($cptChocTri*3+2))->applyFromArray($styleBordTab);
for($i=0;$i<$cptChocTri;$i++)
{
	$sheet->setCellValue('E'.$l,$tabChocTri[$i]->axeX);
	$sheet->setCellValue('F'.$l,$tabChocTri[$i]->sensiX);
	$l++;
	
	$sheet->setCellValue('A'.$l,$tabChocTri[$i]->numInstru);
	$sheet->setCellValue('B'.$l,$tabChocTri[$i]->marque);
	$sheet->setCellValue('C'.$l,$tabChocTri[$i]->modele);
	$sheet->setCellValue('D'.$l,$tabChocTri[$i]->numSerie);
	$sheet->setCellValue('E'.$l,$tabChocTri[$i]->axeY);
	$sheet->setCellValue('F'.$l,$tabChocTri[$i]->sensiY);
	if($tabChocTri[$i]->date_derniereInt!="")
		$sheet->setCellValue('G'.$l,dateSQLToFr($tabChocTri[$i]->date_derniereInt));
	if($tabChocTri[$i]->date_futureInt!="")
		$sheet->setCellValue('H'.$l,dateSQLToFr($tabChocTri[$i]->date_futureInt));
	$sheet->setCellValue('I'.$l,$tabChocTri[$i]->nomLocal);
	$sheet->setCellValue('J'.$l,$tabChocTri[$i]->nomStatut);
	
	$l++;
	$sheet->setCellValue('E'.$l,$tabChocTri[$i]->axeZ);
	$sheet->setCellValue('F'.$l,$tabChocTri[$i]->sensiZ);
	$excel->getActiveSheet()->getStyle('A'.$l.':J'.$l)->applyFromArray($styleBordSeparation);
	$l++;
}

$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}


$excel->setActiveSheetIndex(0);
$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');