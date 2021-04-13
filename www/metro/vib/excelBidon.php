<?php
session_start();
function rempLigne($sheet,$ligne,$marque,$type,$axe,$description,$measureQ,$sensi,$nomUnite,$dateSQL)
{
	$sheet->setCellValue('A'.$ligne,htmlspecialchars_decode($marque));
	$sheet->setCellValue('B'.$ligne,htmlspecialchars_decode($type));
	$sheet->setCellValue('C'.$ligne,htmlspecialchars_decode($axe));
	$sheet->setCellValue('D'.$ligne,htmlspecialchars_decode($description));
	$sheet->setCellValue('E'.$ligne,htmlspecialchars_decode($measureQ));
	$sheet->setCellValue('F'.$ligne,'100');
	$sheet->setCellValue('G'.$ligne,$sensi);
	$sheet->setCellValue('H'.$ligne,htmlspecialchars_decode($nomUnite));
	$sheet->setCellValue('I'.$ligne,'0');
	$sheet->setCellValue('J'.$ligne,'mV');
	$sheet->setCellValue('K'.$ligne,'+');
	$sheet->setCellValue('L'.$ligne,$dateSQL." 00:00:00");
	$sheet->setCellValue('M'.$ligne,'365');
	$sheet->setCellValue('N'.$ligne,'');
	$sheet->setCellValue('O'.$ligne,'');
	$sheet->setCellValue('P'.$ligne,'');
	$sheet->setCellValue('Q'.$ligne,'');
	$sheet->setCellValue('R'.$ligne,'');
	$sheet->setCellValue('S'.$ligne,'');
	$sheet->setCellValue('T'.$ligne,'');
	$sheet->setCellValue('U'.$ligne,'');
	$sheet->setCellValue('V'.$ligne,'');
	$sheet->setCellValue('W'.$ligne,'');
}

