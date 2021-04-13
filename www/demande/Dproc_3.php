<?php 
require('top.php');
require('../fonction.php');
if(!isset($_SESSION["idDP"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de demande</strong></div>';
else
{
	$idDP=$_SESSION["idDP"];
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	//on verifie l'avancement de l'etape
	$str="select validiteDP from DEMANDE_PROCEDURE where idDP=$idDP;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de l\'étape</strong></div>';
	else
	{
		$etape=(mysqli_fetch_object($req)->validiteDP);
		if ($etape>=2 && $etape<4)
		{
			//Recuperation des documents Clients
			$str="select b.idTypeDoc_TYPE_DOC, t.nom, b.reference, b.issue, a.comTypeArt, b.rev from fournirSpec a,  SPEC_CLIENT b, TYPE_DOC t
			where a.idDP_DEMANDE_PROCEDURE=$idDP
			and b.idSpec=a.idSpec_SPEC_CLIENT
			and b.idTypeDoc_TYPE_DOC=t.idTypeDoc;";
			$reqDocClient=mysqli_query($bdd,$str);
			if(!$reqDocClient)
				echo '<div class="alert alert-danger"><strong>Erreur de récupération des documents</strong></div>';
			else
			{
				
				$nbDoc=mysqli_num_rows($reqDocClient);
				// si le demandeur a deja rempli le formulaire des documents clients
				if ($nbDoc >0){
					$first_tp20=true;
					$first_tp21=true;
					$first_tp22=true;
					$tabDocOpt=array();
					$j=0;
					
					while($lg=mysqli_fetch_object($reqDocClient)){ 
						//pour chaque doc client
						// on recupere les 2 doc obligatoires 1 tp21 et 1 tp22 ou 1 tp20
						// on met le reste dans un tableau tabDocOpt

						$tpdoc=$lg->idTypeDoc_TYPE_DOC;//type du document
						if($tpdoc==20 and $first_tp20){
							// c est le premier doc tp20 que l on trouve
							$first_tp20=false;
							$refElecEnv=$lg->reference;
							$issueElecEnv=$lg->issue;
							$revElecEnv=$lg->rev;
							$comElecEnv=$lg->comTypeArt;
							
						}
						else if ($tpdoc==21 and $first_tp21){
							// c est le premier doc tp21 que l on trouve
							$first_tp21=false;
							$refElec=$lg->reference;
							$issueElec=$lg->issue;
							$revElec=$lg->rev;
							$comElec=$lg->comTypeArt;
							
						}else if ($tpdoc==22 and $first_tp22){
							// c est le premier doc tp22 que l on trouve
							$first_tp22=false;
							$refEnv=$lg->reference;
							$issueEnv=$lg->issue;
							$revEnv=$lg->rev;
							$comEnv=$lg->comTypeArt;
						
						}else{
							// c est un document en option
							$tabDocOpt[$j][0]=$lg->reference;
							$tabDocOpt[$j][1]=$lg->issue;
							$tabDocOpt[$j][2]=$lg->rev;
							$tabDocOpt[$j][3]=$lg->comTypeArt;//commentaire type article
							$tabDocOpt[$j][4]=$lg->idTypeDoc_TYPE_DOC;//type du document
							$tabDocOpt[$j][5]=$lg->nom;
							$j++;
						}
						$nbDocOpt=$j;
						// a la fin $tabDocOpt correspond a $tabDoc sans les 2 doc obligatoires
					}
					//on supprime le tableau tabDoc maintenant inutile (liberation de memoire)
					unset($tabDoc);
					
					
				}
				//recup des labo concernés
				$str="select idService_SERVICE from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP;";
				$req=mysqli_query($bdd, $str);
				
				$isEMC=false;$isVIB=false;$isVTH=false;
				while($lg=mysqli_fetch_object($req)){ 
					if($lg->idService_SERVICE==1)
						$isEMC=true;
					elseif($lg->idService_SERVICE==2)
						$isVIB=true;
					elseif($lg->idService_SERVICE==3)
						$isVTH=true;
				}
				
				
				$str="select a.idSpec, a.nomFichier ,a.reference , a.issue, a.rev , a.plateforme, b.nom, b.idTypeDoc from SPEC_CLIENT a, TYPE_DOC b where b.idTypeDoc=a.idTypeDoc_TYPE_DOC;";
				$reqSpecClient=mysqli_query($bdd,$str);
				?>
				<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
								
				<div class="container">
					<div class="page-header">
						<h2>Demande de procédure: n° <?php echo $idDP; ?></h2>
					</div>
					<p>
						<input type="button" id="enrDoc" value="Enregistrer un document" class="btn btn-lg btn-primary" />
						<input type="button" id="listDoc" value="Liste des documents" class="btn btn-lg btn-primary" />
					</p>
					<form method="post" id='form' action="traitement_DProc_3.php" >
						<div class="container theme-showcase" role="main">
							<h4>Référencer les documents</h4>
							<div class="jumbotron">
								<center style='color:#0088FF'>Les documents référencés doivent être préalablement enregistrés.</center>
								<br/>
								<table class="table" id="tableRefSpec">
									<tr>
										<th class="td-label">Type de document</th>
										<th>Référence</th>
										<th>Issue</th>
										<th>Révision</th>
										<th>Com / Type Article</th>
									</tr>				
									
									<tr>
										<td class="td-label">Specifications Electrique et Environnement</td>
										<td><input placeholder="Référence" type="text" id="ref_elec_env" class="form-control" name="ref_elec_env" value="<?php if(isset($refElecEnv)) echo $refElecEnv ?>" /></td>
										<td><input placeholder="Issue" type="text" id="issue_elec_env" class="form-control" name="issue_elec_env" value="<?php if(isset($issueElecEnv)) echo $issueElecEnv ?>" /></td>
										<td><input placeholder="Révision" type="text" class="form-control" id="rev_elec_env" name="rev_elec_env" value="<?php if(isset($revElecEnv)) echo $revElecEnv ?>" /></td>
										<td><input placeholder="Com / Type Article" type="text" class="form-control" id="tcom_elec_env" name="com_elec_env" value="<?php if(isset($comElecEnv)) echo $comElecEnv ?>" /></td>
									</tr>
									<tr><td style="text-align:left;"><b>OU</b></td></tr>
									<?php if($isEMC) { ?>
									<tr>
										<td class="td-label">Specifications Electrique</td>
										<td><input placeholder="Référence" type="text" id="ref_elec" class="form-control" name="ref_elec" value="<?php  if(isset($refElec)) echo $refElec ?>" /></td>
										<td><input placeholder="Issue" type="text" id="issue_elec" class="form-control" name="issue_elec" value="<?php if(isset($issueElec)) echo $issueElec ?>" /></td>
										<td><input placeholder="Révision" type="text" class="form-control" id="rev_elec" name="rev_elec" value="<?php  if(isset($revElec)) echo $revElec ?>" /></td>
										<td><input placeholder="Com / Type Article" type="text" class="form-control" id="tcom_elec" name="com_elec" value="<?php  if(isset($comElec)) echo $comElec ?>" /></td>
									</tr>
									<?php } ?>
									<tr>
										<td class="td-label">Specifications Environnement</td>
										<td><input placeholder="Référence" type="text" class="form-control" id="ref_env" name="ref_env" value="<?php  if(isset($refEnv)) echo $refEnv ?>" /></td>
										<td><input placeholder="Issue" type="text" class="form-control" id="issue_env" name="issue_env" value="<?php if(isset($issueEnv)) echo $issueEnv ?>" /></td>
										<td><input placeholder="Révision" type="text" class="form-control" id="rev_env" name="rev_env" value="<?php  if(isset($revEnv)) echo $revEnv ?>" /></td>
										<td><input placeholder="Com / Type Article" type="text" class="form-control" id="tcom_env" name="com_env" value="<?php  if(isset($comEnv)) echo $comEnv ?>" /></td>
									</tr>
									
								</table>
								<input type="button" class="btn btn-primary" value="Supprimer les documents" onclick="supprDOC_Client()"/>
							</div>
						</div>
						<input type="submit" class="btn btn-lg btn-primary" style="float:right;" value="Suivant" />
					</form>
					<input type="button" class="btn btn-lg btn-primary" style="float:left;" value="Précédent" onclick="document.location.href='DProc_2.php'"/>
				</div>
				<div id="dialog" title="Enregistrer un document">
					<form enctype="multipart/form-data" method="post" id="formDoc">
						<select id="tpdoc" name="tpdoc" class="form-control">
							<option value="-1" selected disabled>Choisir le type de document</option>
							<option value="20">Spécifications Electrique et Environnement</option>
							<option value="21">Spécifications Electrique</option>
							<option value="22">Spécifications Environnement</option>
							<option value="23">RFD Electrique</option>	
							<option value="24">RFD Mécanique</option>
							<option value="25">RFD Thermique</option>						
							<option value="26">Autre</option>						
						</select>
						<input placeholder="Référence" class="form-control" type="text" id="ref" name="ref" />
						<input placeholder="Issue" class="form-control" type="text" id="issue" name="issue" />
						<input placeholder="Révision" class="form-control" type="text" id="rev" name="rev" />
						<input placeholder="Plateforme" class="form-control" type="text" id="plateforme" name="plateforme" />
						Document à envoyer:<input title="Document à envoyer"  class="form-control" style="height:auto;" id="file"  type="file" name="monfichier"/>
					</form>
				</div>
				
				
				<div id="dialog_liste" title="Sélectionnez les documents à utiliser">
					<table class="table table-striped table-tri" id="tri">
						<thead >
							<tr>
								<th></th>
								<th>Type de document</th>
								<th>Référence</th>
								<th>Issue</th>
								<th>Révision</th>
								<th>Plateforme</th>
								<th>Voir le doc</th>
							</tr>
						</thead>
						<tbody>
						<?php
						while($lg=mysqli_fetch_object($reqSpecClient))
						{	
							$idSpec=$lg->idSpec;
							$nomFicher=$lg->nomFichier;
							$reference=$lg->reference;
							$issue=$lg->issue;
							$rev=$lg->rev;
							$plateforme=$lg->plateforme;
							$tpdoc=$lg->nom;
							$idTypeDoc=$lg->idTypeDoc;
							$lien=$idSpec.$nomFicher;
							echo "<tr>";
								echo "<td><input type='checkbox' class='checkDoc' /></td>";
								echo "<td><input type='hidden' value='$idTypeDoc'/>$tpdoc</td>";
								echo "<td>$reference</td>";
								echo "<td>$issue</td>";
								echo "<td>$rev</td>";
								echo "<td>$plateforme</td>";
								echo "<td><a style='color:blue' href='download.php?link=$lien&nomOr=$nomFicher'>".tronquer($nomFicher,10)."</a></td>";
							echo "</tr>";
						}
						?>
						</tbody>
						<tfoot >
							<tr>
								<th></th>
								<th>Type de document</th>
								<th>Référence</th>
								<th>Issue</th>
								<th>Révision</th>
								<th>Plateforme</th>
								<th>Voir le doc</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<script src="../jquery-ui/js/jquery-ui.min.js"></script>
				<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
				<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
				<script src="../js/DProc_3.js"></script>
				<?php	
				//pour les doc en options
				if (isset($nbDocOpt) && $nbDocOpt!=0){
					//pour tous les documents facultatifs
					for ($i=0;$i<$nbDocOpt;$i++){	
						$valR=$tabDocOpt[$i][0];
						$valI=$tabDocOpt[$i][1];
						$valRev=$tabDocOpt[$i][2];
						$valCom=$tabDocOpt[$i][3];
						$valS=$tabDocOpt[$i][4];
						$NomTypeDoc=$tabDocOpt[$i][5];
						echo "<script>ajoutDoc_Client_avecParam('$valR','$valI','$valRev','$valS','$NomTypeDoc','$valCom',1);</script>";
					}
				}
			}
		}
		else
			echo '<div class="alert alert-danger"><strong>L\'étape précédente n\'a pas correctement été validé</strong></div>';
	}
}
require('bottom.php');
