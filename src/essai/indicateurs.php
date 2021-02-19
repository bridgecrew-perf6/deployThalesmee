<?php
require('../conf/connexion_param.php'); 
require('../conf/connexionPDO_param.php');// connexion a la base
require('top.php');

$labo=$_SESSION['infoUser']['idService'];// service du labo

//Date de début date (date actuelle moins un an)
$date_deb = strftime("%Y-%m-%d", mktime(0,0,0,date('m'), date('d'), date('Y')-1));
//Date de fin (date actuelle)
$date_fin = strftime("%Y-%m-%d", mktime(0,0,0,date('m'), date('d'), date('Y')));


//formatage pour la value des input
$dateDebForm=explode("-",$date_deb);
$dateDebForm=$dateDebForm[2]."/".$dateDebForm[1]."/".$dateDebForm[0];
$dateFinForm=explode("-",$date_fin);
$dateFinForm=$dateFinForm[2]."/".$dateFinForm[1]."/".$dateFinForm[0];

$str_ligne = "SELECT nomLigne FROM ligneproduit";
$req_ligne = mysqli_query($bdd, $str_ligne);

$target = array();
$str = "SELECT valeur FROM target";
$req = mysqli_query($bdd, $str);
while ($lg = mysqli_fetch_object($req)){
	
	array_push($target, $lg->valeur);
}

$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $labo";
$req_moyen = mysqli_query($bdd, $str);


?>
<link href="../calendrier/calendrier.css" rel="stylesheet" />
<link href="../css/starter-template.css" rel="stylesheet">
<link href="../css/indicateurs.css" rel="stylesheet">

