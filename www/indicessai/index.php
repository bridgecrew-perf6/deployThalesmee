<?php
	require('../conf/connexion_param.php'); 
	require('top.php');
?>
	<link href="../css/starter-template.css" rel="stylesheet">
	<link href="../css/publication.css" rel="stylesheet">
	<link href="../css/indicessai.css" rel="stylesheet">
	<script type="text/javascript" src="../js/indicessai.js"></script>

<?php
//Valeurs par défault
$annee = date("Y");
$semaine = date("W");
//Date de début date (date actuelle moins un an)
$date_deb = strftime("%Y-%m-%d 00:00:00", mktime(0,0,0,1,1,date('Y')));
//Date de fin (date actuelle)
$date_fin = strftime("%Y-%m-%d 23:59:00", mktime(0,0,0,date('m'),date('d'),date('Y')));

for ($labo=1; $labo<7; $labo++)
{
	
	if ($labo <= 3)
	{
		$lab = $labo;
		//Selection du contenu des textarea
		$str = "SELECT analyse, mesure, fait, deviation FROM publication WHERE annee = $annee AND numSemaine = $semaine AND idService_SERVICE = $labo";
		$req = mysqli_query($bdd, $str);
		//Si la ligne n'existe pas 
		if (mysqli_num_rows($req) == 0)
		{
			//Affectation de valeurs pas défault
			$fait = "";
			$analyse = "";
			$mesure = "";
			$deviation = -1;
		//Si la ligne existe
		}else 
		{
			//Affectation des valeurs
			$lg = mysqli_fetch_object($req);
			$fait = $lg->fait;
			$analyse = $lg->analyse;
			$mesure = $lg->mesure;
			$deviation = $lg->deviation;
		}
		
		//Récupération des targets pour les graphiques
		$str = "SELECT valeur FROM `target` WHERE nom='attente'";
		$req = mysqli_query($bdd, $str);
		$target1= mysqli_fetch_object($req)->valeur;
		$str = "SELECT valeur FROM `target` WHERE  nom='fpy'";
		$req = mysqli_query($bdd, $str);
		$target2= mysqli_fetch_object($req)->valeur;
		$str = "SELECT valeur FROM `target` WHERE  nom='retard'";
		$req = mysqli_query($bdd, $str);
		$target3= mysqli_fetch_object($req)->valeur;
		

		//Création des urls par défault
		$url1 = "";
		$url3 = "";
		if ($labo != 2){
			$url1 = "../graph/retard_fin_test.php?&idService=".$labo."&dateDeb=".$date_deb."&dateFin=".$date_fin."&target=".$target3."&moyenneRetard=1&public=1&Tous=1";
			//Sélection des moyens
			$str = "SELECT idMoyen, nomMoyen FROM moyen WHERE idService_SERVICE = $labo";
			$req = mysqli_query($bdd,  $str);
			while ($lg = mysqli_fetch_object($req)){
				$url1 .= "&".$lg->nomMoyen."=1";
			}
		}
		else
			$url1 = "../graph/attente_equip_av_annuel.php?&idService=".$labo."&dateDeb=".$date_deb."&dateFin=".$date_fin."&target=".$target1."&moyenne=1&public=1&Tous=1";

		$url2 = "../graph/fpy.php?&idService=".$labo."&dateDeb=".$date_deb."&dateFin=".$date_fin."&target=".$target2."&global=1&public=1&Tous=1";
		
		
		//Selection des lignes de produits
		$str = "SELECT nomLigne FROM `ligneproduit` ";
		$req = mysqli_query($bdd, $str);
		//Ajout des lignes de produit aux url
		while ($lg=mysqli_fetch_object($req))
		{
			if ($labo ==2 ) $url1.="&".$lg->nomLigne."=1";
			$url2.="&".$lg->nomLigne."=1";
		}

		//Récupération du chef de laboratoire
		$str = "SELECT prenomEmp, nomEmp FROM employe, utilisateur WHERE idEmp = idEmp_EMPLOYE AND categUser = 3 and idService_SERVICE = $labo";
		$req = mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$nomChef = $lg->nomEmp;
		$prenomChef= $lg->prenomEmp;

		?>
		<div class="container-fluid carousel">
			<div class="jumbotron">
				<div class="row">
					<div id="titre" class="col-md-10 text-center">
						<h2 class="text-info"><strong>CCEL/I2P-T/</strong></h2><h2 class="text-primary"><strong> KPI ESSAIS ENVIRONNEMENT 
							<?php if ($labo == 2) echo "MECANIQUE";
								  elseif ($labo == 1) echo "EMC";
								  elseif ($labo == 3) echo "VIDE THERMIQUE";
							?>
						</strong></h2>
					</div>
					<div id="feu" class="col-md-2 text-center bg-primary text-right feu">
						<div class="row">
							<span id="rouge<?php echo $labo ?>" class="bouton"></span>
						</div>
						<div class="row">
							<span id="orange<?php echo $labo ?>" class="bouton"></span>
						</div>
						<div class="row">
							<span id="vert<?php echo $labo ?>" class="bouton"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-6"></div>
					<div class="col-md-6 col-lg-6">
						<div class="row">
							<div class="text-info col-md-12">
								<strong>Porteur KPI : <?php echo $prenomChef[0].". ".$nomChef ?> - Ligne/Unité CCEL/I2P-T</strong>
							</div>
						</div>		
					</div>
				</div>
				<div class="row">
					<div class="col-md-7 col-lg-6 p-0 border-grey">
						<center><img id="graphe1" class="graph" src="<?php echo $url1 ?>" alt=""/></center>
						<center><img id ="graphe2" class="graph" src="<?php echo $url2 ?>" alt=""/></center>
					</div>
					<div class="col-md-5 col-lg-6">
						<h4 class="bg-primary text-white">ANALYSE DES ECARTS</h4>
						<textarea disabled id="analyse" name="analyse" class="form-control zone"><?php echo $analyse ?></textarea>
						<h4 class="bg-primary text-white">MESURES PRISES</h4>
						<textarea disabled id="mesure" name="mesure" class="form-control zone"><?php echo $mesure ?></textarea>
						<h4 class="bg-primary text-white">FAITS MARQUANTS</h4>
						<textarea disabled id="fait" name="fait" class="form-control zone"><?php echo $fait ?></textarea>
					</div>
				</div>
				<div class="row vertical-align mt-20">
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-red"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
					</div>
					<div class="col-md-1 bg-white m-10">
						<div class="row">
							<strong>Considerable</strong>
						</div>
						<div class="row">
							<strong>deviation</strong>
						</div>
						<div class="row">
							<strong>from target</strong>
						</div>
					</div>
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-orange"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
					</div>
					<div class="col-md-1 bg-white m-10">
						<div class="row">
							<strong>Critical</strong>
						</div>
						<div class="row">
							<strong>deviation</strong>
						</div>
						<div class="row">
							<strong>from target</strong>
						</div>
					</div>
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-green"></span>
						</div>
					</div>
					<div class="col-md-1 bg-white m-10">
						<div class="row">
							<strong>No or little</strong>
						</div>
						<div class="row">
							<strong>deviation</strong>
						</div>
						<div class="row">
							<strong>from target</strong>
						</div>
					</div>
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
					</div>
					<div class="col-md-2 bg-white m-10">
						<div class="row">
							<strong>Action required for</strong>
						</div>
						<div class="row">
							<strong>consistent</strong>
						</div>
						<div class="row">
							<strong>comparison to target</strong>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}else
	{
		$lab = $labo - 3;
		//Selection du contenu des textarea
		$str = "SELECT analyse, mesure, fait, deviation FROM publication WHERE annee = $annee AND numSemaine = $semaine AND idService_SERVICE = $lab";
		$req = mysqli_query($bdd, $str);
		//Si la ligne n'existe pas 
		if (mysqli_num_rows($req) == 0)
		{
			//Affectation de valeurs pas défault
			$fait = "";
			$analyse = "";
			$mesure = "";
			$deviation = -1;
		//Si la ligne existe
		}else 
		{
			//Affectation des valeurs
			$lg = mysqli_fetch_object($req);
			$fait = $lg->fait;
			$analyse = $lg->analyse;
			$mesure = $lg->mesure;
			$deviation = $lg->deviation;
		}
		
		//Récupération des targets pour les graphiques
		$str = "SELECT valeur FROM `target` WHERE nom='attente'";
		$req = mysqli_query($bdd, $str);
		$target1= mysqli_fetch_object($req)->valeur;
		$str = "SELECT valeur FROM `target` WHERE  nom='fpy'";
		$req = mysqli_query($bdd, $str);
		$target2= mysqli_fetch_object($req)->valeur;
		$str = "SELECT valeur FROM `target` WHERE  nom='retard'";
		$req = mysqli_query($bdd, $str);
		$target3= mysqli_fetch_object($req)->valeur;
		

		//Création des urls par défault
		$url3 = "../graph/retard_test.php?&idService=".$lab."&dateDeb=".$date_deb."&dateFin=".$date_fin."&target=".$target2."&global=1&public=1&Tous=1";
		
		
		//Selection des lignes de produits
		$str = "SELECT nomLigne FROM `ligneproduit` ";
		$req = mysqli_query($bdd, $str);
		//Ajout des lignes de produit aux url
		while ($lg=mysqli_fetch_object($req))
		{
			$url3.="&".$lg->nomLigne."=1";
		}

		//Récupération du chef de laboratoire
		$str = "SELECT prenomEmp, nomEmp FROM employe, utilisateur WHERE idEmp = idEmp_EMPLOYE AND categUser = 3 and idService_SERVICE = $lab";
		$req = mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$nomChef = $lg->nomEmp;
		$prenomChef= $lg->prenomEmp;

		?>
		<div class="container-fluid carousel">
			<div class="jumbotron">
				<div class="row">
					<div id="titre" class="col-md-10 text-center">
						<h2 class="text-info"><strong>CCEL/I2P-T/</strong></h2><h2 class="text-primary"><strong> KPI ESSAIS ENVIRONNEMENT 
							<?php if ($lab == 2) echo "MECANIQUE";
								  elseif ($lab == 1) echo "EMC";
								  elseif ($lab == 3) echo "VIDE THERMIQUE";
							?>
						</strong></h2>
					</div>
					<div id="feu" class="col-md-2 text-center bg-primary text-right feu">
						<div class="row">
							<span id="rouge<?php echo $labo ?>" class="bouton"></span>
						</div>
						<div class="row">
							<span id="orange<?php echo $labo ?>" class="bouton"></span>
						</div>
						<div class="row">
							<span id="vert<?php echo $labo ?>" class="bouton"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-6"></div>
					<div class="col-md-6 col-lg-6">
						<div class="row">
							<div class="text-info col-md-12">
								<strong>Porteur KPI : <?php echo $prenomChef[0].". ".$nomChef ?> - Ligne/Unité CCEL/I2P-T</strong>
							</div>
						</div>		
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 col-lg-12 p-0 border-grey">
						<center><img id="graphe1" class="graph" src="<?php echo $url3 ?>" alt=""/></center>
					</div>
				</div>
				<div class="row vertical-align mt-20">
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-red"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
					</div>
					<div class="col-md-1 bg-white m-10">
						<div class="row">
							<strong>Considerable</strong>
						</div>
						<div class="row">
							<strong>deviation</strong>
						</div>
						<div class="row">
							<strong>from target</strong>
						</div>
					</div>
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-orange"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
					</div>
					<div class="col-md-1 bg-white m-10">
						<div class="row">
							<strong>Critical</strong>
						</div>
						<div class="row">
							<strong>deviation</strong>
						</div>
						<div class="row">
							<strong>from target</strong>
						</div>
					</div>
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-green"></span>
						</div>
					</div>
					<div class="col-md-1 bg-white m-10">
						<div class="row">
							<strong>No or little</strong>
						</div>
						<div class="row">
							<strong>deviation</strong>
						</div>
						<div class="row">
							<strong>from target</strong>
						</div>
					</div>
					<div class="col-md-1 text-center bg-primary text-right feuStatic">
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
						<div class="row">
							<span class="colorFeu bg-white"></span>
						</div>
					</div>
					<div class="col-md-2 bg-white m-10">
						<div class="row">
							<strong>Action required for</strong>
						</div>
						<div class="row">
							<strong>consistent</strong>
						</div>
						<div class="row">
							<strong>comparison to target</strong>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
echo '<script type="text/javascript">changeDeviation('.$deviation.','.$lab.')</script>';
}
?>
<div class="col-md-2">
	<div class="autre-form">
		<input id="semaine" name="semaine" placeholder="Numéro de semaine" value="<?php echo $semaine ?>"  type="hidden" class="form-control"  size="2" />
	</div>
</div>
<div class="col-md-2">
	<div class="autre-form">
		<input id="annee" name="annee" placeholder="Année" value="<?php echo $annee ?>"  type="hidden" class="form-control"  size="4" />
	</div>
</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<?php

require('bottom.php');