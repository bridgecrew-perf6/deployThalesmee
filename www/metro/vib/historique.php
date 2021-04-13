<?php

require ('../conf/connexion_param.php'); //connexion a la bdd
require ('../fonction.php');
require ('../Classes/PHPExcel.php');
require ('../Classes/PHPExcel/Writer/Excel2007.php');

//creation de l'excel
$excel = new PHPExcel;

/*Affichage des en-tête du fichier Excel
* @param
* sheet : feuille de calcul
* type : type de capteur
* unite : unité de la sensibilité
* l : ligne de départ (par défault à 1)
*/
function affichageEnTete ($sheet, $type, $unite, $l=1)
{
	//Type de capteur
	$sheet->mergeCells('A'.$l.':C'.$l);
	$sheet->setCellValue('A'.$l,$type);
	//Sauter une ligne
	$l++;
	$sheet->setCellValue('A'.$l,'N°Immo');
	$sheet->setCellValue('B'.$l,'Fournisseur');
	$sheet->setCellValue('C'.$l,'Modèle');
	$sheet->setCellValue('D'.$l,'N°série');
	$sheet->setCellValue('E'.$l,'Axe');
	$sheet->setCellValue('F'.$l,'Actual Sensibilité '.$unite);
	$sheet->setCellValue('G'.$l,'Étalonnage');
	$sheet->setCellValue('H'.$l,'Prochain étalonnage');
	$sheet->setCellValue('I'.$l,'Localisation');
	$sheet->setCellValue('J'.$l,'Statut');
}

/*Affichage des en-tête du fichier Excel pour les capteurs crés par l'utilisateur
* @param
* sheet : feuille de calcul
* type : type de capteur
* unite : unité de la sensibilité
* l : ligne de départ (par défault à 1)
*/
function affichageEnTeteAutre ($sheet, $type, $unite, $l=1, $entete)
{
	//Type de capteur
	$sheet->mergeCells('A'.$l.':C'.$l);
	$sheet->setCellValue('A'.$l,$type);
	//Sauter une ligne
	$l++;
	$sheet->setCellValue('A'.$l,'N°Immo');
	$sheet->setCellValue('B'.$l,'Fournisseur');
	$sheet->setCellValue('C'.$l,'Modèle');
	$sheet->setCellValue('D'.$l,'N°série');
	$entete = explode("-", $entete);
	$colonne = 'E';
	foreach ($entete as $col) {
		$sheet->setCellValue($colonne.$l,$col.' '.$unite);
		$colonne ++;
	}
	$sheet->setCellValue($colonne .$l,'Étalonnage');
	$colonne ++;
	$sheet->setCellValue($colonne .$l,'Prochain étalonnage');
	$colonne ++;
	$sheet->setCellValue($colonne .$l,'Localisation');
	$colonne ++;
	$sheet->setCellValue($colonne .$l,'Statut');
}

/*Affichage des en-tête du fichier Excel pour les bobines et les couplemètres
* @param
* sheet : feuille de calcul
* type : type de capteur
* unite : unité de la sensibilité
*/
function affichageEnTeteBobineCouplemetre ($sheet, $nom, $unite, $type)
{
	
	$sheet->mergeCells('A1:C1');
	$sheet->setCellValue('A1',$nom);
	$sheet->setCellValue('A2','N°Immo');
	$sheet->setCellValue('B2','Fournisseur');
	$sheet->setCellValue('C2','Modèle');
	$sheet->setCellValue('D2','N°série');
	//Si le capteur n'est pas une bobine -> c'est un couplemètre
	if ($type != "Bobines") $sheet->setCellValue('E2','Plage de mesure min '.$unite);
	else $sheet->setCellValue('E2','Actual Sensibilité '.$unite);
	//Affichage de l'en-tete des couplemètres
	if ($type != "Bobines") 
	{
		$sheet->setCellValue('F2','Plage de mesure max '.$unite);
		$sheet->setCellValue('G2','Étalonnage');
		$sheet->setCellValue('H2','Prochain étalonnage');
		$sheet->setCellValue('I2','Localisation');
		$sheet->setCellValue('J2','Statut');
	}
	else 
	{
		$sheet->setCellValue('F2','Étalonnage');
		$sheet->setCellValue('G2','Prochain étalonnage');
		$sheet->setCellValue('H2','Localisation');
		$sheet->setCellValue('I2','Statut');
	}
}

