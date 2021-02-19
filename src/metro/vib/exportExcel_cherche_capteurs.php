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

$sheet->setCellValue('A1','Tableau récap des capteurs');
$sheet->setCellValue('A2','Type');
$sheet->setCellValue('B2','Marque');
$sheet->setCellValue('C2','Référence');
$sheet->setCellValue('D2','Total');
$sheet->setCellValue('E2','En Service');
$sheet->setCellValue('F2','Calibration');
$sheet->setCellValue('G2','Prêt Cannes');
$sheet->setCellValue('H2','Prêt Intespace');
$sheet->setCellValue('I2','Prêt Mecano');
$sheet->setCellValue('J2','Rebut');

$str="Select idTypeC, nomTypeC FROM typecapteur, instrument_vib_capteur where idTypeC_typecapteur = idTypeC group by idTypeC;" ;
$reqType=mysqli_query($bdd, $str);
$cptType = 3;
$cptMarque=3;
$cptModele=3;
$total = 3;
while($lg=mysqli_fetch_object($reqType))
	{
		$premier = true;
		$exception = true;
		$str="SELECT count(distinct(modele)) as total FROM instrument ,`instrument_vib_capteur` WHERE `idTypeC_typeCapteur`=".$lg->idTypeC." and `numInstru_instrument`=numInstru;";
		$reqNbLigneType=mysqli_query($bdd, $str);
		$lgNbLigneType=mysqli_fetch_object($reqNbLigneType);
		
		$str="SELECT marque as nomMarque ,count(Distinct(modele)) as nbligneMarque  FROM `instrument_vib_capteur`, instrument WHERE `idTypeC_typeCapteur`=".$lg->idTypeC." and `numInstru_instrument`= numInstru  group by marque;";
		$Marque = mysqli_query($bdd, $str) or die ("erreur: '".mysqli_error($bdd));
		while($lgMarque=mysqli_fetch_object($Marque)){
			
			$nbLigneMarque = $lgMarque->nbligneMarque;
			$marque = $lgMarque->nomMarque;
			if ($exception == true && strstr($marque,'PCB') ){
				
				$exception = false;
				$str = "SELECT count(Distinct(modele)) as nbligneMarque  FROM `instrument_vib_capteur`, instrument WHERE `idTypeC_typeCapteur`=".$lg->idTypeC." and `numInstru_instrument`= numInstru and marque like 'PCB%'  ";
				$reqException=mysqli_query($bdd, $str);
				$lgException=mysqli_fetch_object($reqException);
				$nbLigneMarque = $lgException->nbligneMarque;
				
				$marque = "PCB";
				if ($premier == true){
					
					$sheet->setCellValue('A'.$cptType,$lg->nomTypeC);
					$sheet->mergeCells("A".($cptType).":A".($cptType+$lgNbLigneType->total-1));
					$cptType += $lgNbLigneType->total;
					$sheet->setCellValue('B'.$cptMarque,$marque);
					$sheet->mergeCells("B".($cptMarque).":B".($cptMarque+$nbLigneMarque-1));
					$cptMarque += $nbLigneMarque;
					
					
					$capteur = $lg->idTypeC;
				
					$str="SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque like 'PCB%' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele;";
					if (isset($_GET['DESC'])) {
					
						if ($_GET['DESC']=="DESC"){
						
							$str="SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque like 'PCB%' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele DESC;";
						}
					
					}
				
				
					$modele=mysqli_query($bdd, $str) or die ("erreur: '".mysqli_error($bdd));
				
					$prems= true;
					while($lgmodele=mysqli_fetch_object($modele)){
					
						$mod = $lgmodele->modele;
						$strCalib="SELECT count(*) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
						$reqCalib=mysqli_query($bdd, $strCalib);
						$lgCalib=mysqli_fetch_object($reqCalib);
					
						$strCannes="SELECT count(*) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
						$reqCannes=mysqli_query($bdd, $strCannes);
						$lgCannes=mysqli_fetch_object($reqCannes);
					
						$strIntespace="SELECT count(*) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
						$reqIntespace=mysqli_query($bdd, $strIntespace);
						$lgIntespace=mysqli_fetch_object($reqIntespace);
					
						$strMecano="SELECT count(*) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
						$reqMecano=mysqli_query($bdd, $strMecano);
						$lgMecano=mysqli_fetch_object($reqMecano);
					
						$strRebut="SELECT count(*) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
						$reqRebut=mysqli_query($bdd, $strRebut);
						$lgRebut=mysqli_fetch_object($reqRebut);
					
						$EnService = $lgmodele->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
						if ($EnService < 0) {
						
							$EnService = 0;
						}	
						if ($prems==true){
						
							$sheet->setCellValue('C'.$cptModele,$lgmodele->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
							$cptModele += 1;
							
							$prems = false;
						}else{
							$sheet->setCellValue('C'.$cptModele,$lgmodele->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
							$cptModele += 1;

						}
					}

					$premier = false;
				}else{

				$capteur = $lg->idTypeC;
				$sheet->setCellValue('B'.$cptMarque,$marque);
				$sheet->mergeCells("B".($cptMarque).":B".($cptMarque+$nbLigneMarque-1));
				$cptMarque += $nbLigneMarque;
	
				
				$str2="SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque like 'PCB%' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele;";
				if (isset($_GET['DESC'])) {
					
					if ($_GET['DESC']=="DESC"){
						
						$str2="SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque like 'PCB%' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele DESC;";
					}
					
				}
				
				$modele2=mysqli_query($bdd, $str2) or die ("erreur: '".mysqli_error($bdd));
				$prems = true;
				while($lgmodele2=mysqli_fetch_object($modele2)){
					
					$mod = $lgmodele2->modele;
					$strCalib="SELECT count(*) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
					$reqCalib=mysqli_query($bdd, $strCalib);
					$lgCalib=mysqli_fetch_object($reqCalib);
					
					$strCannes="SELECT count(*) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
					$reqCannes=mysqli_query($bdd, $strCannes);
					$lgCannes=mysqli_fetch_object($reqCannes);
					
					$strIntespace="SELECT count(*) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
					$reqIntespace=mysqli_query($bdd, $strIntespace);
					$lgIntespace=mysqli_fetch_object($reqIntespace);
					
					$strMecano="SELECT count(*) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
					$reqMecano=mysqli_query($bdd, $strMecano);
					$lgMecano=mysqli_fetch_object($reqMecano);
					
					$strRebut="SELECT count(*) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
					$reqRebut=mysqli_query($bdd, $strRebut);
					$lgRebut=mysqli_fetch_object($reqRebut);
					
					$EnService = $lgmodele2->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
					if ($EnService < 0) {
						$EnService = 0;
						
					}
					if ($prems==true){
						
						$sheet->setCellValue('C'.$cptModele,$lgmodele2->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele2->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
						$cptModele += 1;
						
						$prems = false;
					}else{
						$sheet->setCellValue('C'.$cptModele,$lgmodele2->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele2->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
						$cptModele += 1;
					}	
					
				}
				
			}
			
			}else {

				if(!strstr($marque,'PCB') ){

					if ($premier == true){
				
						$sheet->setCellValue('A'.$cptType,$lg->nomTypeC);
						$sheet->mergeCells("A".($cptType).":A".($cptType+$lgNbLigneType->total-1));
						$cptType += $lgNbLigneType->total;
						$sheet->setCellValue('B'.$cptMarque,$marque);
						$sheet->mergeCells("B".($cptMarque).":B".($cptMarque+$nbLigneMarque-1));
						$cptMarque += $nbLigneMarque;
						$capteur = $lg->idTypeC;
						$str="SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque='$marque' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele;";
						if (isset($_GET['DESC'])) {
					
							if ($_GET['DESC']=="DESC"){
						
								$str="SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque='$marque' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele DESC;";
							}
					
						}
						$modele=mysqli_query($bdd, $str) or die ("erreur: '".mysqli_error($bdd));
						$prems= true;
						while($lgmodele=mysqli_fetch_object($modele)){
							
							$mod = $lgmodele->modele;
							$strCalib="SELECT count(*) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
							$reqCalib=mysqli_query($bdd, $strCalib)or die ("erreur: '".mysqli_error($bdd));
							$lgCalib=mysqli_fetch_object($reqCalib);
					
							$strCannes="SELECT count(*) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
							$reqCannes=mysqli_query($bdd, $strCannes)or die ("erreur: '".mysqli_error($bdd));
							$lgCannes=mysqli_fetch_object($reqCannes);
					
							$strIntespace="SELECT count(*) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
							$reqIntespace=mysqli_query($bdd, $strIntespace)or die ("erreur: '".mysqli_error($bdd));
							$lgIntespace=mysqli_fetch_object($reqIntespace);
					
							$strMecano="SELECT count(*) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
							$reqMecano=mysqli_query($bdd, $strMecano)or die ("erreur: '".mysqli_error($bdd));
							$lgMecano=mysqli_fetch_object($reqMecano);
					
							$strRebut="SELECT count(*) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
							$reqRebut=mysqli_query($bdd, $strRebut)or die ("erreur: '".mysqli_error($bdd));
							$lgRebut=mysqli_fetch_object($reqRebut);
					
							$EnService = $lgmodele->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
							if ($EnService < 0) {
								$EnService = 0;
						
							}
							if ($prems==true){
						
								$sheet->setCellValue('C'.$cptModele,$lgmodele->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
								$cptModele += 1;
								$prems = false;
							}else{
								$sheet->setCellValue('C'.$cptModele,$lgmodele->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
								$cptModele += 1;

							}

						}

						$premier = false;
					}else{
				
						$marque = $lgMarque->nomMarque;
						$capteur = $lg->idTypeC;
						$sheet->setCellValue('B'.$cptMarque,$marque);
						$sheet->mergeCells("B".($cptMarque).":B".($cptMarque+$nbLigneMarque-1));
						$cptMarque += $nbLigneMarque;
						$str2 = "SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque='$marque' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele;";
						if (isset($_GET['DESC'])) {
					
							if ($_GET['DESC']=="DESC"){
						
								$str2 = "SELECT modele, count(*) as total FROM `instrument_vib_capteur`, instrument WHERE marque='$marque' and `numInstru_instrument`= numInstru and `idTypeC_typeCapteur`='$capteur' group by modele order by modele DESC;";
							}
					
						}
				
						$modele2=mysqli_query($bdd, $str2) or die ("erreur: '".mysqli_error($bdd));
						$prems = true;
						while($lgmodele2=mysqli_fetch_object($modele2)){
							
							$mod = $lgmodele2 -> modele;
							$strCalib="SELECT count(*) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
							$reqCalib=mysqli_query($bdd, $strCalib);
							$lgCalib=mysqli_fetch_object($reqCalib);
					
							$strCannes="SELECT count(*) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
							$reqCannes=mysqli_query($bdd, $strCannes);
							$lgCannes=mysqli_fetch_object($reqCannes);
					
							$strIntespace="SELECT count(*) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
							$reqIntespace=mysqli_query($bdd, $strIntespace);
							$lgIntespace=mysqli_fetch_object($reqIntespace);
					
							$strMecano="SELECT count(*) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
							$reqMecano=mysqli_query($bdd, $strMecano);
							$lgMecano=mysqli_fetch_object($reqMecano);
					
							$strRebut="SELECT count(*) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
							$reqRebut=mysqli_query($bdd, $strRebut);
							$lgRebut=mysqli_fetch_object($reqRebut);
					
							$EnService = $lgmodele2->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
							if ($EnService < 0) {
								$EnService = 0;
						
							}
							if ($prems==true){
						
								$sheet->setCellValue('C'.$cptModele,$lgmodele2->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele2->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
								$cptModele += 1;
						
								$prems = false;
							}else{
								$sheet->setCellValue('C'.$cptModele,$lgmodele2->modele);
							$sheet->setCellValue('D'.$cptModele,$lgmodele2->total);
							$sheet->setCellValue('E'.$cptModele,$EnService);
							$sheet->setCellValue('F'.$cptModele,$lgCalib -> calib);
							$sheet->setCellValue('G'.$cptModele,$lgCannes -> cannes);
							$sheet->setCellValue('H'.$cptModele,$lgIntespace -> intespace);
							$sheet->setCellValue('I'.$cptModele,$lgMecano->mecano);
							$sheet->setCellValue('J'.$cptModele,$lgRebut->rebut);
								$cptModele += 1;
							}
					
					
						}
				
					}
			
				}
			}
			
		}
		
		
	}

$excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleBordTitre);
$excel->getActiveSheet()->getStyle('A3:J'.($cptMarque-1))->applyFromArray($styleBordTab);
for($col = 'A'; $col !== 'K'; $col++) {
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}




$excel->setActiveSheetIndex(0);
$writer = new PHPExcel_Writer_Excel2007($excel);

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');