<div class="container-fluid">
	<div class="page-header">
		<h2>Indicateurs</h2> 
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="jumbotron">
				<div class="panel-group">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse1"><strong>&bull; Suivi de procédure</strong></a>
							</h4>
						</div>
						<div id="collapse1" class="panel-collapse collapse in">
							<ul class="list-group">
								<li class="list-group-item nodate noligneprod" id="1" onclick="changeUrl (this, 'suivi_proc.php?', 'genPDFrexFifo.php?arg=procedure', 'suivi_proc_excel.php?')"><strong>&gt;</strong> Livraison des procédures par laboratoire</li>
								<li class="list-group-item nodate noligneprod" id="2" onclick="changeUrl (this, 'duree_redac.php?', 'genPDFrexFifo.php?arg=procedure', 'duree_redac_excel.php?')"><strong>&gt;</strong> Durée moyenne des étapes de rédaction des procédures VIB</li>
								<li class="list-group-item nodate noligneprod" id="3" onclick="changeUrl (this, 'ecart_proc.php?', 'genPDFrexFifo.php?arg=procedure', 'ecart_proc_excel.php?')"><strong>&gt;</strong> Écart de livraison des procédures VIB par rapport à la date de besoin</li>
							</ul>
						</div>
					</div>
				</div> 
				<div class="panel-group">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse2"><strong>&bull; Suivi d'activité</strong></a>
							</h4>
						</div>
						<div id="collapse2" class="panel-collapse collapse in">
							<ul class="list-group">
								<!-- <li class="list-group-item" id="4" onclick="changeUrl (this, 'attente_equip_av.php?fifo=0', 'genPDFrexFifo.php?fifo=0')">&gt; Durée d'attente de l'équipement sur l'étagère VIB avant test</li>
								<li class="list-group-item" id="5" onclick="changeUrl (this, 'attente_equip_fin.php?fifo=0', 'genPDFrexFifo.php?fifo=0')">&gt; Durée d'attente de l'équipement sur l'étagère VIB après test</li> -->
								<li class="list-group-item" id="6" onclick="changeUrl (this, 'attente_equip_av.php?fifo=2', 'genPDFrexFifo.php?fifo=2', 'attente_equip_av_excel.php?fifo=2&')"><strong>&gt;</strong> Temps d'attente avant test</li>
								<li class="list-group-item" id="7" onclick="changeUrl (this, 'attente_equip_fin.php?fifo=2', 'genPDFrexFifo.php?fifo=2', 'attente_equip_fin_excel.php?fifo=2&')"><strong>&gt;</strong> Temps d'attente après test</li>
								<li class="list-group-item" id="8" onclick="changeUrl (this, 'attente_equip_fin.php?fifo=1', 'genPDFrexFifo.php?fifo=1', 'attente_equip_fin_excel.php?fifo=1&')"><strong>&gt;</strong> Temps d'attente après test (FIFO)</li>
								<li class="list-group-item" id="9" onclick="changeUrl (this, 'attente_equip_av.php?fifo=1', 'genPDFrexFifo.php?fifo=1', 'attente_equip_av_excel.php?fifo=1&')"><strong>&gt;</strong> Temps d'attente avant test (FIFO)</li>
								<li class="list-group-item" id="10" onclick="changeUrl (this, 'rex_fifo_test.php?fifo=1', 'genPDFrexFifo.php?fifo=1', 'rex_fifo_test_excel.php?')"><strong>&gt;</strong> Retour d'expérience FIFO</li>
								<li class="list-group-item" id="11" onclick="changeUrl (this, 'retardEnregistre.php?', 'genPDFrexFifo.php?arg=retard', 'retardEnregistre_excel.php?')"><strong>&gt;</strong> Retard de livraison des équipements avant test</li>
								<li class="list-group-item noligneprod" id="20" onclick="changeUrl (this, 'occupation.php?', 'genPDFrexFifo.php?fifo=2', 'occupation_excel.php?')"><strong>&gt;</strong> Taux d'occupation des moyens</li>
							</ul>
						</div>
					</div>
				</div> 
				<div class="panel-group">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse3"><strong>&bull; Suivi qualité annuel</strong></a>
							</h4>
						</div>
						<div id="collapse3" class="panel-collapse collapse in">
							<ul class="list-group">
								<li class="list-group-item nodate date_annu target" id="16" onclick="changeUrl(this, 'attente_equip_av_annuel.php?', 'genPDFrexFifo.php?fifo=2', 'attente_equip_av_annuel_excel.php?' )"><strong>&gt;</strong> Temps d'attente avant test</li>
								<li class="list-group-item nodate date_annu target" id="17" onclick="changeUrl(this, 'attente_equip_fin_annuel.php?', 'genPDFrexFifo.php?fifo=2', 'attente_equip_fin_annuel_excel.php?' )"><strong>&gt;</strong> Temps d'attente après test</li>
								<li class="list-group-item nodate date_annu" id="18" onclick="changeUrl(this, 'fpy.php?', 'genPDFrexFifo.php?fifo=2', 'fpy_excel.php?' )"><strong>&gt;</strong> First part yield (FPY)</li>
								<li class="list-group-item nodate date_annu noligneprod" id="19" onclick="changeUrl(this, 'fiabilitePrevision.php?', 'genPDFrexFifo.php?fifo=2', 'fiabilitePrevision_excel.php?' )"><strong>&gt;</strong> Fiabilité prévisions LdP</li>
								<li class="list-group-item moyen date_annu noligneprod" id="21" onclick="changeUrl(this, 'occupation_annuel.php?', 'genPDFrexFifo.php?fifo=2', 'occupation_annuel_excel.php?' )"><strong>&gt;</strong> Taux d'occupation des moyens</li>
								<li class="list-group-item date_annu noligneprod" id="23" onclick="changeUrl(this, 'retard_fin_test.php?', 'genPDFrexFifo.php?fifo=2', 'retard_fin_test_excel.php?' )"><strong>&gt;</strong> Retard fin de test</li>
								<li class="list-group-item date_annu" id="24" onclick="changeUrl(this, 'retard_test.php?', 'genPDFrexFifo.php?fifo=2', 'retard_test_excel.php?' )"><strong>&gt;</strong> Efficacité (tenue des cycles)</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="panel-group">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse4"><strong>&bull; Suivi non qualité</strong></a>
							</h4>
						</div>
						<div id="collapse4" class="panel-collapse collapse in">
							<ul class="list-group">
								<li class="list-group-item" id="14" onclick="changeUrl(this, 'nombre_anomalie.php?num=3', 'genPDFindicateurs.php?num=1', 'nombre_anomalie_excel.php?')"><strong>&gt;</strong> Nombre de test en anomalie par secteur d'origine</li>
								<li class="list-group-item" id="13" onclick="changeUrl(this, 'cumulatif_anomalie.php?num=2', 'genPDFindicateurs.php?num=2', 'cumulatif_anomalie_excel.php?')"><strong>&gt;</strong> Écart cumulatif de test en anomalie par secteur d'origine</li>
								<li class="list-group-item" id="14" onclick="changeUrl(this, 'pourcentage_anomalie.php?num=3', 'genPDFindicateurs.php?num=3', 'pourcentage_anomalie_excel.php?')"><strong>&gt;</strong> Pourcentage de test en anomalie par secteur d'origine</li>
								<li class="list-group-item" id="15" onclick="changeUrl(this, 'pourcentage_cumulatif_anomalie.php?num=4', 'genPDFindicateurs.php?num=4', 'pourcentage_cumulatif_anomalie_excel.php?')"><strong>&gt;</strong> Pourcentage cumulatif de test en anomalie par secteur d'origine</li>
								<li class="list-group-item noligneprod"  id="22" onclick="changeUrl(this, 'cause_anomalie.php?num=5', 'genPDFrexFifo.php?fifo=2', 'cause_anomalie_excel.php?')"><strong>&gt;</strong> Cause des anomalies</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="jumbotron">
				<div class="row vertical-align" style="margin-bottom:10px;">
					<div class="col-md-1 text-center">
						<label><strong>Du</strong></label> 
					</div>
					<div class="col-md-5">
						<div class="autre-form">
							<input id="dateDeb" placeholder="01/01/2014" value="<?php echo $dateDebForm;?>"  type="text" class="date date_annuelle calendrier form-control"  size="8" />
						</div>
					</div>
					<div class="col-md-1 text-center">
						<label><strong>au</strong></label> 
					</div>
					<div class="col-md-5">
						<div class="autre-form">
							<input id="dateFin" placeholder="01/01/2014" value="<?php echo $dateFinForm;?>"  type="text" class="date date_annuelle alendrier form-control"  size="8"/>
						</div>
					</div>
				</div>
				<div class="row date" style="margin-bottom:10px;">
					<div class="col-md-2">
						<button onclick="mois_prec()" class="date btn btn-block btn-warning">Mois précédent</button>
					</div>
					<div class="col-md-4">
						<button onclick="mois_cours()" class="date btn btn-block btn-primary">Mois en cours</button>
					</div>
					<div class="col-md-2">
						<button onclick="mois_suiv()" class="date btn btn-block btn-warning">Mois suivant</button>
					</div>
					<div class="col-md-4">
						<button onclick="annee_cours()" class="date btn btn-block btn-info">Année en cours</button>
					</div>			
				</div>
				<div class="row date_annuelle btn_annuelle" style="margin-bottom:10px;">
					<div class="col-md-4">
						<button onclick="annee_prec()" class="btn btn-block btn-warning" onclick="changeDate(1)">Année précédente</button>
					</div>
					<div class="col-md-4">
						<button onclick="annee_cours()" class="btn btn-block btn-info" onclick="changeDate(2)">Année en cours</button>
					</div>
					<div class="col-md-4">
						<button onclick="annee_suiv()" class="btn btn-block btn-warning" onclick="changeDate(3)">Année suivante</button>
					</div>
				</div>
				<div id="moyen" class="row moyen" style="margin : 0; margin-bottom:10px;">
					<div class="row">
						<div class="col-md-1">
							<strong>Target</strong>
						</div>
						<div class="col-md-3">
							<input id="targetmoyen" name="moyen" value="<?php echo $target[2] ?>" step="any">
							<input class="limiteretard" id="targetretard" name="moyen" value="<?php echo $target[3] ?>" step="any">
						</div>
						<div class="row limiteretard">
							<div class="col-md-2" style="width:14%;">
								<label class="checkbox-inline" ><input type="checkbox" name="moyenne" value="moyenneRetard"><strong>Moyenne</strong></label>
							</div>
							<div class="col-md-2" style="width:14%;">
								<label class="checkbox-inline" ><input type="checkbox" name="median" value="medianRetard"><strong>Médiane</strong></label>
							</div>
						</div>
					</div>
					<div class="row listemoyen">
						<div class="col-md-2">
							<label class="checkbox-inline" onclick="check_all(1)" ><input id="tousmoyen" type="checkbox" name="tous" value="Tous"><strong>Tous</strong></label>
						</div>
						<?php
							$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $labo";
							$req_moyen = mysqli_query($bdd, $str);
							while($lg = mysqli_fetch_object($req_moyen)){
							?>	
								<div class="col-md-2">
									<label class="checkbox-inline"><input type="checkbox" class="filtremoyen <?php if ($lg->nomMoyen == 'Petite cage' || $lg->nomMoyen == 'Grande cage') echo 'cage' ?>" name="moyen" value="<?php echo $lg->nomMoyen ?>"><strong>&nbsp;<?php echo $lg->nomMoyen ?></strong></label>
								</div>
							<?php
							}
						?>
					</div>
				</div>
				<div id="ligneProd" class="row text-center" style="margin-bottom:10px;">
					<div class="col-md-1" style="width:10%;">
						<label class="checkbox-inline" onclick="check_all(2)" ><input id="tous" type="checkbox" name="tous" value="Tous"><strong>Tous</strong></label>
					</div>
					<?php
					while($lg = mysqli_fetch_object($req_ligne)){
					?>	
						<div class="col-md-1" style="width:10%;">
							<label class="checkbox-inline"><input type="checkbox" class="filtre" name="ligneproduit" value="<?php echo $lg->nomLigne ?>"><strong>&nbsp;<?php echo $lg->nomLigne ?></strong></label>
						</div>
					<?php
					}
					?>
					<div class="col-md-1" style="width:14%;">
						<label class="spe checkbox-inline" ><input type="checkbox" name="ligneproduit" value="HorsDite"><strong>Hors I2PT</strong></label>
					</div>
					<div class="col-md-1" style="width:10%;">
						<label class="spe checkbox-inline"  ><input type="checkbox" name="ligneproduit" value="Total"><strong>Total</strong></label>
					</div>
				</div>
				
				<!-- Case à cocher et target pour le graphique des temps d'attentes annuelles -->
				<div class="limite row" style="margin-bottom:10px;">
					<div class="col-md-1">
						<strong>Target</strong>
					</div>
					<div class="col-md-3">
						<input id="target" name="target" value="<?php echo $target[0] ?>" step="any">
					</div>
					<div class="col-md-1" style="width:14%;">
						<label class="checkbox-inline" ><input type="checkbox" name="moyenne" value="moyenne"><strong>Moyenne</strong></label>
					</div>
					<div class="col-md-1" style="width:14%;">
						<label class="checkbox-inline" ><input type="checkbox" name="median" value="median"><strong>Médiane</strong></label>
					</div>
				</div>
				<!-- Case à cocher et target pour le graphique des cause anomalies -->
				<div class="cause row" style="margin-bottom:10px;">
					<div class="col-md-1">
						<strong>Target</strong>
					</div>
					<div class="col-md-3">
						<input id="targetcause" name="target" value="<?php echo $target[2] ?>" step="any">
					</div>
				</div>
				<!-- Case à cocher et target pour le graphique des retard -->
				<div class="retard row" style="margin-bottom:10px;">
					<div class="col-md-1">
						<strong>Target</strong>
					</div>
					<div class="col-md-3">
						<input id="targetretardtest" name="target" value="<?php echo $target[4] ?>" step="any">
					</div>
				</div>
				<!-- Case à cocher et target pour le graphique des anomalie FPY -->
				<div class="fpy row" style="margin-bottom:10px;">
					<div class="col-md-1">
						<strong>Target</strong>
					</div>
					<div class="col-md-3">
						<input id="targetfpy" name="target" value="<?php echo $target[1] ?>" step="any">
					</div>
					<div class="col-md-1" style="width:16%;">
						<label class="checkbox-inline" ><input type="checkbox" name="global" value="global"><strong>FPY global</strong></label>
					</div>
					<div class="col-md-1" style="width:14%;">
						<label class="checkbox-inline" ><input type="checkbox" name="I2PT" value="i2pt"><strong>FPY I2PT</strong></label>
					</div>
				</div>
				<div class="row">
					<center>
						<div class="col-md-12">
							<div id="valider" class="date date_annuelle btn btn-block btn-success" onclick="changeDate()">Valider</div>
						</div>
					</center>
				</div>
			</div>
			<div class="row div-graph">
				<div class="col-md-12">
					<center><img style="width : 50%" id="rex_test" class="graph" src="" alt=""/></center>
					<div id="tab"></div>
					<div id="export" class="jumbotron">
						<div class="row">
							<div class="col-md-6">
								<a id="pdf" type="button" class="btn btn-lg btn-block btn-danger graph figure-center" href="#" />PDF<a/>
								</figure>
							</div>
							<div class="col-md-6">
								<a id="excel" type="button" class="btn btn-lg btn-block btn-success" href="#" />Excel<a/>
							</div>
						</div>
					
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script><?php echo 'var labo = '.$labo ?></script>
<script src="../js/indicateurs.js"></script>

<?php
require('bottom.php');