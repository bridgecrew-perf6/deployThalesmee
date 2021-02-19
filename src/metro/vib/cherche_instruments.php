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
	
if (isset($_POST['ref'])){
		
		echo '<div class="container-fluid">';
		echo '<div class="jumbotron" style="margin-top:50px"><h3>Tableau récapitulatif des instruments non capteurs</h3></div>';
		?>
	<div class="jumbotron">
	<div class="row">
	<div class="col-md-3"><button type="button" class="btn btn-block btn-lg btn-info" data-toggle="collapse" data-target="#filtre">Filtrer</button></div>
		<div class="col-md-3"><a class="btn btn-block btn-lg btn-warning" href="cherche_instruments.php">Réinitialiser filtre</a></div>
		<div class="col-md-3"><a id="export" href="exportExcel_cherche_instruments.php" class="btn btn-block btn-info btn-lg" role="button">Exporter au format excel</a></div>
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
			
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="DESC"){ echo "disabled";}}  ?>" type="button" value="Tri par ordre décroissant des références" onclick= "document.location.href = 'cherche_instruments.php?DESC=DESC'" /></div>
		
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="ASC"){ echo "disabled";}} ?>" type="button" value="Tri par ordre croissant des références" onclick= "document.location.href = 'cherche_instruments.php?DESC=ASC'" /></div>
				
				</div>
		</div>
	</div><?php
		echo '<div class="jumbotron">';
		echo '<table class="table table-bordered"><thead><tr style="background-color:#5bc0de;""><th>Type</th><th>Marque</th><th>Référence</th><th>Total</th><th>En Service</th><th>Calibration</th><th>Prêt Cannes</th><th>Prêt Intespace</th><th>Prêt Mécano</th><th>Rebut</th></tr></thead><tbody>';
		$ref = $_POST['ref'];
		
		$str="SELECT nomDes, `marque`, `modele`, count(modele) as total FROM `instrument`, designation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$ref' and `idDes_designation`=idDes;" ;
		$req = mysqli_query($bdd,$str);
		$lg=mysqli_fetch_object($req);
		
		$mod = $lg->modele;
		$strEnService="SELECT count(distinct(numInstru)) as service FROM `instrument`, `instrument_vib`, localisation WHERE `numInstru_instrument`=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and idLocal_Localisation = idLocal;";
		$reqEnService=mysqli_query($bdd, $strEnService);
		$lgEnService=mysqli_fetch_object($reqEnService);
		$titreEnService = "";
		if ($lgEnService -> service != 0){
			
			$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, localisation , `instrument_vib` WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
			
		}
		
		$strTotal="SELECT count(distinct(numInstru)) as total FROM `instrument`,typecapteur, `instrument_vib`, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `numInstru_instrument`=numInstru and modele ='$mod' and idLocal_Localisation = idLocal;";
		$reqTotal=mysqli_query($bdd, $strTotal);
		$lgTotal=mysqli_fetch_object($reqTotal);
		$titreTotal = "";
		if ($lgTotal -> total != 0){
			
			$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
			
		}
		
		$strCalib="SELECT count(*) as calib FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele ='$ref' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
		$reqCalib=mysqli_query($bdd, $strCalib);
		$lgCalib=mysqli_fetch_object($reqCalib);
		$titreCalib = "";
		if ($lgCalib -> calib != 0){
			
			$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
			
		}
					
		$strCannes="SELECT count(*) as cannes FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$ref' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
		$reqCannes=mysqli_query($bdd, $strCannes);
		$lgCannes=mysqli_fetch_object($reqCannes);
		$titreCannes = "";
		if ($lgCannes -> cannes != 0){
			
			$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
			
		}
					
		$strIntespace="SELECT count(*) as intespace FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$ref' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
		$reqIntespace=mysqli_query($bdd, $strIntespace);
		$lgIntespace=mysqli_fetch_object($reqIntespace);
		$titreIntespace = "";
		if ($lgIntespace -> intespace != 0){
			
			$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
			
		}
					
		$strMecano="SELECT count(*) as mecano FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$ref' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
		$reqMecano=mysqli_query($bdd, $strMecano);
		$lgMecano=mysqli_fetch_object($reqMecano);
		$titreMecano = "";
		if ($lgMecano -> mecano != 0){
			
			$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
			
		}
					
		$strRebut="SELECT count(*) as rebut FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$ref' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
		$reqRebut=mysqli_query($bdd, $strRebut);
		$lgRebut=mysqli_fetch_object($reqRebut);
		$titreRebut = "";
		if ($lgRebut -> rebut != 0){
			
			$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
			$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
			$req2=mysqli_query($bdd, $str2);
			while ($lg2=mysqli_fetch_object($req2)){
				
				$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
			}
			$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
			
		}

		$EnService = $lg->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
		if ($EnService < 0) {
				$EnService = 0;
		}
		
		
		if ($lg->total!=0){
			
			
			echo '<tr><td>'.$lg->nomDes.'</td><td>'.$lg->marque.'</td><td>'.$lg->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
		}else{
			
			echo '<tr><td colspan="10">Aucune donnée correspondante</td></tr>';
		}
		echo '</tbody></table>';
		echo '</div>';
		echo '<div>';
		
}else{
	

	echo '<div class="container-fluid">';
	echo '<div class="jumbotron" style="margin-top:50px"><h3>Tableau récapitulatif des instruments non capteurs</h3></div>';?>
	<div class="jumbotron">
	<div class="row">
	<div class="col-md-3"><button type="button" class="btn btn-block btn-lg btn-info" data-toggle="collapse" data-target="#filtre">Filtrer</button></div>
		<div class="col-md-3"><a class="btn btn-block btn-lg btn-warning" href="cherche_instruments.php">Réinitialiser filtre</a></div>
		<div class="col-md-3"><a id="export" href="exportExcel_cherche_instruments.php" class="btn btn-block btn-info btn-lg" role="button">Exporter au format excel</a></div>
		<div class="col-md-3"><a type="button" href="tableau_recap.php" class="btn btn-block btn-primary btn-lg" value="Retour">Retour</a></div>
		</div>
	</div>
		<div id="filtre" class="collapse">
			<div class="jumbotron">
			<div class="row">
			<form method="post" action="cherche_instruments.php">
				
					<div class="col-md-8" style="margin-top:20px">
						<input class="form-control" type="text" placeholder="Rechercher un référence" id="ref" name="ref" >
					</div>
					<div style="margin-top:10px" class="col-md-4"><input class="btn btn-block btn-lg btn-primary" type="submit" value="Valider" /></div>
			</form>
			
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="DESC"){ echo "disabled";}}  ?>" type="button" value="Tri par ordre décroissant des références" onclick= "document.location.href = 'cherche_instruments.php?DESC=DESC'" /></div>
		
			<div class="col-md-6"><input style="margin-top:20px" class="btn btn-block btn-lg btn-primary <?php if (isset($_GET['DESC'])) { if ($_GET['DESC']=="ASC"){ echo "disabled";}} ?>" type="button" value="Tri par ordre croissant des références" onclick= "document.location.href = 'cherche_instruments.php?DESC=ASC'" /></div>
				
				</div>
		</div>
	</div>
	
	<div class="jumbotron">
	<table class="table table-bordered">
		<thead>
			<tr style="background-color:#5bc0de;">
				<th>Designation</th>
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
	$str="Select distinct(idDes), nomDes FROM designation, instrument, instrument_vib where numInstru_instrument=numInstru and idDes_designation = idDes order by nomDes ;" ;

	$reqType=mysqli_query($bdd, $str);
	while($lg=mysqli_fetch_object($reqType))
	{

		$premier = true;
		$exception = true;
		$str="SELECT count(distinct(modele)) as total FROM instrument, instrument_vib where numInstru_instrument=numInstru and `idDes_designation`=".$lg->idDes." and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) ;";
		$reqNbLigneType=mysqli_query($bdd, $str);
		$lgNbLigneType=mysqli_fetch_object($reqNbLigneType);
		
		$str="SELECT marque as nomMarque ,count(Distinct(modele)) as nbligneMarque  FROM  instrument, instrument_vib where numInstru_instrument=numInstru and `idDes_designation`=".$lg->idDes." and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) group by marque;";
		$Marque = mysqli_query($bdd, $str) or die ("erreur: '".mysqli_error($bdd));
		while($lgMarque=mysqli_fetch_object($Marque)){
			
			$nbLigneMarque = $lgMarque->nbligneMarque;
			$marque = $lgMarque->nomMarque;
			if ($exception == true && strstr($marque,'PCB') ){
				
				$exception = false;
				$str = "SELECT count(Distinct(modele)) as nbligneMarque  FROM  instrument, instrument_vib where numInstru_instrument=numInstru and `idDes_designation`=".$lg->idDes." and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and marque like 'PCB%'  ";
				$reqException=mysqli_query($bdd, $str);
				$lgException=mysqli_fetch_object($reqException);
				$nbLigneMarque = $lgException->nbligneMarque;
				
				$marque = "PCB";
				if ($premier == true){
				
				
				echo '<tr ><td  rowspan="'.$lgNbLigneType->total.'">'.$lg->nomDes.'</td><td rowspan="'.$nbLigneMarque.'">'.$marque.'</td>';
				
				$des = $lg->idDes;
				
				$str="SELECT modele, count(*) as total FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque like 'PCB%' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele;";
				if (isset($_GET['DESC'])) {
					
					if ($_GET['DESC']=="DESC"){
						
						$str="SELECT modele, count(*) as total FROM instrument, instrument_vib where numInstru_instrument=numInstru and marque like 'PCB%' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele DESC;";
					}
					
				}
				
				
				$modele=mysqli_query($bdd, $str) or die ("erreur: '".mysqli_error($bdd));
				
				$prems= true;
				while($lgmodele=mysqli_fetch_object($modele)){
					
					$mod = $lgmodele->modele;
					$strEnService="SELECT count(distinct(numInstru)) as service FROM `instrument`, `instrument_vib`, localisation WHERE `numInstru_instrument`=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and idLocal_Localisation = idLocal;";
					$reqEnService=mysqli_query($bdd, $strEnService);
					$lgEnService=mysqli_fetch_object($reqEnService);
					$titreEnService = "";
					if ($lgEnService -> service != 0){
						
						$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, localisation , `instrument_vib` WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
						
					}
					
					$strTotal="SELECT count(distinct(numInstru)) as total FROM `instrument`,typecapteur, `instrument_vib`, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `numInstru_instrument`=numInstru and modele ='$mod' and idLocal_Localisation = idLocal;";
					$reqTotal=mysqli_query($bdd, $strTotal);
					$lgTotal=mysqli_fetch_object($reqTotal);
					$titreTotal = "";
					if ($lgTotal -> total != 0){
						
						$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
						
					}
							
					$strCalib="SELECT count(distinct(numInstru)) as calib FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
					$reqCalib=mysqli_query($bdd, $strCalib);
					$lgCalib=mysqli_fetch_object($reqCalib);
					$titreCalib = "";
					if ($lgCalib -> calib != 0){
						
						$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
						
					}
					
					$strCannes="SELECT count(distinct(numInstru)) as cannes FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
					$reqCannes=mysqli_query($bdd, $strCannes);
					$lgCannes=mysqli_fetch_object($reqCannes);
					$titreCannes = "";
					if ($lgCannes -> cannes != 0){
						
						$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
						
					}
					
					$strIntespace="SELECT count(distinct(numInstru)) as intespace FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
					$reqIntespace=mysqli_query($bdd, $strIntespace);
					$lgIntespace=mysqli_fetch_object($reqIntespace);
					$titreIntespace = "";
					if ($lgIntespace -> intespace != 0){
						
						$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
						
					}
					
					$strMecano="SELECT count(distinct(numInstru)) as mecano FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
					$reqMecano=mysqli_query($bdd, $strMecano);
					$lgMecano=mysqli_fetch_object($reqMecano);
					$titreMecano = "";
					if ($lgMecano -> mecano != 0){
						
						$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
						
					}
					
					$strRebut="SELECT count(distinct(numInstru)) as rebut FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
					$reqRebut=mysqli_query($bdd, $strRebut);
					$lgRebut=mysqli_fetch_object($reqRebut);
					$titreRebut = "";
					if ($lgRebut -> rebut != 0){
						
						$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
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
							
						echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
						$prems = false;
					}else{
						echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

					}
				}

				$premier = false;
				}else{

				$des = $lg->idDes;
				echo'<tr><td  rowspan="'.$nbLigneMarque.'">'.$marque.'</td>';
				
				$str2="SELECT modele, count(*) as total FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque like 'PCB%' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele;";
				if (isset($_GET['DESC'])) {
					
					if ($_GET['DESC']=="DESC"){
						
						$str2="SELECT modele, count(*) as total FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque like 'PCB%' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele DESC;";
					}
					
				}
				
				$modele2=mysqli_query($bdd, $str2) or die ("erreur: '".mysqli_error($bdd));
				$prems = true;
				while($lgmodele2=mysqli_fetch_object($modele2)){
					
					$mod = $lgmodele2->modele;
					$strEnService="SELECT count(distinct(numInstru)) as service FROM `instrument`, `instrument_vib`, localisation WHERE `numInstru_instrument`=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and idLocal_Localisation = idLocal group by modele;";
					$reqEnService=mysqli_query($bdd, $strEnService);
					$lgEnService=mysqli_fetch_object($reqEnService);
					$titreEnService = "";
					if ($lgEnService -> service != 0){
						
						$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, localisation , `instrument_vib` WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
						
					}
					
					$strTotal="SELECT count(distinct(numInstru)) as total FROM `instrument`,typecapteur, `instrument_vib`, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `numInstru_instrument`=numInstru and modele ='$mod' and idLocal_Localisation = idLocal group by modele;";
					$reqTotal=mysqli_query($bdd, $strTotal);
					$lgTotal=mysqli_fetch_object($reqTotal);
					$titreTotal = "";
					if ($lgTotal -> total != 0){
						
						$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
						
					}
							
					$strCalib="SELECT count(distinct(numInstru)) as calib FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
					$reqCalib=mysqli_query($bdd, $strCalib);
					$lgCalib=mysqli_fetch_object($reqCalib);
					$titreCalib = "";
					if ($lgCalib -> calib != 0){
						
						$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
						
					}
					
					$strCannes="SELECT count(distinct(numInstru)) as cannes FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
					$reqCannes=mysqli_query($bdd, $strCannes);
					$lgCannes=mysqli_fetch_object($reqCannes);
					$titreCannes = "";
					if ($lgCannes -> cannes != 0){
						
						$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
						
					}
					
					$strIntespace="SELECT count(distinct(numInstru)) as intespace FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
					$reqIntespace=mysqli_query($bdd, $strIntespace);
					$lgIntespace=mysqli_fetch_object($reqIntespace);
					$titreIntespace = "";
					if ($lgIntespace -> intespace != 0){
						
						$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
						
					}
					
					$strMecano="SELECT count(distinct(numInstru)) as mecano FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
					$reqMecano=mysqli_query($bdd, $strMecano);
					$lgMecano=mysqli_fetch_object($reqMecano);
					$titreMecano = "";
					if ($lgMecano -> mecano != 0){
						
						$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
						$req2=mysqli_query($bdd, $str2);
						while ($lg2=mysqli_fetch_object($req2)){
							
							$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
						}
						$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
						
					}
					
					$strRebut="SELECT count(distinct(numInstru)) as rebut FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
					$reqRebut=mysqli_query($bdd, $strRebut);
					$lgRebut=mysqli_fetch_object($reqRebut);
					$titreRebut = "";
					if ($lgRebut -> rebut != 0){
						
						$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
						$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
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
							
						echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
						$prems = false;
					}else{
						echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

					}
					
				}
				
			}
			
			}else 
			{

				if(!strstr($marque,'PCB') ){

					if ($premier == true){
				
						echo '<tr ><td rowspan="'.$lgNbLigneType->total.'">'.$lg->nomDes.'</td><td  rowspan="'.$lgMarque->nbligneMarque.'">'.$marque.'</th>';
						$des = $lg->idDes;
						$str="SELECT modele, count(*) as total FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque='$marque' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele;";
						if (isset($_GET['DESC'])) {
					
							if ($_GET['DESC']=="DESC"){
						
								$str="SELECT modele, count(*) as total FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque='$marque' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele DESC;";
							}
					
						}
						$modele=mysqli_query($bdd, $str) or die ("erreur: '".mysqli_error($bdd));
						$prems= true;
						while($lgmodele=mysqli_fetch_object($modele)){
							
							$mod = $lgmodele->modele;
							$strEnService="SELECT count(distinct(numInstru)) as service FROM `instrument`, `instrument_vib`, localisation WHERE `numInstru_instrument`=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and idLocal_Localisation = idLocal;";
							$reqEnService=mysqli_query($bdd, $strEnService);
							$lgEnService=mysqli_fetch_object($reqEnService);
							$titreEnService = "";
							if ($lgEnService -> service != 0){
								
								$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, localisation , `instrument_vib` WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
								
							}
							
							$strTotal="SELECT count(distinct(numInstru)) as total FROM `instrument`,typecapteur, `instrument_vib`, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `numInstru_instrument`=numInstru and modele ='$mod' and idLocal_Localisation = idLocal;";
							$reqTotal=mysqli_query($bdd, $strTotal);
							$lgTotal=mysqli_fetch_object($reqTotal);
							$titreTotal = "";
							if ($lgTotal -> total != 0){
								
								$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
								
							}
						
							$strCalib="SELECT count(distinct(numInstru)) as calib FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
							$reqCalib=mysqli_query($bdd, $strCalib)or die ("erreur: '".mysqli_error($bdd));
							$lgCalib=mysqli_fetch_object($reqCalib);
							$titreCalib = "";
							if ($lgCalib -> calib != 0){
								
								$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
								
							}
					
							$strCannes="SELECT count(distinct(numInstru)) as cannes FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
							$reqCannes=mysqli_query($bdd, $strCannes)or die ("erreur: '".mysqli_error($bdd));
							$lgCannes=mysqli_fetch_object($reqCannes);
							$titreCannes = "";
							if ($lgCannes -> cannes != 0){
								
								$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
								
							}
					
							$strIntespace="SELECT count(distinct(numInstru)) as intespace FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
							$reqIntespace=mysqli_query($bdd, $strIntespace)or die ("erreur: '".mysqli_error($bdd));
							$lgIntespace=mysqli_fetch_object($reqIntespace);
							$titreIntespace = "";
							if ($lgIntespace -> intespace != 0){
								
								$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
								
							}
					
							$strMecano="SELECT count(distinct(numInstru)) as mecano FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
							$reqMecano=mysqli_query($bdd, $strMecano)or die ("erreur: '".mysqli_error($bdd));
							$lgMecano=mysqli_fetch_object($reqMecano);
							$titreMecano = "";
							if ($lgMecano -> mecano != 0){
								
								$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
								
							}
					
							$strRebut="SELECT count(distinct(numInstru)) as rebut FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
							$reqRebut=mysqli_query($bdd, $strRebut)or die ("erreur: '".mysqli_error($bdd));
							$lgRebut=mysqli_fetch_object($reqRebut);
							$titreRebut = "";
							if ($lgRebut -> rebut != 0){
								
								$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
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
							
								echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
								$prems = false;
							}else{
								echo '<td>'.$lgmodele->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

							}

						}

						$premier = false;
					}else{
				
						$marque = $lgMarque->nomMarque;
						$des = $lg->idDes;
						echo'<tr><td  rowspan="'.$lgMarque->nbligneMarque.'">'.$marque.'</td>';
						$str2 = "SELECT modele FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque='$marque' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele;";
						if (isset($_GET['DESC'])) {
					
							if ($_GET['DESC']=="DESC"){
						
								$str2 = "SELECT modele, count(*) as total FROM  instrument, instrument_vib where numInstru_instrument=numInstru and marque='$marque' and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `idDes_designation`='$des' group by modele order by modele DESC;";
							}
					
						}
				
						$modele2=mysqli_query($bdd, $str2) or die ("erreur: '".mysqli_error($bdd));
						$prems = true;
						while($lgmodele2=mysqli_fetch_object($modele2)){
							
							$mod = $lgmodele2 -> modele;
							$strCalib="SELECT count(distinct(numInstru)) as calib FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and idLocal_Localisation = idLocal;";
							$reqCalib=mysqli_query($bdd, $strCalib);
							$lgCalib=mysqli_fetch_object($reqCalib);
							$titreCalib = "";
							if ($lgCalib -> calib != 0){
								
								$titreCalib = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Calibration' and numInstru_instrument = numInstru  and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCalib .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCalib = str_replace (" ", "&nbsp;", $titreCalib);
								
							}
							
							$strEnService="SELECT count(distinct(numInstru)) as service FROM `instrument`,typecapteur, `instrument_vib`, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `numInstru_instrument`=numInstru and modele ='$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and idLocal_Localisation = idLocal;";
							$reqEnService=mysqli_query($bdd, $strEnService);
							$lgEnService=mysqli_fetch_object($reqEnService);
							$titreEnService = "";
							if ($lgEnService -> service != 0){
								
								$titreEnService = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num, nomLocal FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal != 'Calibration' and nomLocal != 'Prêt Cannes' and nomLocal != 'Prêt Intespace' and nomLocal != 'Prêt Mécano' and nomLocal != 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreEnService .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreEnService = str_replace (" ", "&nbsp;", $titreEnService);
								
							}
							
							$strTotal="SELECT count(distinct(numInstru)) as total FROM `instrument`,typecapteur, `instrument_vib`, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and `numInstru_instrument`=numInstru and modele ='$mod' and idLocal_Localisation = idLocal;";
							$reqTotal=mysqli_query($bdd, $strTotal);
							$lgTotal=mysqli_fetch_object($reqTotal);
							$titreTotal = "";
							if ($lgTotal -> total != 0){
								
								$titreTotal = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie)as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreTotal .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreTotal = str_replace (" ", "&nbsp;", $titreTotal);
								
							}
					
							$strCannes="SELECT count(distinct(numInstru)) as cannes FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and idLocal_Localisation = idLocal;";
							$reqCannes=mysqli_query($bdd, $strCannes);
							$lgCannes=mysqli_fetch_object($reqCannes);
							$titreCannes = "";
							if ($lgCannes -> cannes != 0){
								
								$titreCannes = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Cannes' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreCannes .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreCannes = str_replace (" ", "&nbsp;", $titreCannes);
								
							}
					
							$strIntespace="SELECT count(distinct(numInstru)) as intespace FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and idLocal_Localisation = idLocal;";
							$reqIntespace=mysqli_query($bdd, $strIntespace);
							$lgIntespace=mysqli_fetch_object($reqIntespace);
							$titreIntespace = "";
							if ($lgIntespace -> intespace != 0){
								
								$titreIntespace = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Intespace' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreIntespace .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreIntespace = str_replace (" ", "&nbsp;", $titreIntespace);
								
							}
					
							$strMecano="SELECT count(distinct(numInstru)) as mecano FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and idLocal_Localisation = idLocal;";
							$reqMecano=mysqli_query($bdd, $strMecano);
							$lgMecano=mysqli_fetch_object($reqMecano);
							$titreMecano = "";
							if ($lgMecano -> mecano != 0){
								
								$titreMecano = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'Prêt Mécano' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreMecano .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreMecano = str_replace (" ", "&nbsp;", $titreMecano);
								
							}
					
							$strRebut="SELECT count(distinct(numInstru)) as rebut FROM `instrument`, localisation, instrument_vib where numInstru_instrument=numInstru and numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and idLocal_Localisation = idLocal;";
							$reqRebut=mysqli_query($bdd, $strRebut);
							$lgRebut=mysqli_fetch_object($reqRebut);
							$titreRebut = "";
							if ($lgRebut -> rebut != 0){
								
								$titreRebut = "Numéro&nbsp;de&nbsp;serie&nbsp;:&#10";
								$str2="Select distinct(numSerie) as num FROM instrument, typecapteur, instrument_vib, localisation WHERE numInstru not in (select `numInstru_instrument` from instrument_vib_capteur) and modele = '$mod' and nomLocal = 'REBUT' and numInstru_instrument = numInstru and idLocal_Localisation = idLocal order by modele";
								$req2=mysqli_query($bdd, $str2);
								while ($lg2=mysqli_fetch_object($req2)){
									
									$titreRebut .= "&ndash;&nbsp;".$lg2->num."&#10;";
								}
								$titreRebut = str_replace (" ", "&nbsp;", $titreRebut);
								
							}
					
							//$EnService = $lgmodele2->total - $lgRebut->rebut - $lgMecano->mecano - $lgIntespace -> intespace - $lgCannes -> cannes - $lgCalib -> calib ;
							if ($EnService < 0) {
								$EnService = 0;
						
							}
							if ($prems==true){
							
								echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';
								$prems = false;
							}else{
								echo '<td>'.$lgmodele2->modele.'</td><td><div title='.$titreTotal.'>'.$lgTotal->total.'</div></td><td><div title='.$titreEnService.'>'.$lgEnService->service.'</div></td><td><div title='.$titreCalib.'>'.$lgCalib -> calib.'</div></td><td><div title='.$titreCannes.'>'.$lgCannes -> cannes.'</div></td><td><div title='.$titreIntespace.'>'.$lgIntespace -> intespace.'</div></td><td><div title='.$titreMecano.'>'.$lgMecano->mecano.'</div></td><td><div title='.$titreRebut.'>'.$lgRebut->rebut.'</div></td></tr>';

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
// }


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




















