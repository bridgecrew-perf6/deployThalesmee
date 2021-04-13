<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd

$tab_axe = array('X', 'Y', 'Z');
if (isset ($_POST['crit_ampl']) && isset($_POST['crit_freq'])){

	//Selection de la valeur d'amplitude
	$ampl =  $_POST['crit_ampl'];
	//Selection de la valeur de fréquence
	$freq =  $_POST['crit_freq'];
	
	//Selection des idEssais passé en paramétre (séparés par '-')
	$idEssai = explode("-", $_GET['idEssai']);
	
	$premierEssai = $idEssai[0];
	
	$str = "SELECT idFiche_FICHE_CRITERE as idFiche FROM essai WHERE idEssai = $premierEssai";
	$req = mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object ($req);
	$idFiche = $lg->idFiche;

	$str = "UPDATE essai SET idFiche_FICHE_CRITERE = 0 WHERE idFiche_FICHE_CRITERE = $idFiche";
	$req = mysqli_query($bdd, $str);
	
	if ($idFiche == 0){
		
		$str = "INSERT INTO fiche_critere VALUES (NULL)";
		$req = mysqli_query($bdd, $str);
		$str = "SELECT MAX(idFiche) as maxId FROM fiche_critere";
		$req = mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$id = $lg->maxId;
		$idFiche = $id;
		$str = "UPDATE essai SET idFiche_FICHE_CRITERE = $id WHERE idEssai = $premierEssai";
		$req = mysqli_query($bdd, $str);
		if (!$req) echo "";
		
	}

	//Si il y a des ofs
	if (isset ($_GET['of'])){
		
		//Pour chaque essai
		for ($cpt=0; $cpt < count($idEssai); $cpt++){
			
			$idEs = $idEssai[$cpt];
			$str = "UPDATE essai SET idFiche_FICHE_CRITERE = $idFiche WHERE idEssai = $idEs";
			$req = mysqli_query($bdd, $str);
			if (!$req) echo "";
			
			//Selection des numéros de capteurs qui ont été utilisé dans l'essai
			$str="select numCapteur_CAPTEUR_CRITERE as numCapt from mesurer where idEssai_ESSAI = $idEs ;";
			$reqCapt=@mysqli_query($bdd,$str);
			//Si il y en a
			if (mysqli_num_rows($reqCapt) != 0){

				while ($lg = mysqli_fetch_object($reqCapt)){
					
					//On les supprime tous
					$capt=$lg->numCapt;
					$str2="delete from capteur_critere where numCapteur = $capt ;";
					$req2=@mysqli_query($bdd,$str);
				}
			}
			//Récuperation des ofs passés en paramètres
			$of = explode(" ", $_GET['of']);
			//Pour chaque ofs
			for ($i=0; $i< count($of); $i++){
				//Pour inclure les caractères spéciaux
				$of_str = htmlspecialchars(mysqli_real_escape_string($bdd,$of[$i]));
				$ajoute = false;

				$article = $_POST["article".$of_str];
				$str = "UPDATE equipement_of SET article= '$article' WHERE `noOF`= '$of_str' ;";
				$req = mysqli_query($bdd, $str);
				//Selection de l'essai associé à l'of 
				$str="SELECT idEssai_ESSAI as idEssai FROM `tester` WHERE `noOF_EQUIPEMENT_OF`= '$of_str' ;";
				$req=@mysqli_query($bdd,$str);
				//Parcour du résultat 
				while ($lg=mysqli_fetch_object($req)){
					
					//Si on trouve l'id de l'essai en question
					if ($lg->idEssai == $idEs){

						$ajoute = true;
						break;
					}
				}
				if ($ajoute){
					//Si il y a des capteurs associés à l'essai
					if (mysqli_num_rows($reqCapt) != 0){
						//Suppression des liens
						$str="DELETE from mesurer where idEssai_ESSAI = $idEs and noOF_EQUIPEMENT_OF = '$of_str';";
						$req=@mysqli_query($bdd,$str);
					}
					
					//Récuperation de la valeur de previb
					$previb = intval($_POST['previb']);
					//Selection des anciennes valeurs de l'essai
					$str = "SELECT crit_freq, crit_ampl, preVib from critere where idEssai_ESSAI = $idEs;";
					$req=@mysqli_query($bdd,$str);
					//Si l'essai est déjà présent
					if (mysqli_num_rows($req) != 0){
						
						//Modification des valeurs
						$str="UPDATE critere set preVib=$previb, crit_ampl=$ampl, crit_freq=$freq where idEssai_ESSAI = $idEs;";
						$req=@mysqli_query($bdd,$str);
						
					}else{
						//Insertion des valeurs
						$str="INSERT into critere values ($idEs,$previb,$freq,$ampl);";
						$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
						$req=@mysqli_query($bdd,$str);
					}
					
					foreach ($tab_axe as $axe)
					{	
							//Si il y a des capteur en X
						if (isset($_POST['init_freq'.$axe.$of[$i]])){
							
							//Si il y a des capteur en X spécifique
							if (isset ($_POST['spec'.$axe.$of[$i]])){
								
								$spec = $_POST['spec'.$axe.$of[$i]];
							}
							$nom="";
							//Si il y a des noms de capteur en X
							if (isset ($_POST['nom'.$axe.$of[$i]])){
								
								$nom = $_POST['nom'.$axe.$of[$i]];
							}
							 
							//Récuperation des tableaux de données
							$tab_initfreq = $_POST['init_freq'.$axe.$of[$i]];
							$tab_finfreq = $_POST['fin_freq'.$axe.$of[$i]];
							$tab_shiftfreq = $_POST['shift_freq'.$axe.$of[$i]];
							$tab_initampl = $_POST['init_ampl'.$axe.$of[$i]];
							$tab_finampl = $_POST['fin_ampl'.$axe.$of[$i]];
							$tab_shiftampl = $_POST['shift_ampl'.$axe.$of[$i]];
							//Parcours des tableaux (mêmes longueurs)
							for ($a=0; $a< count($tab_initfreq); $a++){
								$special = 0;
								//Si il y a des capteur en X spécifique
								if (isset ($_POST['spec'.$axe.$of[$i]])){
									//Pour chaque 
									foreach ($spec as $val){
										//Si la valeur de l'input 'specifique' égalle aux capteurs
										//La valeur de l'input est indiqué de 0 à nb capteurs
										//Si l'input à la valeur 1 alors le capteur associé est la deuxieme ligne car la première est 0
										if ($val == $a){
											//Le capteur est spécial
											$special = 1;
											break;
										}
									}
								}
								//Si le nom n'est pas renseigné
								if(!isset($nom[$a])){
									
									$nom[$a] = " ";
								}
								//Insertion du capteur dans la table
								$str="insert into capteur_critere values (NULL, '$tab_initfreq[$a]','$tab_finfreq[$a]','$tab_shiftfreq[$a]','$tab_initampl[$a]','$tab_finampl[$a]','$tab_shiftampl[$a]','".$axe."', $special,'$nom[$a]');";
								$req=@mysqli_query($bdd,$str);
								$idCapt=mysqli_insert_id($bdd);
								//Insertion du lien avec l'essai
								$str="insert into mesurer values ($idEs, '$of_str',$idCapt);";
								$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
								$req=@mysqli_query($bdd,$str);
								
								
							}
							
						}

					}
				}
			}
		}
		
	}
	//Si le technicien à sauvegardé affiche du succés
	if(isset($_POST['save']))
	{
		echo '<script src="../js/success.js"></script>';
	}
	
	if(isset($_POST['tab']))
	{
		echo 'document.location.href="tableauRecap.php?idEssai='.$_GET["idEssai"].'&of='.$_GET["of"];
		echo '<script>document.location.href="tableauRecap.php?idEssai='.$_GET["idEssai"].'&of='.$_GET["of"].'"</script>';
	}

	

}
if (isset($_GET['nom']) && isset($_GET['resp']) && isset($_GET['date'])){
	
	$labo=$_SESSION["infoUser"]["idService"];
	$idEs = $_GET['essai'];
	$idEss = $_GET['essai'];
	$nom = $_GET['nom'];
	$of = explode (" ", $_GET['of']);
	$of_str = $_GET['of'];
	$Essai = array();
	
	$str = "SELECT idFiche_FICHE_CRITERE as idFiche FROM essai WHERE idEssai = $idEs";
	$req = mysqli_query($bdd, $str);
	$lg =mysqli_fetch_object ($req);
	$idFiche = $lg->idFiche;
	
	
	if ($idFiche != 0){
		
		//Selection des essais en lien sur la fiche critère avec l'essai en question
		$str = "select idEssai from essai where idFiche_FICHE_CRITERE = $idFiche;";
		$req=@mysqli_query($bdd,$str);
		$of = explode(" ", $_GET['of']);
		$of_str = $_GET['of'];

		while($lg=mysqli_fetch_object($req)){
			

			$new = $lg->idEssai;
			array_push($Essai,$new);
			
			if ($lg->idEssai != $idEs){
				$idEss .= "-".$new;
				//Selection des ofs
				$str2 = "select noOF_EQUIPEMENT_OF as of from tester where idEssai_ESSAI = $new;";
				$req2=@mysqli_query($bdd,$str2);
				while($lg2=mysqli_fetch_object($req2)){
					if (!in_array($lg2->of,$of)){
						
						array_push($of,$lg2->of);
						
					}
				}
			}
		}
		
	}
	$of_str = implode(" ", $of);
	
	
	//On connait donc les ofs et les idEssai qui son t en lien avec l'essai où l'utilisateur à cliqué

	$idEs = $_GET['essai'];

	$id = explode("-", $idEss);

	//Selection des infos des capteurs
	$str = "select * from critere where idEssai_ESSAI = $idEs;";
	$req=@mysqli_query($bdd,$str);
	//Valeurs par défault
	$crit_freq = 10;
	$crit_ampl = 40;
	$previb = 0;
	if (mysqli_num_rows($req) != 0){
		
		
		while($lg=mysqli_fetch_object($req)){
			
			$crit_freq = $lg->crit_freq;
			$crit_ampl = $lg->crit_ampl;
			$previb = $lg->preVib;
			
		}
	}
	
	//Selection des essais susceptible d'être réalisé en simultané
	$str = "SELECT distinct(ee.idEssai_ESSAI) as idEs FROM tester t, `etatessai` ee, essai e WHERE ee.idEssai_ESSAI =e.idEssai and e.idService_SERVICE='$labo' and idEtat_ETAT=23 and ee.idEssai_Essai=t.idEssai_Essai and  ee.idEssai_ESSAI not in (SELECT ee.idEssai_ESSAI FROM `etatessai` ee WHERE idEtat_ETAT=24)";
	$req=@mysqli_query($bdd,$str);

	

?>	
<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
<script src="../jquery-ui/js/jquery-ui.min.js"></script>

	<form method="post" name = "form" action="critere.php?of=<?php echo $of_str ; ?>&idEssai=<?php echo $idEss; ?>">
		<input id= "idEssai" style="display: none" name ='idEssai' value="<?php echo $idEss; ?>"></div>
		<div class="container-fluid">
			<div class="page-header">
				<div style="position:relative;"><h2>Critères</h2></div>
			</div>		
			<div class="container-fluid theme-showcase" role="main">
				<h4>Informations générales</h4>
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-3">
							<div class="info"><label>Nom du responsable:&nbsp </label><?php echo $_GET['resp']; ?></div>									
						</div>
						<div class="col-md-3">
							<div class="info"><label>Date:&nbsp </label><?php echo $_GET['date']; ?></div>						
						</div>
						<div class="col-md-3">
							<div class="info"><label>Nom de l'équipement:&nbsp </label><?php echo $_GET['nom']; ?></div>
						</div>
						<div class="col-md-3">
							<div class="info"><label class='of' id='<?php echo $of_str; ?>'>OF:&nbsp </label><?php echo $of_str; ?></div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="container-fluid theme-showcase" role="main">
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-6">
							<div class="info"><label>Critère pour la fréquence:&nbsp </label><input value=<?php echo $crit_freq  ?> class="form-control" placeholder="Fréquence"  type="text" id="crit_freq" name="crit_freq" title="Critère frèquence" required/></div>									
						</div>
						<div class="col-md-6">
							<div class="info"><label>Critère pour l'amplitude:&nbsp </label><input value=<?php echo $crit_ampl  ?> class="form-control" placeholder="Amplitude"  type="text" id="crit_ampl" name="crit_ampl" title="Critère amplitude" required/></div>						
						</div>
						
					</div>
					
					<div class="row">
						<div class="col-md-1">
							<div class="info"><label>Pré-vibration:&nbsp </label></div>
						</div>
						<div class="col-md-1">
							<div class="btnRadio"><input name="previb" type="radio" value="1" <?php if ($previb == 1) echo 'checked' ?>> Oui</div>
						</div>
						<div class="col-md-1">
							<div class="btnRadio"><input name="previb" type="radio" value="0" <?php if ($previb == 0) echo 'checked' ?>> Non</div>
						</div>
						
					</div>
				</div>
				<h4>Essai susceptible d'être réaliser en simultané</h4>
				<div class="jumbotron">
					<div class="row">
						<table class='table'>
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th style="text-align:left;">Numéro</th>
									<th style="text-align:left;">Equipement</th>
									<th style="text-align:left;">Moyen</th>
									<th style="text-align:left;">Affaire</th>
									<th style="text-align:left;">Article</th>
									<th style="text-align:left;">OF</th>
								</tr>
							</thead>
						<tbody>

					<?php
					$str ="SELECT max(idEtat_ETAT) as etat FROM etatEssai WHERE idEssai_ESSAI=$idEs";
					$req_etat = mysqli_query($bdd, $str);
					if (mysqli_fetch_object($req_etat)->etat != 23)
					{
						$str3 = "SELECT affaire, nomMoyen, equipement FROM moyen,  essai e WHERE idMoyen=idMoyen_MOYEN and idEssai = ".$idEs."";
						$req3=@mysqli_query($bdd,$str3);
						$lg3=mysqli_fetch_object($req3);
						
						$str2 = "SELECT noOF_EQUIPEMENT_OF as of FROM tester t, essai e WHERE e.idEssai=t.idEssai_Essai and idEssai = ".$idEs."";
						$req2=@mysqli_query($bdd,$str2);
						$allof = "";
						$allof2 = "";
						$article = "";
						while ($lg2=mysqli_fetch_object($req2)){
							
							$strArticle = "SELECT article FROM equipement_of WHERE noOF='".$lg2->of."'";
							$reqArticle=@mysqli_query($bdd,$strArticle);
							$article = mysqli_fetch_object($reqArticle)->article;
							$allof .= $lg2->of."-";
							$allof2 .= $lg2->of." ";
						}

						$allof = substr($allof, 0,strlen($allof)-1);
						$allof2 = substr($allof2, 0,strlen($allof2)-1);
						echo '<tr><td class="info" id="'.$idEs.'" ><input type="checkbox" id = "'.$allof.'" class="plus" value="'.$allof.'" name="new" size=12 ';
								
						echo ' checked disabled="disabled"> </td>';

						echo '<td>'.$idEs.'</td>';
						echo '<td>'.$lg3->equipement.'</td>';
						echo '<td>'.$lg3->nomMoyen.'</td>';
						echo '<td>'.$lg3->affaire.'</td>';
						echo '<td><input class="form-control" type="text" name="article'.$allof2.'" value="'.$article.'" required /></td>';
						echo '<td>'.$allof2.'</td></tr>';
					}

					while($lg=mysqli_fetch_object($req)){
							
							$str3 = "SELECT affaire, nomMoyen, equipement FROM moyen,  essai e WHERE idMoyen=idMoyen_MOYEN and idEssai = ".$lg->idEs."";
							$req3=@mysqli_query($bdd,$str3);
							$lg3=mysqli_fetch_object($req3);
							
							$str2 = "SELECT noOF_EQUIPEMENT_OF as of FROM tester t, essai e WHERE e.idEssai=t.idEssai_Essai and idEssai = ".$lg->idEs."";
							$req2=@mysqli_query($bdd,$str2);
							
							$allof = "";
							$allof2 = "";
							$article = "";
							while ($lg2=mysqli_fetch_object($req2)){
								
								$strArticle = "SELECT article FROM equipement_of WHERE noOF='".$lg2->of."'";
								$reqArticle=@mysqli_query($bdd,$strArticle);
								$article = mysqli_fetch_object($reqArticle)->article;
								$allof .= $lg2->of."-";
								$allof2 .= $lg2->of." ";
							}
							$allof = substr($allof, 0,strlen($allof)-1);
							$allof2 = substr($allof2, 0,strlen($allof2)-1);
							echo '<tr><td class="info" id="'.$lg->idEs.'" ><input type="checkbox" id = "'.$allof.'" class="plus" value="'.$allof.'" name="new" size=12 ';
							if ($lg->idEs == intval($idEs)){
									
								echo ' checked disabled="disabled"> </td>';
							}
							else if (in_array($lg->idEs,$Essai)){
								
								echo ' checked> </td>';
								
							}else{
								echo '> </td>';	
							}
							echo '<td>'.$lg->idEs.'</td>';
							echo '<td>'.$lg3->equipement.'</td>';
							echo '<td>'.$lg3->nomMoyen.'</td>';
							echo '<td>'.$lg3->affaire.'</td>';
							echo '<td><input class="form-control" type="text" name="article'.$allof2.'" value="'.$article.'" required /></td>';
							echo '<td>'.$allof2.'</td></tr>';
							
						}

					?>
							</tbody>
						</table>


					</div>
					
					
				</div>
			</div>
			
			
			<div id="contain" class="container-fluid theme-showcase" role="main">
				<div id="new"></div>
				<?php 
				
				for ($compteur=0; $compteur < count($id); $compteur++){
						
					$idEs = $id[$compteur];
				
					for ($i=0; $i< count($of); $i++){
					
						$ajoute = false;
						$str="SELECT idEssai_ESSAI as idEssai FROM `tester` WHERE `noOF_EQUIPEMENT_OF`= '$of[$i]' ;";
						$req=@mysqli_query($bdd,$str);
						
						while ($lg=mysqli_fetch_object($req)){
							
							if ($lg->idEssai == $idEs){

								$ajoute = true;
								break;
							}
						}
						if ($ajoute){

							echo '
							<div id="new">
								<h4 class='.$of[$i].' id='.$of[$i].' >OF concerné : '.$of[$i].' </h4>
								<div class="jumbotron">';

							foreach ($tab_axe as $axe)
							{
								$str="select * from capteur_critere cc , mesurer m where cc.axe = '".$axe."' and cc.numCapteur = m.numCapteur_CAPTEUR_CRITERE and m.idEssai_ESSAI = $idEs and m.noOF_EQUIPEMENT_OF = '$of[$i]';";
								$req=@mysqli_query($bdd,$str);
								echo '
								
										<div class="row"><div class="col-md-1" ><h4 style="display:inline">Axe '.$axe.' </h4></div><input type="checkbox" class="anomalieX '.$of[$i].'" value="1" name="anomalie" 
										';
								if (mysqli_num_rows($req) !=0){
									$style = "";
									echo "checked";
								}else{
									$style = "display:none";
								}

								echo '>
							
							<div style='.$style.' id="anom'.$axe.$of[$i].'" class="jumbotron table-reponsive">
										<table class="table table-striped">
											<thead>
												<tr>
													
													<th >Spécifique</th>
													<th>Nom du capteur</th>
													<th >Fréquence initiale</th>
													<th >Fréquence finale</th>
													<th >Décalage sur la fréquence</th>
													<th >Amplitude initiale</th>
													<th >Amplitude finale</th>
													<th >Décalage sur l&#39;amplitude</th>
													<th>Supprimer</th>
												</tr>
											</thead>
											<tbody id="tabCapt'.$axe.$of[$i].'">';
											$cpt = 0;
											while($lg=mysqli_fetch_object($req))
											{
												$nom = $lg->nom;
												$sp = $lg->specifique;
												$fi =$lg->freq_init;
												$ff =$lg->freq_fin;
												$sf =$lg->shift_freq;
												$ai =$lg->ampl_init;
												$af =$lg->ampl_fin;
												$sa =$lg->shift_ampl;
												echo '<tr>
												<td>
												<input type="checkbox" class = '.$cpt.' id="spec" value='.$cpt.' name="spec'.$axe.$of[$i].'[]" '; if ($sp == 1) echo "checked"; echo ' size=12>
												</td>
												<td>
												<input type="text" class = "nom" id="spec" value="'.$nom.'" name="nom'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<input type="text" class="init_freq" value="'.$fi.'" name="init_freq'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<input type="text" class="fin_freq" value="'.$ff.'" name="fin_freq'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<input type="text" class="shift_freq" value="'.$sf.'" name="shift_freq'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<input type="text" class="init_ampl" value="'.$ai.'" name="init_ampl'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<input type="text" class="fin_ampl" value="'.$af.'" name="fin_ampl'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<input type="text" class="shift_ampl" value="'.$sa.'" name="shift_ampl'.$axe.$of[$i].'[]" size=6>
												</td>
												<td>
												<img class="imgSupp"  SRC="../img/supr.png" class="btnSuppLigne" onclick="suppLigne(this)" />
												</td></tr>';
												$cpt+=1;
											}

											echo '</tbody>
										</table>
										<center>
											<input type="button" class="btn  btn-primary" value="Ajouter un capteur" onclick=ajout_capt("X","'.$of[$i].'") />		
										</center>
									
							</div></div>';
							}
							echo '</div></div>';

						}
					}
				}
					
				?>
			</div>
			</div>
			<div class="text-center">
				<input id = "save" style="position:fixed; right:10px; bottom:20px;" type="submit" name='save'  class="btn btn-lg btn-warning" value="Enregistrer et quitter" />
				<input id="tab" type="submit" name='tab' class="btn btn-lg btn-primary" value="Tableau récapitulatif" />
				<input type="button" class="btn btn-lg btn-primary" onclick="document.location.href='./index.php'" value="Retour" />
			</div>
		</div>
	</form>

<script src="../js/critere.js"></script>
<?php	
}
require('bottom.php');
?>