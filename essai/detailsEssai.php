
<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php');
$idLabo=$_SESSION['infoUser']['idService'];// service du labo
if (!isset($_GET['idEssai']))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du numéro de l'essai</strong></div>";
else{
	
	$idEssai=$_GET['idEssai'];
	
	$str = "SELECT nomEmp FROM `employe` WHERE `idService_SERVICE`=$idLabo and idEmp != 4;";
	$reqTechnicien=mysqli_query($bdd,$str);

	//on recupere les infos des of qui n'ont pas encore eu de retour équipement
	$str="SELECT e.idEssai, e.badge, e.idTachePrim, e.affaire, e.equipement, e.ligneProd, e.os, e.commentaire, et.idEtat_ETAT, e.fifo, e.date_debut, e.date_fin, e.date_debut_prevu, e.date_fin_prevu, d.nomDep, d.prenomDep, d.telDep, e.planifie, e.pastilleOrange, e.pastilleRouge, e.duree_planifie, e.duree_actuelle 
	FROM etatEssai et, essai e LEFT JOIN depositaire d on e.idDep_depositaire=d.idDep
	where e.idEssai =$idEssai
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEssai_ESSAI=e.idEssai;";
	$req=mysqli_query($bdd,$str);
	
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de l'essai</strong></div>";
	else{
		$lg=mysqli_fetch_object($req);
		$badge=$lg->badge;
		$code = $lg->idTachePrim;
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$depositaire=$lg->nomDep." ".$lg->prenomDep;
		$telDep=$lg->telDep;
		$os=$lg->os;
		$remarque=$lg->commentaire;
		$idEtat=$lg->idEtat_ETAT;
		$date_debut=date('d/m/Y H:i',strtotime($lg->date_debut));
		$date_debut_prevu = $lg->date_debut_prevu;
		$date_fin=date('d/m/Y H:i',strtotime($lg->date_fin));
		$date_fin_prevu = $lg->date_fin_prevu;
		$planifie=$lg->planifie;
		$pastilleOrange=$lg->pastilleOrange;
		$pastilleRouge=$lg->pastilleRouge;
		$date_debut_planifie = date('d/m/Y H:i',strtotime($lg->date_debut_prevu));
		$date_fin_planifie = date('d/m/Y H:i',strtotime($lg->date_fin_prevu));

		$duree_planifie = duree($date_debut_planifie, $date_fin_planifie);
		$duree_actuelle = duree($date_debut, $date_fin);

		/*$duree_planifie = $lg->duree_planifie;
		if ($duree_planifie == 0) $duree_planifie = dureePrimavera($lg->date_debut_prevu, $lg->date_fin_prevu);
		$duree_planifie = round($duree_planifie*9,1);
		$duree_actuelle = $lg->duree_actuelle;
		if ($duree_actuelle == 0) $duree_actuelle = dureePrimavera($lg->date_debut, $lg->date_fin);
		$duree_actuelle = round($duree_actuelle*9,1);

		$duree_actuelle_heure = intval($duree_actuelle);
		$duree_planifie_heure = intval($duree_planifie);
		$duree_actuelle_minute = round(($duree_actuelle - intval($duree_actuelle)) * 60,0);
		$duree_planifie_minute = round(($duree_planifie - intval($duree_planifie)) * 60,0);*/
		
		if($lg->fifo==1)
			$fifo="Oui";
		else
			$fifo="Non";
		
		$idServ_Labo=$_SESSION['infoUser']['idService'];
			
		$ligne = $lg->ligneProd;
		
		if ($ligne != null){
			//Récupération de la ligne de produit
			$str_ligne = "SELECT nomLigne FROM ligneproduit WHERE idLigne = $ligne";
			$req_ligne = mysqli_query($bdd, $str_ligne);
			$lg_ligne = mysqli_fetch_object ($req_ligne);
			$ligne = $lg_ligne->nomLigne;
		}
			
		$selectMoyen=false;
		
		$str="SELECT e.noOF, m.nomModele, e.article FROM equipement_of e, type_modele m
		where noOF in (select noOF_equipement_of from tester where idEssai_Essai=$idEssai)
		and e.idModele_TYPE_MODELE=m.idModele;";
		$req=@mysqli_query($bdd,$str);
		$tabof="";
		while ($lg=mysqli_fetch_object($req)){
			
			$tabof.= $lg->noOF." ";
		}
		$tabof = substr($tabof, 0,strlen($tabof)-1);

		//on recupere les infos des of qui n'ont pas encore eu de retour équipement
		$str="SELECT  m.nomMoyen FROM essai e, moyen m
		where m.idMoyen = e.idMoyen_MOYEN
		and idEssai=$idEssai";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0)
			$moyen=mysqli_fetch_object($req)->nomMoyen;
		else
		{
			$moyen="Indeterminé";
			$selectMoyen=true;
		}
		switch ($idEtat) {
			case 20:
				$etat="Essai plannifié";
				$etatSuivant="Réserver l'essai";
				break;
			case 21:
				$etat="Essai réservé";
				$etatSuivant="Équipement disponible";
				break;
			case 22:
				$etat="Essai en attente";
				$etatSuivant="Lancer l'essai";
				// on recupere les moyens du labo pour la box passage a l'etat suivant
				$str="SELECT idMoyen, nomMoyen from moyen where idService_SERVICE=$idServ_Labo;";
				$reqMoyen=mysqli_query($bdd, $str);
				break;
			case 23:
				$etat="Essai en cours";
				$etatSuivant="Terminer l'essai";
				break;
			case 24:
				$etat="Essai terminé";
				$etatSuivant="Retour équipement";
				break;
			case 25:
				$etat="Essai terminé avec équipement retourné";
				$etatSuivant="Validation PV";
				break;
			case 26:
				$etat="Essai terminé avec équipement retourné et PV validé";
				break;
			case 27:
				$etat="Maintenance";
				break;
			default:
			
				break;
		}
		
		//Gestion des anomalies
		$str = "select idEssai, quiss_mpti, autre, descriptif, heure, eurosPerdus, status from anomalie where idEssai=$idEssai";
		$req2 = @mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req2);
		if (mysqli_num_rows($req2)==0){
			$anomalie = false;
			$autre = '';
			$decriptif = '';
			$heure = '';
			$euros = '';
			$status=0;
			$quiss = '';
		}else{
			$anomalie = true;
			$autre = $lg->autre;
			$str = "SELECT nomLigne FROM ligneproduit WHERE idLigne = $autre";
			$req_ligne = mysqli_query($bdd, $str);
			$lg_ligne = mysqli_fetch_object($req_ligne);
			$autre = $lg_ligne->nomLigne;
			$descriptif = $lg->descriptif;
			$heure = $lg->heure;
			$euros = $lg->eurosPerdus;
			$status=$lg->status;
			if ($status == 0){
				
				$status = "Non validé";
			}else{
				
				$status = "Ok"; 
			}
			$quiss = $lg->quiss_mpti;
		}

		//On recupere le nom du technicien qui a passé le test
		if ($idEtat>=23){
			
			$str = "select nomEmp from vibtesterpar where idEssai=$idEssai";
			$req2 = @mysqli_query($bdd, $str);
			$lg = mysqli_fetch_object($req2);
			if (mysqli_num_rows($req2)!=0){
				$nom = $lg->nomEmp;
			}else{
				
				$nom = "Non renseigné";
			}
			if ($nom == "undefined"){
				
				$nom = "Non renseigné";
			}
		}
		
		//Récupération des familles
		$str="SELECT famille_FAMILLE, heure_FAMILLE, modeleFamille_FAMILLE FROM famille_essai, famille WHERE idEssai_ESSAI = $idEssai;";
		$req=mysqli_query($bdd,$str);
		$famille_saisie = false;
		if(mysqli_num_rows($req)!=0){
			
			$lg=mysqli_fetch_object($req);
			
			$famille = $lg->famille_FAMILLE;
			$heure_famille = $lg->heure_FAMILLE;
			$modeleFamille = $lg->modeleFamille_FAMILLE;
			$famille_saisie=true;
		}else{
			
			$famille = "Non renseigné";
			$modeleFamille = "";
			$heure_famille = 0;
		}
		
		//Récuperation des anomalies
		$anomalie = false;
		$str="SELECT idEssai from anomalie where idEssai = $idEssai;";
		$req=mysqli_query($bdd,$str); 
		if(mysqli_num_rows($req)!=0){
			
			$anomalie = true;
			//Récupération de la cause
			$str="SELECT nomCause FROM cause_anomalie WHERE idEssai_ESSAI = $idEssai";
			$req=mysqli_query($bdd,$str);
			$cause_saisie = false;
			if(mysqli_num_rows($req)!=0){
				
				$lg=mysqli_fetch_object($req);
				$nomCause = $lg->nomCause;
				$cause_saisie=true;
			}else{
				
				$nomCause = "Non renseigné";
			}
		}

		//Récuperation du retard ME
		$retardME = false;
		$str="SELECT retardME from essai where idEssai = $idEssai;";
		$req=mysqli_query($bdd,$str);
		$lg = mysqli_fetch_object($req);
		if($lg->retardME==1){
			
			$retardME = true;
		}
		
		//Récupération des dates des essais
		$str_etat = "SELECT dateEtat, nomEtat, idEtat FROM etat, etatessai et WHERE idEtat = idEtat_ETAT and idEssai_ESSAI = $idEssai";
		$req_etat = mysqli_query($bdd, $str_etat);
		$lg_etat = mysqli_fetch_object($req_etat);
			
		//on recupere les of concernés
		$str="SELECT e.noOF, m.nomModele, e.article FROM equipement_of e, type_modele m
		where noOF in (select noOF_equipement_of from tester where idEssai_Essai=$idEssai)
		and e.idModele_TYPE_MODELE=m.idModele;";
		$req=@mysqli_query($bdd,$str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de des of</strong></div>";
		else{
		
?>
			
			<div class="container-fluid">
				<div class="page-header">
					<div style="position:relative;"><h2>Essai n°<?php echo $idEssai; ?>: <?php echo $etat; ?><?php if ($pastilleOrange==1) echo '<div><span title="Retard livraison équipement" class="pastilleOrange"></span></div>'; ?><?php if ($pastilleRouge==1) echo '<div><span title="Essai non planifié" class="pastilleRouge"></span><div>'; ?><?php if ($retardME == true && $pastilleOrange!=1) echo '<span class="retardME" title="Retard ME">&#x26A0;</span>'; ?><input style= "margin-left:5px;" type="button" class="btn btn-primary" value="Critères" onclick="document.location.href='./critere.php?date=<?php echo $date_debut; ?>&resp=<?php if ($idEtat>=23) echo "$nom"; ?>&nom=<?php echo $equipement; ?>&of=<?php echo $tabof; ?>&essai=<?php echo $idEssai; ?>'" /></h2></div>
					
				</div>
				
				<div class="container-fluid theme-showcase" role="main">
					<h4>Informations générales</h4>
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Activity code:&nbsp </label><?php echo $code; ?></div>
								<div class="info"><label>Nom de l'affaire:&nbsp </label><?php echo $affaire; ?></div>
								<div class="info"><label>Ligne de produit:&nbsp </label><?php echo $ligne; ?></div>
								<div class="info"><label>Nom de l'équipement:&nbsp </label><?php echo $equipement; ?></div>
								<div class="info"><label>Famille d'équipement:&nbsp </label><?php echo $famille." ".$modeleFamille; ?></div>
								
							</div>

							<div class="col-md-4">
								<div class="info"><label>Dépositaire:&nbsp </label><?php echo $depositaire; ?></div>
								<div class="info"><label>Téléphone:&nbsp </label><?php echo $telDep; ?></div>
								<div class="info"><label>Badge:&nbsp </label><?php echo $badge; ?></div>
								<div class="info"><label>Fifo:&nbsp </label><?php echo $fifo; ?></div>
								<div class="info"><label>Heure estimée:&nbsp </label><?php echo $heure_famille; ?></div>
															
							</div>
							<div class="col-md-4">
								<div class="info"><label><?php if ($idEtat>=21) echo "Réalisé par:&nbsp "; ?></label> <?php if ($idEtat>=23) echo "$nom"; ?></div>
								<div class="info"><label>Moyen:&nbsp </label><?php echo $moyen; ?></div>
								<div class="info"><label>N° d'OS:&nbsp </label><?php echo $os; ?></div>
								<div class="info"><label>Date début:&nbsp </label><?php echo $date_debut; ?></div>
								<div class="info"><label>Date fin:&nbsp </label><?php echo $date_fin; ?></div>	
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
					<?php
					if($idEtat > 21 && $idEtat!=27)
					{
						//si etat 22 et non plannifie on verifie qu'il existe un etat précedent
						$verif=true;
						if($planifie==0 && $idEtat == 22)
						{
							$str="SELECT idEtat_ETAT from etatEssai
							where idEtat_ETAT='21'
							and idEssai_essai='$idEssai'";
							$reqVerif=@mysqli_query($bdd,$str);
							if(mysqli_num_rows($reqVerif)==0)
								$verif=false;
						}
						if($verif)
						{
							?><input type="button" class="btn btn-lg btn-primary" value="Étape précédente" onclick="if(confirm('Revenir à l\'étape précedente ?'))document.location.href='etapePrecedente.php?idEssai=<?php echo $idEssai; ?>';"/><?php 
						}
					}
					?>
					</div>
					<div class="col-md-4 text-center">
						<?php
					if (isset($_GET['back'])){
					?>
						<input type="button" class="btn btn-lg btn-warning" value="Modifier" onclick="document.location.href='modifEssai.php?idEssai=<?php echo $idEssai; ?>&back=<?php echo $_GET['back']; ?>'"/>
					<?php	
					}else{
					?>
						<input type="button" class="btn btn-lg btn-warning" value="Modifier" onclick="document.location.href='modifEssai.php?idEssai=<?php echo $idEssai; ?>'" />
					<?php
					}
					?>

					<form style="display:inline;" method="post" action="suppEssai.php" onsubmit="return confirmSupp();" id="suppr">
						<input type="hidden" name="idEssai" value="<?php echo $idEssai; ?>"/>
						<input type="hidden" id="sendRaison" name="raison" value="" />
						<input type="button" class="btn btn-lg btn-danger" value="Supprimer" onclick='raisonSuppr()'/>					
					</form>

					<br/>
					<br/>
					<?php
					if (isset($_GET['back'])){
					?>

						<input type="button" class="btn btn-lg btn-primary" value="Retour" onclick="document.location.href='./<?php echo $_GET['back']?>.php'" />

					<?php	
					}else{
					?>

						<input type="button" class="btn btn-lg btn-primary" value="Retour" onclick="document.location.href='./index.php'" />

					<?php
					}
					?>
					</div>
					<div class="col-md-4 text-right">
						<?php if($idEtat<26)
						{
						?>
							<input type="button" class="btn btn-lg btn-primary" value="<?php echo $etatSuivant; ?>" id="eta" onclick="valdEtape('<?php echo $idEtat;?>','<?php echo $selectMoyen;?>','<?php echo $idEssai;?>')"/>
						<?php 
						}
						?>
					</div>
				</div>
				
				<div class="container-fluid theme-showcase" role="main">
					<h4 class="sub-header">N°OF concernés</h4>
					<div class="jumbotron" >
						<div class="row">
							<div class="col-md-6" id="col0" >
							</div>							
							<div class="col-md-6" id="col1">
							</div>	
						</div>
					</div>
				</div>
				<div class="container-fluid theme-showcase" role="main">
					<h4 class="sub-header">Date de passage dans les différents états <button class="btn btn-primary" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">Afficher</button></h4>
					<div class="jumbotron collapse" id="collapse">
						<?php
						if (mysqli_num_rows($req_etat) != 0){
							
							echo '<div class="row"><div class="col-md-3"><table>';
							while ($lg = mysqli_fetch_object ($req_etat)){
							
								$idEtat = $lg->idEtat;
								switch ($idEtat) {
									case 20:
										$classBar='bar-plannifie';
										break;
									case 21:
										$classBar='bar-reserve';
										break;
									case 22:
										$classBar='bar-attente';
										break;
									case 23:
										$classBar='bar-cours';
										break;	
									case 24:
										$classBar='bar-fin';
										break;
									case 25:
										$classBar='bar-retour';
										break;
									case 26:
										$classBar='bar-validation';
										break;
									case 27:
										$classBar='bar-maintenance';
										break;
								}
								echo '
								<tr class="'.$classBar.'">
									<td style="padding:10px;"><label class="info">'.$lg->nomEtat.' le</label></td>
									<td style="padding:10px;"><label class="info">'.dateSQLToFrWithHours($lg->dateEtat).'</label></td>
									
								</tr>';
							}

							echo '</table></div>
							<div class="col-md-3"><table>
								<tr>
									<td style="padding:10px;"><label class="info">Date de début</label></td>
									<td style="padding:10px;"><label class="info">'.$date_debut.'</label></td>
								</tr>
								<tr>
									<td style="padding:10px;"><label class="info">Date de début prévue</label></td>
									<td style="padding:10px;"><label class="info">'.dateSQLToFrWithoutMinutes($date_debut_prevu).'</label></td>
								</tr>
							</table></div>
							<div class="col-md-3"><table>
								<tr>
									<td style="padding:10px;"><label class="info">Date de fin</label></td>
									<td style="padding:10px;"><label class="info">'.$date_fin.'<label></td>
								</tr>
								<tr>
									<td style="padding:10px;"><label class="info">Date de fin prévue</label></td>
									<td style="padding:10px;"><label class="info">'.dateSQLToFrWithoutMinutes($date_fin_prevu).'</label></td>
								</tr>
							</table></div>
							<div class="col-md-3"><table>
								<tr class=';
								
							if ($duree_actuelle <= $duree_planifie) echo "bg-success";
							else echo "bg-danger";
							echo '>
									<td style="padding:10px;"><label class="info">Durée réelle</label></td>
									<td style="padding:10px;"><label class="info">'.$duree_actuelle["heure"].' heure(s) et '.$duree_actuelle["minutes"].' minutes</label></td>
								</tr>
								<tr>
									<td style="padding:10px;"><label class="info">Durée théorique</label></td>
									<td style="padding:10px;"><label class="info">'.$duree_planifie["heure"].' heure(s) et '.$duree_planifie["minutes"].' minutes</label></td>
								</tr>
							</table></div></div>';
						
						}
						?>

					</div>
				</div>
				<div class="container-fluid theme-showcase" role="main">
					<h4 class="sub-header">Anomalie</h4>
					<div id="anom" class="jumbotron">
					<?php if ($anomalie) {


						echo '
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Type:&nbsp</label>'.$quiss.'</div>
								<div class="info"><label>Secteur:&nbsp </label>'.$autre.'</div>		
							</div>
							<div class="col-md-4">
								<div class="info"><label>Heures perdues:&nbsp </label>'.$heure.'</div>
								<div class="info"><label>KEuros perdus:&nbsp </label>'.$euros.'</div>
							</div>
							<div class="col-md-4">
								<div class="info"><label>Traçabilité:&nbsp </label>'.$status.'</div>';
								if ($cause_saisie)
								{
									echo '<div class="info"><label>Cause:&nbsp </label>'.$nomCause.'</div>';
								}
							echo '</div>
						</div>	
						<div class="info"><label>Descriptif:&nbsp </label>'.$descriptif.'</div>';
					}

				?>
				</div>
				</div>
				<div class="container-fluid theme-showcase" role="main">
				<h4 id="title" class="sub-header">Remarques</h4>
				<div class="jumbotron">
					<div><?php echo $remarque; ?></div>	
				</div>
				</div>
				<?php
				//Affichage de la pop-up ssi la remarque n'est pas vide
				if ($remarque != ""){
				?>
					<div id="remarque" title="Remarque">
						<p><?php echo $remarque; ?></p>
					</div>
				<?php
				}
				?>
				
			</div><!-- /.container -->
			<div id="dialog" title="Informations complémentaires">
				
					<select id="moy" title="Type moyen" class="form-control" name="moy">
					<option value="" disabled selected>Sélectionner un moyen</option>
				<?php
				if(isset($reqMoyen))
				{
					while($lg=mysqli_fetch_object($reqMoyen))
					{
						$moyen=$lg->nomMoyen;
						$idMoyen=$lg->idMoyen;
						echo "<option value='$idMoyen' > $moyen</option>";
					}
				}
				?>
				</select>
				
					<form method="post" action="">
						<?php
							$str = "SELECT nomEmp, prenomEmp FROM utilisateur, employe WHERE idEmp_EMPLOYE = idEmp and `idService_SERVICE`=$idLabo and idEmp > 6 and actif = 1;";
							$reqTechnicien=mysqli_query($bdd,$str);
							
							echo '<h4><p>Nom du technicien : </p></h4><div class="row">
							<div style="display:none; margin-top:10px;" class="col-md-3"><input name="tech" type="radio" value="Non renseigné"><h5 style="display:inline;">'.$nom." ".$prenom.'</h5></div>';
							while($lgTech=mysqli_fetch_object($reqTechnicien)){
								$nom = $lgTech->nomEmp;
								$prenom = $lgTech->prenomEmp;
								echo '<div style="margin-top:50px;" class="col-md-3"><label><input name="tech" type="radio" value="'.$nom." ".$prenom.'" ><h5 style="display:inline;">'.$nom." ".$prenom.'</h5></label></div>';
							}
							echo '</div>';
						?>
				</form>
			</div>
			<div id="dialog6" title="Nom du technicien">
				<form method="post" action="">
					<?php
						$str = "SELECT nomEmp, prenomEmp FROM utilisateur, employe WHERE idEmp_EMPLOYE = idEmp and `idService_SERVICE`=$idLabo and idEmp >6 and actif = 1;";
						$reqTechnicien=mysqli_query($bdd,$str);
						echo '<div class="row">
						<div style="display:none; margin-top:10px;" class="col-md-3"><input name="tech" type="radio" value="Non renseigné"><h5 style="display:inline;">'.$nom." ".$prenom.'</h5></div>';
						while($lgTech=mysqli_fetch_object($reqTechnicien)){
							$nom = $lgTech->nomEmp;
							$prenom = $lgTech->prenomEmp;
							echo '<div style="margin-top:50px;" class="col-md-3"><label><input name="tech" type="radio" value="'.$nom." ".$prenom.'"><h5 style="display:inline;">'.$nom." ".$prenom.'</h5></label></div>';
						}
						echo '</div>';
					?>
				</form>
			</div>
			<?php
			if ($idLabo == 2 || $idLabo == 3){
			?>
			<div id="dialogCheckList" title="Informations complémentaires">
				
				<div id="alert" class="alert alert-danger">Ces trois étapes sont obligatoires pour passer à la phase suivante</div>
				<div class="checkbox">
					<label><input id="check1" type="checkbox" name="fiche" value="1">Fiche suiveuse présente et renseignée</label>
				</div>
				<div class="checkbox">
					<label><input id="check2" type="checkbox" name="ofs" value="2">Concordance des OFs (fiche suiveuse/équipement)</label>
				</div>				
				<div class="checkbox">
					<label><input id="check3" type="checkbox" name="savers" value="3">Configuration savers</label>
				</div>
			</div>
			<?php
			}else{
			?>
			<div id="dialogCheckList" title="Informations complémentaires">
				
				<div id="alert" class="alert alert-danger">Ces deux étapes sont obligatoires pour passer à la phase suivante</div>
				<div class="checkbox">
					<label><input id="check1" type="checkbox" name="fiche" value="1">Fiche suiveuse présente et renseignée</label>
				</div>
				<div class="checkbox">
					<label><input id="check2" type="checkbox" name="ofs" value="2">Procédure disponible dans Alice</label>
				</div>				
			</div>
			<?php
			}
			?>
			
			<div id="dialog4" title="Sélectionnez un moyen">
				<?php
				$premier=true;
				if(isset($reqMoyen))
				{
					while($lg=mysqli_fetch_object($reqMoyen))
					{
						$moyen=$lg->nomMoyen;
						$idMoyen=$lg->idMoyen;
						if($premier){
							echo "<div><input checked type='radio' value='$idMoyen' name='rd'> $moyen</input></div>";
							$premier=false;
						}
						else
							echo "<div><input type='radio' value='$idMoyen' name='rd'> $moyen</input></div>";
					}
				}
				?>
			</div>
			
			<div id="dialog2" title="Raison de la suppression">
				<p><input class="form-control" type='text' id='raison' /></p>
			</div>	
			<div id="dialog3" title="FIFO ?">
				<p></p>
			</div>
			<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
			<script src="../jquery-ui/js/jquery-ui.min.js"></script>
			<?php echo '<script>var labo = '.$idLabo.' </script>' ?>
			<script src="../js/detailsEssai.js"></script>
			<script>
				$('#dialog').hide();
				$('#dialog2').hide();
				$('#dialog3').hide();
				$('#dialog4').hide();
				$('#dialog6').hide();
				$('#remarque').hide();
				$('#dialogCheckList').hide();
				$("#alert").hide();
			</script>
			<?php 
			$nb=0; //permet au .js de correctement répartir les infos dans les colonnes (0 dans la colonne 0, 1 dans la 1, 2 dans la 0, 3 dans la 1, ect)
			while($lg=mysqli_fetch_object($req)){
				$noOf=$lg->noOF;
				$nomModele=$lg->nomModele;
				$article=$lg->article;
				echo "<script>ecrireLigneOF('$noOf','$nomModele','$nb','$article')</script>";
				$nb++;
			}
		}
	}
}
require('bottom.php');