/*Affichage des en-tête pour l'historique des sensibilités du fichier Excel
* @param
* excel : tableur
* sheet : feuille de calcul
* @return
* column : dernière colonne qui dispose d'un contenu
*/
function affichageEnTeteHisto ($excel, $sheet, $entete)
{
	//L'historique ne peut pas être antérieur à 2013
	$annee_debut = 2013;
	//Récupération de l'année actuelle
	$annee_en_cours = date("Y");
	//Colonne de départ
	if ($entete != "")
	{
		$column = "I";
		$entete = explode("-", $entete);
		foreach ($entete as $col) {
			$column ++;
		}

	}else $column = "K";
	//Tant que l'année actuelle n'est pas atteinte
	while ($annee_debut <= $annee_en_cours)
	{	
		//Décalage de deux colonnes (1 colonne pour la valeur de la sensibilité et l'autre pour la date)	
		$column++;
		$column++;
		//Ajout de l'en-tête
		$sheet->setCellValue($column.'2',$annee_debut);
		//Ajout de la mise en forme
		$excel->getActiveSheet()->getStyle($column.'2')->applyFromArray(getStyleBordTitre());
		$annee_debut ++;
	}
	return $column;
}

/*Style de bordure simple
* @return
* array
*/
function getStyleBordTab()
{
	return array(
	  'borders' => array(
	    'allborders' => array(
	      'style' => PHPExcel_Style_Border::BORDER_THIN
	    )
	  )
	);
}

/*Style de bordure pour les titre (en gras)
* @return
* array
*/
function getStyleBordTitre ()
{
	return array(
	  'borders' => array(
	    'allborders' => array(
	      'style' => PHPExcel_Style_Border::BORDER_MEDIUM
	    )
	  ),
	  'font'=> array('bold'=> true)
	);
}

/*Style de bordure pour la séparation des capteurs
* @return
* array
*/
function getStyleBordSeparation ()
{
	return array(
	  'borders' => array(
	    'bottom' => array(
	      'style' => PHPExcel_Style_Border::BORDER_MEDIUM)
	  )
	);
}

function getTextFormat ()
{
	return array(
		'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
	);
}

/*Remplissage du fichier avec les capteurs et les historiques de sensibilités associés
* @param
* excel : tableau
* sheet : feuille de calcul
* nb : nombre de capteur de ce type
* tab : tableau contenant les capteurs
* tab_histo : tableau contenant l'historique des capteurs
* l : ligne de départ
* type : type du capteur
*/
function remplissageMonoAxe ($excel, $sheet, $nb, $tab, $tab_histo, $l, $type, $champ)
{
	//Pour chaque capteur de ce type
	for($i=0;$i<$nb;$i++)
	{
		//Remplissage la feuille de calcul
		remplissageCapteur($sheet, $l, $tab[$i], $type, $champ);
		//Initialisation de la colonne de départ pour l'historique des sensibilités
		$col = "L"; 
		if ($type == "Autre")
		{
			$col = "J";
			$entete = explode("-", $champ);
			foreach ($entete as $colonne) {
				$col ++;
			}
		}
		if ($type == "Couplemètre" ) remplissageHistoCapteur ($excel, $sheet, $tab, $tab_histo, $col, $l, "couplemètre", $i, $tab[$i]);
		else remplissageHistoCapteur ($excel, $sheet, $tab, $tab_histo, $col, $l, "null", $i, $tab[$i], $champ);
		//incrémentation de la ligne
		$l++;
	}
}