if(isset($_GET["idProjet"]))
{
	require ('../conf/connexion_param.php'); //connexion a la bdd
	require ('../fonction.php');
	require ('../Classes/PHPExcel.php');
	require ('../Classes/PHPExcel/Writer/Excel2007.php');
	$tab=array();
	$tabCapteurForce=array();
	$excel = new PHPExcel;

	$sheet = $excel->getActiveSheet();
	$ligne=2;

	foreach ($_SESSION['capteur'] as $capt){
		
		$idProjet=$_GET["idProjet"];
		$str="select i.marque, de.nomDes, i.numSerie, s.nomStatut, i.modele, i.date_futureInt, c.pied, u.nomUnite, ic.idTypeC_typeCapteur as types, i.modele, i.numInstru,
		ic.axeX, ic.sensiX, ic.axeY, ic.sensiY, ic.axeZ, ic.sensiZ, ic.axeZs, ic.sensiZs
		from statut s, instrument_vib_capteur ic LEFT OUTER JOIN unite u ON u.idUnite=ic.idUnite_unite, concerneBidon c,
		instrument i 
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		where c.idProjet_projetBidon='$idProjet' and i.numInstru = '$capt'
		and ic.idInstruCapt=c.idInstruCapt_instrument_vib_capteur
		and i.idStatut_statut=s.idStatut
		and ic.numInstru_instrument=i.numInstru
		order by c.ordre;";
		$req=mysqli_query($bdd,$str);
		
		//creation de l'excel
		
		
		//remplissage des cellules
		$sheet->setCellValue('A1','Manufacturer');
		$sheet->setCellValue('B1','Type');
		$sheet->setCellValue('C1','Serial Number');
		$sheet->setCellValue('D1','Description');
		$sheet->setCellValue('E1','Measured Quantity');
		$sheet->setCellValue('F1','Nominal sensitivity');
		$sheet->setCellValue('G1','Actual sensitivity');
		$sheet->setCellValue('H1','Sensitivity unit');
		$sheet->setCellValue('I1','Offset');
		$sheet->setCellValue('J1','Electrical Unit');
		$sheet->setCellValue('K1','Polarity');
		$sheet->setCellValue('L1','Due for Calibration');
		$sheet->setCellValue('M1','Calibration valid for');
		$sheet->setCellValue('N1','Equalization State');
		$sheet->setCellValue('O1','Sound Field');
		$sheet->setCellValue('P1','Simulated Value');
		$sheet->setCellValue('Q1','Offset Zeroing');
		$sheet->setCellValue('R1','Nominal offset');
		$sheet->setCellValue('S1','Electrical Value');
		$sheet->setCellValue('T1','Engineering Value');
		$sheet->setCellValue('U1','Bridge Supply');
		$sheet->setCellValue('V1','Input Mode');
		$sheet->setCellValue('W1','Gage Resistance');

		

		while($lg=mysqli_fetch_object($req))
		{

			if($lg->pied == NULL) //on ecrit directement les bobines
			{
				if($lg->sensiX != NULL)
				{

					if (intval($lg->types) == 10){

						if ($lg->modele == 'Random'){

							rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->numInstru,"Current","Current",$lg->sensiX,$lg->nomUnite,$lg->date_futureInt);
							$ligne++;	
						}else if ($lg->modele == 'Sinus'){
							
							rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->numInstru,"Current","Current",$lg->sensiX,$lg->nomUnite,$lg->date_futureInt);
							$ligne++;
							
						}
						
					}else if (intval($lg->types) == 11){

						
						if ($lg->modele == 'Random'){
							
							rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->numInstru,"Voltage","Voltage",$lg->sensiX,$lg->nomUnite,$lg->date_futureInt);
							$ligne++;	
						}else if ($lg->modele == 'Sinus'){
							
							rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->numInstru,"Voltage","Voltage",$lg->sensiX,$lg->nomUnite,$lg->date_futureInt);
							$ligne++;
							
						}
						
						
					}else{
						
						rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->axeX,"Accel","Acceleration",$lg->sensiX,$lg->nomUnite,$lg->date_futureInt);
						$ligne++; //pour les capteurs classiques 
						
					}
					
				}
				
				if($lg->axeY != null)
				{
					
					rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->axeY,"Accel","Acceleration",$lg->sensiY,$lg->nomUnite,$lg->date_futureInt);
					$ligne++; //pour les capteurs classiques 

				}
				if($lg->axeZ != null)
				{
					rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->axeZ,"Accel","Acceleration",$lg->sensiZ,$lg->nomUnite,$lg->date_futureInt);
					$ligne++; //pour les capteurs classiques 

				}
				/*if($lg->axeZs != null)
				{
					rempLigne($sheet,$ligne,$lg->marque,$lg->nomDes,$lg->axeZs,"Accel","Acceleration",$lg->sensiZs,$lg->nomUnite,$lg->date_futureInt);
					$ligne++;
				}*/
			}
			else //pour les capteurs de forces on les tries par pied
			{
				$pied=$lg->pied;
				$tabCapteurForce["$pied"][]=$lg;
			}
		}
	}
	
	
	foreach($tabCapteurForce as $tabPied)
	{
		$moySensiX=0;
		$moySensiY=0;
		$moySensiZ=0;
		//$moySensiZs=0;
		$dateCalMin="";
		foreach($tabPied as $lg)
		{
			if($lg->axeX != null)
				$moySensiX+=$lg->sensiX;
			if($lg->axeY != null)
				$moySensiY+=$lg->sensiY;
			if($lg->axeZ != null)
				$moySensiZ+=$lg->sensiZ;
			/*if($lg->axeZs != null)
				$moySensiZs+=$lg->sensiZs;*/
			
			//selection de la date la plus basse	
			if($dateCalMin=="" || strtotime($dateCalMin)>strtotime($lg->date_futureInt))
				$dateCalMin=$lg->date_futureInt;
		}
		//moyenne des sensibilité et division du résultats par 10, valeur absolu du résultat
		$moySensiX= abs(($moySensiX /count($tabPied))/10);
		$moySensiY=abs(($moySensiY /count($tabPied))/10);
		$moySensiZ=abs(($moySensiZ /count($tabPied))/10);
		//$moySensiZs=abs(($moySensiZs /count($tabPied))/10);
		
		//on ecrit une ligne par axe de pied, on se base sur le premier element du tableau pour remplir l'excel, ces infos ne servent que d'indications
		if($tabPied[0]->axeX != null)
		{
			rempLigne($sheet,$ligne,$tabPied[0]->marque,$tabPied[0]->nomDes,"Pied_".$tabPied[0]->pied."_X","Force","Force",$moySensiX,$tabPied[0]->nomUnite,$dateCalMin);
			$ligne++;
		}
		if($tabPied[0]->axeY != null)
		{
			rempLigne($sheet,$ligne,$tabPied[0]->marque,$tabPied[0]->nomDes,"Pied_".$tabPied[0]->pied."_Y","Force","Force",$moySensiY,$tabPied[0]->nomUnite,$dateCalMin);
			$ligne++;
		}
		if($tabPied[0]->axeZ != null)
		{
			rempLigne($sheet,$ligne,$tabPied[0]->marque,$tabPied[0]->nomDes,"Pied_".$tabPied[0]->pied."_Z","Force","Force",$moySensiZ,$tabPied[0]->nomUnite,$dateCalMin);
			$ligne++;
		}
		/*if($tabPied[0]->axeZs != null)
		{
			rempLigne($sheet,$ligne,$tabPied[0]->marque,$tabPied[0]->nomDes,"Pied_".$tabPied[0]->pied."_Zs","Force","Force",$moySensiZs,$tabPied[0]->nomUnite,$dateCalMin);
			$ligne++;
		}*/
	}
	$ligne--;
	//ajout du nom pour compatibilité avec le logiciel utilisé
	$excel->addNamedRange(
		new PHPExcel_NamedRange('TRANSDUCERS', $sheet, '$A$1:$W$'.$ligne) 
	);

	
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

	$writer = new PHPExcel_Writer_Excel5($excel);

	header('Content-type: application/vnd.ms-excel');
	header('Content-Disposition:inline;filename=ProjetBidon.xls');
	$writer->save('php://output');
}