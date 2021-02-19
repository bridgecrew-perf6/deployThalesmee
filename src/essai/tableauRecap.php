<?php	
//Si le technicien veut voir le(s) tableau(x) récapitulatif(s)
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
echo '<div class="container-fluid">
	<div class="page-header">
		<div style="position:relative;"><h2>Tableau récapitulatif critère</h2></div>
	</div>';
$idEssai = explode("-", $_GET['idEssai']);
//Pour chaque essais
for ($compteur=0; $compteur < count($idEssai); $compteur++){

	$idEs = $idEssai[$compteur];

			
	$of = explode(" ", $_GET['of']);
	//Pour chaque ofs associés à l'essai
	for ($i=0; $i< count($of); $i++){
		
		$ajoute = false;
		//Selection des essais associés à l'of
		$str="SELECT idEssai_ESSAI as idEssai FROM `tester` WHERE `noOF_EQUIPEMENT_OF`= '$of[$i]' ;";
		$req=@mysqli_query($bdd,$str);
		
		while ($lg=mysqli_fetch_object($req)){
			//Si on trouve l'id de l'essai en question
			if ($lg->idEssai == $idEs){

				$ajoute = true;
				break;
			}
		}

		
		if ($ajoute){
			//Selection du modele de l'of
			$str="SELECT nomModele FROM `type_modele`, equipement_of WHERE `noOF`= '$of[$i]' and idModele = idModele_TYPE_MODELE;";
			$req=@mysqli_query($bdd,$str);
			$lg=mysqli_fetch_object($req);
			//Affichage du tableau
			echo '
		
	<div class="container-fluid theme-showcase" role="main">
		<div class="jumbotron"><table id='.$of[$i].' style="vertical-align:baseline" class="table table-bordered "><thead>
					<tr>
						<th colspan=7>'.$lg->nomModele." ".$of[$i].'</th>
					</tr>
					<tr style="vertical-align:middle; align-items:center">
						<th rowspan=2 class="vertical-align"><p><strong>Axis</strong></p></th>
						<th>Initial</th>
						<th>Final</th>
						<th>Shift</th>
						<th>Initial</th>
						<th>Final</th>
						<th>Shift</th>
					</tr>
					<tr>
						<th>Frequency (Hz)</th>
						<th>Frequency (Hz)</th>
						<th>On frequency</th>
						<th>Amplitude (g 0-peak)</th>
						<th>Amplitude (g 0-peak)</th>
						<th>On amplitude</th>
					</tr>
				</thead>
				<tbody>';

			//Selection des valeurs des capteurs en X
			$str="select freq_init as fi, freq_fin as ff, shift_freq as sf, ampl_init as ai, ampl_fin as af, shift_ampl as sa, nom from capteur_critere cc, mesurer m where m.idEssai_ESSAI = $idEs and noOF_EQUIPEMENT_OF = '$of[$i]' and numCapteur_CAPTEUR_CRITERE = numCapteur and axe='X';";
			$req=@mysqli_query($bdd,$str);
			$cpt = 1;
			while ($lg=mysqli_fetch_object($req)){
				
				echo '<tr>
					<th>X'.$cpt.'</th>
					<td>'.$lg->fi.'</td>
					<td>'.$lg->ff.'</td>
					<td>'.$lg->sf.'</td>
					<td>'.$lg->ai.'</td>
					<td>'.$lg->af.'</td>
					<td>'.$lg->sa.'</td>
					</tr>';
					
				$cpt++;
			}
			//Selection des valeurs des capteurs en Y
			$str="select freq_init as fi, freq_fin as ff, shift_freq as sf, ampl_init as ai, ampl_fin as af, shift_ampl as sa from capteur_critere cc, mesurer m where m.idEssai_ESSAI = $idEs and noOF_EQUIPEMENT_OF = '$of[$i]' and numCapteur_CAPTEUR_CRITERE = numCapteur and axe='Y';";
			$req=@mysqli_query($bdd,$str);
			$cpt = 1;
			while ($lg=mysqli_fetch_object($req)){
				
				echo '<tr>
					<th>Y'.$cpt.'</th>
					<td>'.$lg->fi.'</td>
					<td>'.$lg->ff.'</td>
					<td>'.$lg->sf.'</td>
					<td>'.$lg->ai.'</td>
					<td>'.$lg->af.'</td>
					<td>'.$lg->sa.'</td>
					</tr>';
					
				$cpt++;
			}
			//Selection des valeurs des capteurs en Z
			$str="select freq_init as fi, freq_fin as ff, shift_freq as sf, ampl_init as ai, ampl_fin as af, shift_ampl as sa from capteur_critere cc, mesurer m where m.idEssai_ESSAI = $idEs and noOF_EQUIPEMENT_OF = '$of[$i]' and numCapteur_CAPTEUR_CRITERE = numCapteur and axe='Z';";
			$req=@mysqli_query($bdd,$str);
			$cpt = 1;
			while ($lg=mysqli_fetch_object($req)){
				
				echo '<tr>
					<th>Z'.$cpt.'</th>
					<td>'.$lg->fi.'</td>
					<td>'.$lg->ff.'</td>
					<td>'.$lg->sf.'</td>
					<td>'.$lg->ai.'</td>
					<td>'.$lg->af.'</td>
					<td>'.$lg->sa.'</td>
					</tr>';
					
				$cpt++;
			}
			
			echo '</tbody></table></div>';
			echo '</div>';
		}
	}
}
echo '<div class="text-center"><input type="button" class="btn btn-lg btn-primary" onclick=document.location.href="./index.php" value="Retour" /></div>';
echo '<div>';

require('bottom.php');
?>