/*Remplissage du fichier avec les capteurs et les historiques de sensibilités associés
* @param
* excel : tableau
* sheet : feuille de calcul
* nb : nombre de capteur de ce type
* tab : tableau contenant les capteurs
* tab_histo : tableau contenant l'historique des capteurs
* column : dernière colonne qui dispose d'un contenu
* l : ligne de départ
* type : type du capteur
* Semblable à remplissageMonoAxe mais avec 3 sensibilités 
*/
function remplissageTriAxe ($excel, $sheet, $nb, $tab, $tab_histo, $column, $l)
{
	//Pour chaque capteur de ce type
	for($i=0;$i<$nb;$i++)
	{	
		//Remplissage de l'axe X
		$sheet->setCellValue('E'.$l,$tab[$i]->axeX);
		//Remplissage de la sensibilité X
		$sheet->setCellValue('F'.$l,str_replace(",", ".", $tab[$i]->sensiX));
		$l++;
		//Remplissage la feuille de calcul
		remplissageCapteur($sheet, $l, $tab[$i], "Triaxes", "");
		$l++;
		//Remplissage de l'axe Z
		$sheet->setCellValue('E'.$l,$tab[$i]->axeZ);
		//Remplissage de la sensibilité Z
		$sheet->setCellValue('F'.$l, str_replace(",", ".", $tab[$i]->sensiZ));
		//Ajout de la mise en forme pour séparer les différents capteurs
		$excel->getActiveSheet()->getStyle('A'.$l.':J'.$l)->applyFromArray(getStyleBordSeparation());
		//Initialisation de la colonne de départ pour l'historique des sensibilités
		$col = "L"; 
		//Selection de la ligne
		remplissageHistoCapteur ($excel, $sheet, $tab, $tab_histo, $col, $l-2, $column, $i, $tab[$i]);
		$l++;
	}	
}

/*Mise en forme du tableau Excel
* @param
* excel : tableur
* column : colonne limite pour l'application de la mise en forme
* l : ligne de départ
* type : type de capteur
*/
function miseEnForme ($excel, $column, $l, $type, $entete)
{
	//Si le capteur n'est pas de type Bobine
	if ($type != "Bobines")
	{
		//Colonne de fin du tableau des capteurs = 'J'
		$colonne = 'J';
		if ($type == "Autre")
		{
			$colonne = 'H';
			$entete = explode("-", $entete);
			foreach ($entete as $col) {
				$colonne ++;
			}
		}
	//Sinon colonne de fin du tableau des capteurs = 'I' (Les feuilles de calculs des bobines disposent d'une colonne de moins que les autres)
	}else $colonne = 'I';
	//Mise en forme du tableau des capteurs (standard)
	$excel->getActiveSheet()->getStyle('A3:'.$colonne.$l)->applyFromArray(getStyleBordTab());
	//Mise en forme des titres (en gras)
	$excel->getActiveSheet()->getStyle('A2:'.$colonne.'2')->applyFromArray(getStyleBordTitre());
	$colonne++;
	//Ajustement de la taille des colonnes pour le tableau des capteurs
	for($col = 'A'; $col !== $colonne; $col++)
	{
		$excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
	}
	$colonne++;
	if ($type == "Bobines") $colonne++;
	//Mise en forme du tableau de l'historique (standard)
	$excel->getActiveSheet()->getStyle($colonne.'3:'.$column.$l)->applyFromArray(getStyleBordTab());
	$column++;
	//Ajustement de la taille des colonnes pour le tableau de l'historique des sensibilités
	for($col = $colonne; $col !== $column; $col++)
	{
		$excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
	}
	$excel->getActiveSheet()->getStyle('A1:'.$column.$l)->getNumberFormat()->applyFromArray(getTextFormat());
	
}

/*Initialisation de la feuille de calcul
* @param
* excel : tableur
* num : numéro de la feuille de calcul
* titre : titre de la feuille de calcul
* @return
* sheet : feuille de calcul
*/
function initFeuille ($excel, $num, $titre)
{
	//Création de la feuille de calcul
	$excel->createSheet($num);
	//Activation de la feuille de calcul
	$excel->setActiveSheetIndex($num);
	$sheet = $excel->getActiveSheet();
	//Ajout d'un titre à la feuille de calcul
	$sheet->setTitle($titre);
	return $sheet;
}

