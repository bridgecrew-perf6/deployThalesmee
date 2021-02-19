<?php
require('../conf/connexion_param.php'); 
require("top.php");
require("bottom.php");
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../css/addons/datatables.min.css" rel="stylesheet">
<link href="../css/styleTable.css" rel="stylesheet">
<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
<script type="text/javascript" src="../js/table.js"></script>


<?php


if (isset($_GET['type'])){
	
	$type = $_GET['type'];
	$str="Select nomEtat, nomDes, marque, modele, numSerie FROM etat, instrument, typecapteur, instrument_vib_capteur, designation WHERE idTypeC_typecapteur = idTypeC and numInstru_instrument = numInstru and idDes_designation = idDes and idEtat_etat = idEtat and nomTypeC = '$type' group by modele";
	$reqMoyen=mysqli_query($bdd, $str);
	
	echo '<div class="container">';
	echo '<div class="jumbotron" style="margin-top:50px"><h3>Tableau récapitulatif de type : '.$_GET['type'].'</h3></div>';
	echo '<div class="jumbotron" style="margin-top:50px">';
	echo '<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Type</th><th class="th-sm" scope="col">Marque</th><th class="th-sm" scope="col">Référence</th>
	<th class="th-sm" scope="col">Total</th><th class="th-sm"  scope="col">En Service</th><th class="th-sm" scope="col">Calibration</th><th class="th-sm" scope="col">Prêt Cannes</th><th class="th-sm" scope="col">Prêt Intespace</th><th class="th-sm" scope="col">Prêt Mecano</th><th class="th-sm" scope="col">Rebut</th></thead><tbody>';
	
				

	$premier = true;
	
	while($lg=mysqli_fetch_object($reqMoyen))
	{
		
		$mod = $lg->modele;
		$strEnService="SELECT count(numInstru) as service FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqEnService=mysqli_query($bdd, $strEnService);
		$lgEnService=mysqli_fetch_object($reqEnService);
		$titreEnService = "";
		if ($lgEnService -> service != 0){
			
			$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
			
		}
		
		
		$strTotal="SELECT count(numInstru) as total FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqTotal=mysqli_query($bdd, $strTotal);
		$lgTotal=mysqli_fetch_object($reqTotal);
		$titreTotal = "";
		if ($lgTotal -> total != 0){
			
			$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
			
		}

		$strCalib="SELECT count(numInstru) as calib FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Calibration' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqCalib=mysqli_query($bdd, $strCalib);
		$lgCalib=mysqli_fetch_object($reqCalib);
		$titreCalib = "";
		if ($lgCalib -> calib != 0){
			
			$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
			
		}
		
		$strCannes="SELECT count(numInstru) as cannes FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Prêt Cannes' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqCannes=mysqli_query($bdd, $strCannes);
		$lgCannes=mysqli_fetch_object($reqCannes);
		$titreCannes = "";
		if ($lgCannes -> cannes != 0){
			
			$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
			
		}
					
		$strIntespace="SELECT count(numInstru) as intespace FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Prêt Intespace' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqIntespace=mysqli_query($bdd, $strIntespace);
		$lgIntespace=mysqli_fetch_object($reqIntespace);
		$titreIntespace = "";
		if ($lgIntespace -> intespace != 0){
			
			$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
			
		}
					
		$strMecano="SELECT count(numInstru) as mecano FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Prêt Mécano' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqMecano=mysqli_query($bdd, $strMecano);
		$lgMecano=mysqli_fetch_object($reqMecano);
		$titreMecano = "";
		if ($lgMecano -> mecano != 0){
			
			$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
			
		}
					
		$strRebut="SELECT count(numInstru) as rebut FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'REBUT' and nomTypeC = '$type' and idLocal_Localisation = idLocal;";
		$reqRebut=mysqli_query($bdd, $strRebut);
		$lgRebut=mysqli_fetch_object($reqRebut);
		$titreRebut = "";
		if ($lgRebut -> rebut != 0){
			
			$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and nomTypeC = '$type' and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
			
		}

		$nomDes = $lg->nomDes;
		$marque = $lg->marque;
		$modele = $lg->modele;
		$numSerie = $lg->numSerie;
		
		$tot_service = $lgEnService->service;
		$tot_calib = $lgCalib->calib;
		$tot_cannes = $lgCannes->cannes;
		$tot_intespace = $lgIntespace->intespace;
		$tot_mecano = $lgMecano->mecano;
		$tot_rebut = $lgRebut->rebut;
		$tot_total = $lgTotal->total;
		
		$Service = $titreEnService;
		$Calib = $titreCalib;
		$Cannes = $titreCannes;
		$Intespace = $titreIntespace;
		$Mecano = $titreMecano;
		$Rebut = $titreRebut;
		$Total = $titreTotal;
		$cpt = 0;
		$premier = false;

			/*$EnService = $cpt - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
			if ($EnService < 0) {
						
				$EnService = 0;
			}*/
		echo '<tr scope="row"><td>'.$nomDes.'</td><td scope="row">'.$marque.'</td><td scope="row">'.$modele.'</td><td scope="row"><div title='.$Total.'>'.$tot_total.'</div></td><td><div title='.$Service.'>'.$tot_service.'</div></td><td><div title='.$Calib.'>'.$tot_calib.'</div></td><td><div title='.$Cannes.'>'.$tot_cannes.'</div></td><td><div title='.$Intespace.'>'.$tot_intespace.'</div></td><td><div title='.$Mecano.'>'.$tot_mecano.'</div></td><td><div title='.$Rebut.'>'.$tot_rebut.'</div></td></tr>';


						
	}
	echo '</tbody></table>';
	echo '</div>';
	echo '</div>';
	echo '<div class="text-center"><a type="button" href="tableau_recap.php" class="btn btn-primary btn-lg" value="Retour">Retour</a></div>;';
	
	
}else{
	
	if (isset($_POST['ref'])){
		
		echo '<div class="container">';
		echo '<div class="jumbotron" style="margin-top:50px"><h3>Tableau récapitulatif de tous les capteurs</h3></div>';
		?>
	<div class="jumbotron">
	<div class="row">
	<div class="col-md-3"><button type="button" class="btn btn-block btn-lg btn-info" data-toggle="collapse" data-target="#filtre">Filtrer</button></div>
	<div class="col-md-3"><a class="btn btn-block btn-lg btn-warning" href="cherche_capteurs.php">Réinitialiser filtre</a></div>
	<div class="col-md-3"><a id="export" href="exportExcel_cherche_capteurs.php" class="btn btn-block btn-info btn-lg" role="button">Exporter au format excel</a></div>
	<div class="col-md-3"><a type="button" href="tableau_recap.php" class="btn btn-block btn-primary btn-lg" value="Retour">Retour</a></div>
		</div>
	</div>
		<div id="filtre" class="collapse">
			<div class="jumbotron">
			<div class="row">
			<form method="post" action="cherche_capteurs.php">
				
					<div class="col-md-8" style="margin-top:20px">
						<input class="form-control" type="text" placeholder=<?php if ($_POST['ref'] != ""){ echo $_POST['ref']; }else{ echo "Rechercher un référence";}?> id="ref" name="ref" >
					</div>
					<div style="margin-top:10px" class="col-md-4"><input class="btn btn-block btn-lg btn-primary" type="submit" value="Valider" /></div>
			</form>
			
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="DESC"){ echo "disabled";}}  ?>" type="button" value="Tri par ordre décroissant des références" onclick= "document.location.href = 'cherche_capteurs.php?DESC=DESC'" /></div>
		
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="ASC"){ echo "disabled";}} ?>" type="button" value="Tri par ordre croissant des références" onclick= "document.location.href = 'cherche_capteurs.php?DESC=ASC'" /></div>
				
				</div>
		</div>
	</div><?php
		echo '<div class="jumbotron">';
		echo '<table class="table table-bordered"><thead><tr style="background-color:#5bc0de;""><th>Type</th><th>Marque</th><th>Référence</th><th>Total</th><th>En Service</th><th>Calibration</th><th>Prêt Cannes</th><th>Prêt Intespace</th><th>Prêt Mécano</th><th>Rebut</th></tr></thead><tbody>';
		$ref = $_POST['ref'];
		$mod = $_POST['ref'];
		$strEnService="SELECT count(numInstru) as service FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and idLocal_Localisation = idLocal;";
		$reqEnService=mysqli_query($bdd, $strEnService);
		$lgEnService=mysqli_fetch_object($reqEnService);
		$titreEnService = "";
		if ($lgEnService -> service != 0){
			
			$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
			
		}
		
		$strTotal="SELECT count(numInstru) as total FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and idLocal_Localisation = idLocal;";
		$reqTotal=mysqli_query($bdd, $strTotal);
		$lgTotal=mysqli_fetch_object($reqTotal);
		$titreTotal = "";
		if ($lgTotal -> total != 0){
			
			$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
			
		}
						
		$strCalib="SELECT count(numInstru) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele ='$ref' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
		$reqCalib=mysqli_query($bdd, $strCalib);
		$lgCalib=mysqli_fetch_object($reqCalib);
		$titreCalib = "";
		if ($lgCalib -> calib != 0){
			
			$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
			
		}
					
		$strCannes="SELECT count(numInstru) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$ref' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
		$reqCannes=mysqli_query($bdd, $strCannes);
		$lgCannes=mysqli_fetch_object($reqCannes);
		$titreCannes = "";
		if ($lgCannes -> cannes != 0){
			
			$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
			
		}
					
		$strIntespace="SELECT count(numInstru) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$ref' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
		$reqIntespace=mysqli_query($bdd, $strIntespace);
		$lgIntespace=mysqli_fetch_object($reqIntespace);
		$titreIntespace = "";
		if ($lgIntespace -> intespace != 0){
			
			$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
			
		}
					
		$strMecano="SELECT count(numInstru) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$ref' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
		$reqMecano=mysqli_query($bdd, $strMecano);
		$lgMecano=mysqli_fetch_object($reqMecano);
		$titreMecano = "";
		if ($lgMecano -> mecano != 0){
			
			$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
			
		}
					
		$strRebut="SELECT count(numInstru) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$ref' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
		$reqRebut=mysqli_query($bdd, $strRebut);
		$lgRebut=mysqli_fetch_object($reqRebut);
		$titreRebut = "";
		if ($lgRebut -> rebut != 0){
			
			$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru  and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
			
		}
		
		$str="SELECT nomTypeC, `marque`, `modele`, count(modele) as total FROM `instrument`, instrument_vib_capteur, typecapteur WHERE `numInstru`= `numInstru_instrument` and modele = '$ref' and `idTypeC_typeCapteur`=idTypeC;" ;
		$req = mysqli_query($bdd,$str);
		$lg=mysqli_fetch_object($req);
		
		$EnService = $lg->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
		if ($EnService < 0) {
				$EnService = 0;
		}
		
		
		if ($lg->total!=0){
			
			
			echo '<tr ><td>'.$lg->nomTypeC.'</td><td>'.$lg->marque.'</td><td>'.$lg->modele.'</td><td><div title='.$titreTotal.'>'.$lg->total.'</div></td><td><div title='.$titreTotal.'>'.$EnService.'</div></td><td><div title='.$titreTotal.'>'.$lgCalib->calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
		}else{
			
			echo '<tr><td colspan="10">Aucune donnée correspondante</td></tr>';
		}
		echo '</tbody></table>';
		echo '</div>';
		echo '<div>';
		
		
	}else{
	

	echo '<div class="container">';
	echo '<div class="jumbotron" style="margin-top:50px"><h3>Tableau récapitulatif de tous les capteurs</h3></div>';?>
	<div class="jumbotron">
	<div class="row">
	<div class="col-md-3"><button type="button" class="btn btn-block btn-lg btn-info" data-toggle="collapse" data-target="#filtre">Filtrer</button></div>
		<div class="col-md-3"><a class="btn btn-block btn-lg btn-warning" href="cherche_capteurs.php">Réinitialiser filtre</a></div>
		<div class="col-md-3"><a id="export" href="exportExcel_cherche_capteurs.php" class="btn btn-block btn-info btn-lg" role="button">Exporter au format excel</a></div>
		<div class="col-md-3"><a type="button" href="tableau_recap.php" class="btn btn-block btn-primary btn-lg" value="Retour">Retour</a></div>
		</div>
	</div>
		<div id="filtre" class="collapse">
			<div class="jumbotron">
			<div class="row">
			<form method="post" action="cherche_capteurs.php">
				
					<div class="col-md-8" style="margin-top:20px">
						<input class="form-control" type="text" placeholder="Rechercher un référence" id="ref" name="ref" >
					</div>
					<div style="margin-top:10px" class="col-md-4"><input class="btn btn-block btn-lg btn-primary" type="submit" value="Valider" /></div>
			</form>
			
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="DESC"){ echo "disabled";}}  ?>" type="button" value="Tri par ordre décroissant des références" onclick= "document.location.href = 'cherche_capteurs.php?DESC=DESC'" /></div>
		
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="ASC"){ echo "disabled";}} ?>" type="button" value="Tri par ordre croissant des références" onclick= "document.location.href = 'cherche_capteurs.php?DESC=ASC'" /></div>
				
				</div>
		</div>
	</div>
	
	<div class="jumbotron">
	<table class="table table-bordered">
		<thead>
			<tr style="background-color:#5bc0de;">
				<th>Type</th>
				<th>Marque</th>
				<th>Référence</th>
				<th>Total</th>
				<th>En Service</th>
				<th>Calibration</th>
				<th>Prêt Cannes</th>
				<th>Prêt Intespace</th>
				<th>Prêt Mecano</th>
				<th>Rebut</th>
			</tr>
		</thead>
	<tbody>
	<?php
	$str="Select idTypeC, nomTypeC FROM typecapteur, instrument_vib_capteur where idTypeC_typecapteur = idTypeC group by idTypeC;" ;
	if(isset($_GET['type'])){
		$type = $_GET['type'];
		$str="Select idTypeC, nomTypeC FROM typecapteur, instrument_vib_capteur where idTypeC_typecapteur = idTypeC and nomTypeC = '$type' group by idTypeC ;" ;
	}
	$reqType=mysqli_query($bdd, $str);
	while($lg=mysqli_fetch_object($reqType))
	{

		$premier = true;
		$exception = true;
		$str="SELECT count(distinct(modele)) as total FROM instrument ,`instrument_vib_capteur` WHERE `idTypeC_typeCapteur`=".$lg->idTypeC." and `numInstru_instrument`=numInstru ;";
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
				if ($premier == true)
				{
				
				
					echo '<tr ><td  rowspan="'.$lgNbLigneType->total.'">'.$lg->nomTypeC.'</td><td rowspan="'.$nbLigneMarque.'">'.$marque.'</td>';
					
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
						//$strTotal = "SELECT count(*) as total FROM `instrument`,`instrument_vib_capteur` WHERE `numInstru_instrument`=numInstru and modele ='$mod'";
						//$reqTotal=mysqli_query($bdd, $strTotal);
						//$lgTotal=mysqli_fetch_object($reqTotal);
						$strEnService="SELECT count(numInstru) as service FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
						$reqEnService=mysqli_query($bdd, $strEnService);
						$lgEnService=mysqli_fetch_object($reqEnService);
						$titreEnService = "";
						if ($lgEnService -> service != 0){
							
							$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
							
						}
						
						$strTotal="SELECT count(numInstru) as total FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
						$reqTotal=mysqli_query($bdd, $strTotal);
						$lgTotal=mysqli_fetch_object($reqTotal);
						$titreTotal = "";
						if ($lgTotal -> total != 0){
							
							$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
							
						}
						
						$strCalib="SELECT count(numInstru) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqCalib=mysqli_query($bdd, $strCalib);
						$lgCalib=mysqli_fetch_object($reqCalib);
						$titreCalib = "";
						if ($lgCalib -> calib != 0){
							
							$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
							
						}
						
						$strCannes="SELECT count(numInstru) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqCannes=mysqli_query($bdd, $strCannes);
						$lgCannes=mysqli_fetch_object($reqCannes);
						$titreCannes = "";
						if ($lgCannes -> cannes != 0){
							
							$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
							
						}
						
						$strIntespace="SELECT count(numInstru) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqIntespace=mysqli_query($bdd, $strIntespace);
						$lgIntespace=mysqli_fetch_object($reqIntespace);
						$titreIntespace = "";
						if ($lgIntespace -> intespace != 0){
							
							$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
							
						}
						
						$strMecano="SELECT count(numInstru) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqMecano=mysqli_query($bdd, $strMecano);
						$lgMecano=mysqli_fetch_object($reqMecano);
						$titreMecano = "";
						if ($lgMecano -> mecano != 0){
							
							$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
							
						}
						
						$strRebut="SELECT count(numInstru) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqRebut=mysqli_query($bdd, $strRebut);
						$lgRebut=mysqli_fetch_object($reqRebut);
						$titreRebut = "";
						if ($lgRebut -> rebut != 0){
							
							$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
							
						}
						
						$EnService = $lgmodele->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
						if ($EnService < 0) {
							
							$EnService = 0;
						}	
						if ($prems==true){
							
							echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
							$prems = false;
						}else{
							echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

						}
				}

				$premier = false;
				}else
				{

					$capteur = $lg->idTypeC;
					echo'<tr><td  rowspan="'.$nbLigneMarque.'">'.$marque.'</td>';
					
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
						$strEnService="SELECT count(numInstru) as service FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
						$reqEnService=mysqli_query($bdd, $strEnService);
						$lgEnService=mysqli_fetch_object($reqEnService);
						$titreEnService = "";
						if ($lgEnService -> service != 0){
							
							$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
							
						}
						
						$strCalib="SELECT count(numInstru) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqCalib=mysqli_query($bdd, $strCalib);
						$lgCalib=mysqli_fetch_object($reqCalib);
						$titreCalib = "";
						if ($lgCalib -> calib != 0){
							
							$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
							
						}
						
						
						$strTotal="SELECT count(numInstru) as total FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
						$reqTotal=mysqli_query($bdd, $strTotal);
						$lgTotal=mysqli_fetch_object($reqTotal);
						$titreTotal = "";
						if ($lgTotal -> total != 0){
							
							$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
							
						}
				
						$strCannes="SELECT count(numInstru) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqCannes=mysqli_query($bdd, $strCannes);
						$lgCannes=mysqli_fetch_object($reqCannes);
						$titreCannes = "";
						if ($lgCannes -> cannes != 0){
							
							$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
							
						}
				
						$strIntespace="SELECT count(numInstru) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqIntespace=mysqli_query($bdd, $strIntespace);
						$lgIntespace=mysqli_fetch_object($reqIntespace);
						$titreIntespace = "";
						if ($lgIntespace -> intespace != 0){
							
							$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
							
						}
				
						$strMecano="SELECT count(numInstru) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqMecano=mysqli_query($bdd, $strMecano);
						$lgMecano=mysqli_fetch_object($reqMecano);
						$titreMecano = "";
						if ($lgMecano -> mecano != 0){
							
							$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
							
						}
				
						$strRebut="SELECT count(numInstru) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
						$reqRebut=mysqli_query($bdd, $strRebut);
						$lgRebut=mysqli_fetch_object($reqRebut);
						$titreRebut = "";
						if ($lgRebut -> rebut != 0){
							
							$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
							$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
							$req2=mysqli_query($bdd, $str2);
							while ($lg2=mysqli_fetch_object($req2)){
								
								$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
							}
							$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
							
						}
				
						$EnService = $lgmodele2->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
						if ($EnService < 0) {
							$EnService = 0;
					
						}
						if ($prems==true){
							
							echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele2->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
							$prems = false;
						}else{
							echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele2->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

						}	
						
					}
				
			}
			
			}else 
			{

				if(!strstr($marque,'PCB') ){

					if ($premier == true){
				
						echo '<tr ><td rowspan="'.$lgNbLigneType->total.'">'.$lg->nomTypeC.'</td><td  rowspan="'.$lgMarque->nbligneMarque.'">'.$marque.'</th>';
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
							$strEnService="SELECT count(numInstru) as service FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
							$reqEnService=mysqli_query($bdd, $strEnService);
							$lgEnService=mysqli_fetch_object($reqEnService);
							$titreEnService = "";
							if ($lgEnService -> service != 0){
								
								$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
								
							}
							
							$strTotal="SELECT count(numInstru) as total FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
							$reqTotal=mysqli_query($bdd, $strTotal);
							$lgTotal=mysqli_fetch_object($reqTotal);
							$titreTotal = "";
							if ($lgTotal -> total != 0){
								
								$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
								
							}
							
							$strCalib="SELECT count(numInstru) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqCalib=mysqli_query($bdd, $strCalib);
							$lgCalib=mysqli_fetch_object($reqCalib);
							$titreCalib = "";
							if ($lgCalib -> calib != 0){
								
								$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
								
							}
							
							$strCannes="SELECT count(numInstru) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqCannes=mysqli_query($bdd, $strCannes);
							$lgCannes=mysqli_fetch_object($reqCannes);
							$titreCannes = "";
							if ($lgCannes -> cannes != 0){
								
								$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
								
							}
							
							$strIntespace="SELECT count(numInstru) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqIntespace=mysqli_query($bdd, $strIntespace);
							$lgIntespace=mysqli_fetch_object($reqIntespace);
							$titreIntespace = "";
							if ($lgIntespace -> intespace != 0){
								
								$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
								
							}
							
							$strMecano="SELECT count(numInstru) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqMecano=mysqli_query($bdd, $strMecano);
							$lgMecano=mysqli_fetch_object($reqMecano);
							$titreMecano = "";
							if ($lgMecano -> mecano != 0){
								
								$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
								
							}
							
							$strRebut="SELECT count(numInstru) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqRebut=mysqli_query($bdd, $strRebut);
							$lgRebut=mysqli_fetch_object($reqRebut);
							$titreRebut = "";
							if ($lgRebut -> rebut != 0){
								
								$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
								
							}
							
							$EnService = $lgmodele->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
							if ($EnService < 0) {
								
								$EnService = 0;
							}	
							if ($prems==true){
								
								echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
								$prems = false;
							}else{
								echo '<tr><td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

							}

						}

						$premier = false;
					}else{
				
						$marque = $lgMarque->nomMarque;
						$capteur = $lg->idTypeC;
						echo'<tr><td  rowspan="'.$lgMarque->nbligneMarque.'">'.$marque.'</td>';
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
							
							$strEnService="SELECT count(numInstru) as service FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
							$reqEnService=mysqli_query($bdd, $strEnService);
							$lgEnService=mysqli_fetch_object($reqEnService);
							$titreEnService = "";
							if ($lgEnService -> service != 0){
								
								$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
								
							}
							
							$strCalib="SELECT count(numInstru) as calib FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqCalib=mysqli_query($bdd, $strCalib);
							$lgCalib=mysqli_fetch_object($reqCalib);
							$titreCalib = "";
							if ($lgCalib -> calib != 0){
								
								$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
								
							}
							
							
							$strTotal="SELECT count(numInstru) as total FROM `instrument`,typecapteur, `instrument_vib_capteur`, localisation WHERE idTypeC_typecapteur = idTypeC and `numInstru_instrument`=numInstru and modele ='$mod' and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal;";
							$reqTotal=mysqli_query($bdd, $strTotal);
							$lgTotal=mysqli_fetch_object($reqTotal);
							$titreTotal = "";
							if ($lgTotal -> total != 0){
								
								$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
								
							}
					
							$strCannes="SELECT count(numInstru) as cannes FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqCannes=mysqli_query($bdd, $strCannes);
							$lgCannes=mysqli_fetch_object($reqCannes);
							$titreCannes = "";
							if ($lgCannes -> cannes != 0){
								
								$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
								
							}
					
							$strIntespace="SELECT count(numInstru) as intespace FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqIntespace=mysqli_query($bdd, $strIntespace);
							$lgIntespace=mysqli_fetch_object($reqIntespace);
							$titreIntespace = "";
							if ($lgIntespace -> intespace != 0){
								
								$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
								
							}
					
							$strMecano="SELECT count(numInstru) as mecano FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqMecano=mysqli_query($bdd, $strMecano);
							$lgMecano=mysqli_fetch_object($reqMecano);
							$titreMecano = "";
							if ($lgMecano -> mecano != 0){
								
								$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
								
							}
					
							$strRebut="SELECT count(numInstru) as rebut FROM `instrument`,`instrument_vib_capteur`, localisation WHERE `numInstru_instrument`=numInstru and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal and `idTypeC_typeCapteur`='$capteur';";
							$reqRebut=mysqli_query($bdd, $strRebut);
							$lgRebut=mysqli_fetch_object($reqRebut);
							$titreRebut = "";
							if ($lgRebut -> rebut != 0){
								
								$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib_capteur, localisation WHERE idTypeC_typecapteur = idTypeC and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and `idTypeC_typeCapteur`='$capteur' and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
								
							}
					
							$EnService = $lgmodele2->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
							if ($EnService < 0) {
								$EnService = 0;
						
							}
							if ($prems==true){
								
								echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele2->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
								$prems = false;
							}else{
								echo '<tr><td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgmodele2->total.'</div></td><td><div title='.$titreEnService.'>'.$EnService.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

							}
					
					
						}
				
					}
			
				}
			}
			
		}
		
		
	}
	
	echo '</tbody></table>';
	echo '</div>';
	echo '<div>';
	}
}


?>
			<script>
	
	$(".form-control").change (function(){
		
		console.log("changer");
		if ($(this).val()==""){
			
			windows.location.href = "cherche_capteurs.php";
		}
	});
	$(document).ready(function() {
    $('#exemple').DataTable( {
        "language": {
    "sProcessing":     "Traitement en cours...",
    "sSearch":         "Rechercher&nbsp;:",
    "sLengthMenu":     "_MENU_ ",
    "sInfo":           "Affichage de l'instrument _START_ &agrave; _END_ sur _TOTAL_ instruments",
    "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    "sInfoPostFix":    "",
    "sLoadingRecords": "Chargement en cours...",
    "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
    "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
    "oPaginate": {
        "sFirst":      "Premier",
        "sPrevious":   "Pr&eacute;c&eacute;dent",
        "sNext":       "Suivant",
        "sLast":       "Dernier"
    },
    "oAria": {
        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
        "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
    },
    "select": {
            "rows": {
                _: "%d lignes séléctionnées",
                0: "Aucune ligne séléctionnée",
                1: "1 ligne séléctionnée"
            } 
    }
}
    } );
} );
</script>




