/*Remplissage du tableau des capteurs
* @param
* sheet : feuille de calcul
* l : ligne de départ
* lg : objet capteur extrait de la base de données
* type : type de capteur 
*/
function remplissageCapteur ($sheet, $l, $lg, $type, $champ)
{
	//Colonne de départ 'A'
	$col = 'A';
	//Ajout du numéro d'instrument du capteur
	$sheet->setCellValue($col.$l,$lg->numInstru);
	$col++;
	//Ajout de la marque du capteur
	$sheet->setCellValue($col.$l,$lg->marque);
	$col++;
	//Ajout du modele du capteur
	$sheet->setCellValue($col.$l,$lg->modele);
	$col++;
	//Ajout du numéro de série du capteur
	$sheet->setCellValue($col.$l,$lg->numSerie);
	$col++;
	//Si le capteur n'est pas une Bobine
	if ($type != "Bobines")
	{	
		//Si le capteur est un Triaxes
		if ($type == "Triaxes")
		{
			//(L'axe X et la sensibilité X du capteur a été ajouté précédement dans remplissageTriAxe)
			//Ajout de l'axe Y du capteur
			$sheet->setCellValue($col.$l,$lg->axeY);
			$col++;
			//Ajout de la sensibilité Y du capteur
			$sheet->setCellValue($col.$l,str_replace(",", ".", $lg->sensiY));
		//Si le capteur est un monoAxe
		}elseif ($type == "Monoaxe")
		{
			//Ajout de l'axe X du capteur
			$sheet->setCellValue($col.$l,$lg->axeX);
			$col++;
			//Ajout de la sensibilité X du capteur
			$sheet->setCellValue($col.$l,str_replace(",", ".", $lg->sensiX));
		//Sinon c'est un couplemètre
		}elseif ($type == "Autre")
		{
			$champs = explode("-", $champ);
			foreach ($champs as $champ) {
				$sheet->setCellValue($col.$l,$lg->$champ);
				$col++;
			}
			$col--;
		}
		else 
		{
			//Ajout de la sensibilité X du capteur
			$sheet->setCellValue($col.$l,str_replace(",", ".", $lg->sensiX));
			$col++;
			//Ajout de la sensibilité Y du capteur 
			$sheet->setCellValue($col.$l,str_replace(",", ".", $lg->sensiY));
		}
	//Sinon Ajout de la sensibilité X du capteur
	}else $sheet->setCellValue($col.$l,str_replace(",", ".", $lg->sensiX));
	if ($type != "Autre") $col++;
	//Ajout de la date de la dernière calibration du capteur
	if($lg->date_derniereInt!="") $sheet->setCellValue($col.$l,dateSQLToFr($lg->date_derniereInt));
	$col++;
	//Ajout de la date de la future calibration du capteur
	if($lg->date_futureInt!="") $sheet->setCellValue($col.$l,dateSQLToFr($lg->date_futureInt));
	$col++;
	//Ajout de la date de la localisation du capteur
	$sheet->setCellValue($col.$l,$lg->nomLocal);
	$col++;
	//Ajout du statut du capteur
	$sheet->setCellValue($col.$l,$lg->nomStatut);
}

/*Remplissage du tableau de l'historique des capteurs
* @param
* excel : tableur
* sheet : feuille de calcul
* tab : tableau contenant les capteurs
* tab_histo : tableau contenant l'historique des capteurs
* col : colonne du tableau
* l : ligne de départ
* column : dernière colonne qui dispose d'un contenu
* i : indice
*/
function remplissageHistoCapteur ($excel, $sheet, $tab, $tab_histo, $col, $l, $column, $i, $lg, $champ="")
{
	//L'historique ne peut pas être antérieur à 2013
	$annee_debut = 2013;
	//Si le numéro d'instrument existe
	if (isset ($tab_histo[$tab[$i]->numInstru]))
	{
		//Pour chaque sensibilités présentent dans l'historique
		for ($cpt=0; $cpt<count($tab_histo[$tab[$i]->numInstru]); $cpt++)
		{	
			//Récupération de l'année d'ajout à l'historique		
			$annee_histo = explode ("-", $tab_histo[$tab[$i]->numInstru][$cpt][0])[0];
			//Si l'année correspond à l'année de la feuille de calcul (Si l'on est dans le bonne colonne)
			if ($annee_debut == $annee_histo)
			{	
				//Ajout de la sensibilité (X) et (Y) pour les couplemètres
				if ($column == "couplemètre") $sheet->setCellValue($col.$l,str_replace(",", ".", $tab_histo[$tab[$i]->numInstru][$cpt][1])."-".str_replace(",", ".", $tab_histo[$tab[$i]->numInstru][$cpt][2]));
				elseif ($champ != "")
				{
					$champs = explode("-", $champ);
					$res = "";
					foreach ($champs as $champ) { //Parcours des champs concernés par le capteur
						$res .= $lg->$champ."-";
					}
					$res = substr($res,0,-1); //Suppression du dernier tiret
					$sheet->setCellValue($col.$l,$res);
				}
				//Ajout de la sensibilité (X)
				else $sheet->setCellValue($col.$l,str_replace(",", ".", $tab_histo[$tab[$i]->numInstru][$cpt][1]));
				if ($column != "null" && $column != "couplemètre" && $champ == "")
				{
					$l++;
					//Ajout de la sensibilité Y
					$sheet->setCellValue($col.$l,str_replace(",", ".", $tab_histo[$tab[$i]->numInstru][$cpt][2]));
					$l++;
					//Ajout de la sensibilité Z
					$sheet->setCellValue($col.$l,str_replace(",", ".", $tab_histo[$tab[$i]->numInstru][$cpt][3]));
					
					$l -= 1;
				}			
				$col++;
				//Ajout de la date
				$sheet->setCellValue($col.$l,dateSQLToFr($tab_histo[$tab[$i]->numInstru][$cpt][0]));
				if ($column != "null" && $column != "couplemètre")
				{
					$l += 1;
					//Ajout de la mise en forme pour la séparation des capteurs
					$excel->getActiveSheet()->getStyle('L'.$l.':'.$column.$l)->applyFromArray(getStyleBordSeparation());
					$l -= 2;
				}
				$col++;		
			}else 
			{
				//Sinon passage à l'année suivante dans la feuille de calcul
				$col++;
				$col++;
				//Ne pas incrémenter sinon perte de la valeur
				$cpt --;
			}
			
			//Si la date de début est supérieur à l'année en cours -> exit
			if ($annee_debut > date("Y")) return;
			//Incrémentation de l'année
			$annee_debut += 1;
		}
	}
}

/*Affichage des capteur dans la feuille de calcul
* @param
* excel : tableur
* num : numéro de la feuille de calcul
* nom : titre de la feuille de calcul
* unite : unité de la sensibilité
* cpt : nombre de capteur de ce type
* tab : tableau contenant les capteurs
* tab_histo : tableau contenant l'historique des capteurs
* taille : nombre de ligne à couvrir
* type : type de capteur 
*/
function affichageCapteurs($excel, $num, $nom, $unite, $cpt, $tab, $tab_histo, $taille, $type, $entete="", $champ="")
{
	//Initialisation de la feuille
	$sheet = initFeuille($excel, $num, $nom);
	//Si le capteur est une Bobine ou un Couplemètre
	if ($type == "Bobines" || $type == "Couplemètre") affichageEnTeteBobineCouplemetre($sheet, $nom, $unite, $type);
	//Sinon affichage en-tête standard
	else if ($type == "Autre") affichageEnTeteAutre($sheet, $nom, $unite, 1, $entete);
	else affichageEnTete($sheet, $nom, $unite);
	//Affichage de l'en-tête pour l'historique de sensibilités des capteurs
	$column = affichageEnTeteHisto($excel, $sheet, $entete);
	//Mise en forme de tableau
	miseEnForme ($excel, $column, $taille, $type, $entete);
	//Si le capteur est un Triaxes
	if ($type == "Triaxes" ) remplissageTriAxe ($excel, $sheet, $cpt, $tab, $tab_histo, $column, 3);
	//Remplissage du tableau pour les monoaxe
	else remplissageMonoAxe ($excel, $sheet, $cpt, $tab, $tab_histo, 3, $type, $champ);
}

//Selection des instruments
$str = "SELECT numInstru, nomDes, nomStatut, numSerie, marque, modele, date_futureInt, nomLocal 
FROM statut s, instrument_vib iv, instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
WHERE i.idStatut_statut=s.idStatut
and i.numInstru = iv.numInstru_instrument;";
$req=mysqli_query($bdd,$str);

//Selection des capteurs
$str = "SELECT iv.axeX, iv.sensiX, iv.axeY, iv.sensiY, iv.axeZ, iv.sensiZ,idTypeC_typeCapteur, nomTypeC, numInstru, nomStatut, numSerie, marque, modele, date_derniereInt,date_futureInt, nomLocal, nomUnite 
FROM statut s, instrument_vib_capteur iv, typecapteur, unite u, instrument i 
LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal 
LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes 
WHERE i.idStatut_statut=s.idStatut 
and i.idStatut_statut != 4 
and idTypeC_typeCapteur = idTypeC 
and `idUnite_unite`=u.idUnite and i.numInstru = iv.numInstru_instrument 
ORDER BY idTypeC_typeCapteur";
$reqCapteur=mysqli_query($bdd,$str);

//Selection de l'historique
$str = "SELECT distinct(date_histo), numInstru_instrument, h.sensiX as XHisto, h.sensiY as YHisto, h.sensiZ as ZHisto 
FROM histo_vib_capteur h, instrument_vib_capteur 
WHERE idInstruCapt = idInstruCapt_instrument_vib_capteur 
ORDER BY numInstru_instrument, date_histo;";
$req_histo = mysqli_query($bdd, $str);

//Initialisation du tableau de l'historique
$tab_histo = array();
$numInstru = "";
//Initialisation des années
$annee = "2013";
$annee_act = "2012";
//Tant qu'il y a des capteurs
while($lg=mysqli_fetch_object($req_histo))
{	
	$histo_annee = array();
	//Si l'instrument courant est différent de l'élement précédent
	if ($lg->numInstru_instrument != $numInstru)
	{	
		//Modification du numInstru	
		$numInstru = $lg->numInstru_instrument;
		//Décomposition de l'année
		$annee = explode("-",$lg->date_histo)[0];
		//Ajout dans le tableau de l'historique
		array_push($histo_annee, $lg->date_histo);
		array_push($histo_annee, $lg->XHisto);
		array_push($histo_annee, $lg->YHisto);
		array_push($histo_annee, $lg->ZHisto);
		$tab_histo[$numInstru] = array();
		array_push ($tab_histo[$numInstru], $histo_annee);
	//Si ils sont identiques
	}else 
	{	
		//POur gérer le fait que dans une même année la sensibilité peut être modifier plusieurs fois (dans ce cas selection de la dernière de l'année (pop))
		$annee_prec = $annee_act;
		$annee_act = explode("-",$lg->date_histo)[0];
		if ($annee_prec == $annee_act)
		{			
			array_pop ($tab_histo[$numInstru]);
		}
		//Ajout dans le tableau de l'historique
		array_push($histo_annee, $lg->date_histo);
		array_push($histo_annee, $lg->XHisto);
		array_push($histo_annee, $lg->YHisto);
		array_push($histo_annee, $lg->ZHisto);
		array_push ($tab_histo[$numInstru], $histo_annee);	
	}	
}

$excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//Choix feuille
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();
$sheet->setTitle('Instruments');

//Remplissage des en-têtes pour les instruments
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

//Initialisation de la ligne
$i=3;
//Remplissage des cellules pour les instruments
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

//Mise en forme du tableau
$excel->getActiveSheet()->getStyle('A2:H2')->applyFromArray(getStyleBordTitre());
$excel->getActiveSheet()->getStyle('A3:H'.($i-1))->applyFromArray(getStyleBordTab());
for($col = 'A'; $col !== 'I'; $col++) 
{
    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}

$idCapteur = 1;
$tab = array();
$cpt = 0;
$nomTypeC = "";
$nomUnite = "";
$l=0;	
//tant qu'il y a des capteurs
while ($lg =mysqli_fetch_object($reqCapteur))
{	
	//Si le capteur est d'un autre type que le précedent

	if ($idCapteur != $lg->idTypeC_typeCapteur)
	{	
		
		if($idCapteur==1)
		{			
			//pilotes
			affichageCapteurs($excel, 1, "Pilotes", "mV/g", $cpt, $tab, $tab_histo, $cpt+2, "Monoaxe");
			
		}elseif($idCapteur==2)
		{			
			//Monoaxes
			affichageCapteurs($excel, 2, "Monoaxes", "mV/g", $cpt, $tab, $tab_histo, $cpt+2, "Monoaxe");
			
		}elseif($idCapteur==3)
		{			
			//Triaxes
			affichageCapteurs($excel, 3, "Triaxes", "mV/g", $cpt, $tab, $tab_histo, $cpt*3+2, "Triaxes");
			
		}elseif($idCapteur==4)
		{			
			//Couple
			affichageCapteurs($excel, 4, "Couples", "mV/Nm", $cpt, $tab, $tab_histo, $cpt*3+2, "Triaxes");
			
		}elseif($idCapteur==5)
		{			
			//Cellule force monoaxe
			affichageCapteurs($excel, 5, "Cellules Force monoaxe", "pC/N", $cpt, $tab, $tab_histo, $cpt+2, "Monoaxe");
			
		}elseif($idCapteur==6)
		{			
			//Cellule force triaxe
			affichageCapteurs($excel, 6, "Cellules Force triaxes", "pC/N", $cpt, $tab, $tab_histo, $cpt*3+2, "Triaxes");

		}elseif($idCapteur==7)
		{			
			//Microphone
			affichageCapteurs($excel, 7, "Microphones", "mV/g", $cpt, $tab, $tab_histo, $cpt+2, "Monoaxe");
			
		}elseif($idCapteur==8)
		{			
			//Choc
			affichageCapteurs($excel, 7, "Chocs Monoaxes", "mV/g", $cpt, $tab, $tab_histo, $cpt+2, "Monoaxe");
			
		}elseif($idCapteur==9)
		{			
			//Choc triaxes
			affichageCapteurs($excel, 7, "Chocs Triaxes", "mV/g", $cpt, $tab, $tab_histo, $cpt*3+2, "Triaxes");

		}elseif($idCapteur==10)
		{			
			//Bobine Tension
			affichageCapteurs($excel, 10, "Bobines tensions", "mV/V", $cpt, $tab, $tab_histo, $cpt+2, "Bobines");

		}elseif($idCapteur==11)
		{			
			//Bobine Courant
			affichageCapteurs($excel, 11, "Bobines courant", "mV/A", $cpt, $tab, $tab_histo, $cpt+2, "Bobines");
		}elseif($idCapteur==12) 
		{
			affichageCapteurs($excel, 12, "Couplemètre", $lg->nomUnite, $cpt, $tab, $tab_histo, $cpt+2, "Couplemètre");

		}else
		{
			$str = "SELECT libelle, colonne FROM typecapteur WHERE idTypeC = $idCapteur";
			$req = mysqli_query($bdd, $str);
			$lg_type = mysqli_fetch_object($req);
			$entete = $lg_type->libelle;
			$champ = $lg_type->colonne;
			//Bobine Courant
			affichageCapteurs($excel, $idCapteur, $nomTypeC, $nomUnite, $cpt, $tab, $tab_histo, $cpt+2, "Autre", $entete, $champ);
		}
		
		//Changement des variables
		$nomTypeC = $lg->nomTypeC;
		$nomUnite = $lg->nomUnite;
		//Modification du type de capteur précedent
		$idCapteur = $lg->idTypeC_typeCapteur;
		//Réinitilisation du tableau contenant tous les capteurs de même type
		$tab = array();
		//Réinitilisation du compteur
		$cpt = 0;
		//Ajout du capteur en question dans le tableau
		$tab[$cpt] = $lg;
		//Incrémentation du nombre
		$cpt ++;

	//Sinon
	}else 
	{	
		//Remplissage d'un tableau avec tous les capteurs de même type
		$tab[$cpt] = $lg;
		//Incrémentation du nombre
		$cpt ++;
	}
}

//Dernière boucle pour les derniers capteurs
if($idCapteur>12){

	$str = "SELECT libelle, colonne FROM typecapteur WHERE nomTypeC = '$nomTypeC'";
	$req = mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	$entete = $lg->libelle;
	$champ = $lg->colonne;
	affichageCapteurs($excel, $idCapteur, $nomTypeC, $nomUnite, $cpt, $tab, $tab_histo, $cpt+2, "Autre", $entete, $champ);
} 
//Syntaxe pour exporter le fichier
$excel->setActiveSheetIndex(0);
$writer = new PHPExcel_Writer_Excel2007($excel);
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition:inline;filename=Fichier.xlsx ');
$writer->save('php://output');