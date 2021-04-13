<?php
	require('../conf/connexion_param.php'); 
	require('top.php');
	$labo=$_SESSION['infoUser']['idService'];// service du labo

	if ($labo == 1) $titre = "EMC";
	else if ($labo == 2) $titre = "ENVIRONNEMENT MECANIQUE";
	else $titre = "VIDE THERMIQUE";

	//Si le formulaire a été envoyé
	if (isset($_POST["fait"]) || isset($_POST["analyse"]) || isset($_POST["mesure"]) || isset($_GET["deviation"]))
	{
		//Récupération des paramètres transmis
		$fait;
		if (isset($_POST["fait"])) $fait = $_POST["fait"];
		$analyse;
		if (isset($_POST["analyse"])) $analyse = $_POST["analyse"];
		$mesure;
		if (isset($_POST["mesure"])) $mesure = $_POST["mesure"];
		$deviation = null;
		if (isset($_GET["deviation"])) $deviation = $_GET["deviation"];
		$annee = $_POST["annee"];
		$semaine = $_POST["semaine"];

		//Requête SQl pour vérifier si la ligne existe déjà
		$str = "SELECT * FROM publication WHERE annee = $annee AND numSemaine = $semaine AND idService_SERVICE = $labo";
		$req = mysqli_query($bdd, $str);
		//Si la clé n'existe pas
		if (mysqli_num_rows($req) == 0)
		{
			//Insertion des valeurs dans la base de données
			$str = "INSERT INTO publication VALUES ($semaine, $annee, '$analyse', '$mesure', '$fait', $deviation, $labo)";
			$req = mysqli_query($bdd, $str);
		//Si la clé existe
		}else 

		{	//Update des valeurs existantes
			$str = "UPDATE publication SET analyse = '$analyse', mesure = '$mesure', fait = '$fait', deviation= $deviation WHERE annee = $annee AND numSemaine = $semaine AND idService_SERVICE = $labo";
			$req = mysqli_query($bdd, $str);
		}
	}

	//Si l'année et la semaine sont définies
	if (isset($_POST["semaine"]) && isset($_POST["annee"]))
	{
		//Affectation des valeurs
		$annee = $_POST["annee"];
		$semaine = $_POST["semaine"];
	//Si non définies
	}else
	{
		//Valeurs par défault
		$annee = date("Y");
		$semaine = date("W");
	}

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
	
	//Récupération du labo concerné
	$labo=$_SESSION['infoUser']['idService'];// service du labo

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

	//Date de début date (date actuelle moins un an)
	$date_deb = strftime("%Y-%m-%d 00:00:00", mktime(0,0,0,1, 1, date('Y')));
	//Date de fin (date actuelle)
	$date_fin = strftime("%Y-%m-%d 23:59:00", mktime(0,0,0,date('m'), date('d'), date('Y')));

	//Création des urls par défault
	$url1 = "";
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

	
?>

<link href="../calendrier/calendrier.css" rel="stylesheet" />
<link href="../css/starter-template.css" rel="stylesheet">
<link href="../css/publication.css" rel="stylesheet">
<form id="form" action="publication.php" method="POST">
	<div class="container-fluid">
		<div class="jumbotron">
			<div class="row">
				<div id="titre" class="col-md-10 text-center">
					<h2 class="text-info"><strong>CCEL/I2P-T/</strong></h2><h2 class="text-primary"><strong> KPI ESSAIS <?php echo $titre ?></strong></h2>
				</div>
				<div id="feu" class="col-md-2 text-center bg-primary text-right feu">
					<div class="row">
						<span id="rouge" onclick="changeDeviation(0)" class="bouton"></span>
					</div>
					<div class="row">
						<span id="orange" onclick="changeDeviation(1)" class="bouton"></span>
					</div>
					<div class="row">
						<span id="vert" onclick="changeDeviation(2)" class="bouton"></span>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8 col-lg-7 p-0 border-grey">
					<center><img id ="graphe1" class="graph" src="<?php echo $url1 ?>" alt=""/></center>
					<center><img id ="graphe2" class="graph" src="<?php echo $url2 ?>" alt=""/></center>
				</div>
				<div class="col-md-4 col-lg-5">
					<h4 class="bg-primary text-white">ANALYSE DES ECARTS</h4>
					<textarea id="analyse" name="analyse" class="form-control zone"><?php echo $analyse ?></textarea>
					<h4 class="bg-primary text-white">MESURES PRISES</h4>
					<textarea id="mesure" name="mesure" class="form-control zone"><?php echo $mesure ?></textarea>
					<h4 class="bg-primary text-white">FAITS MARQUANTS</h4>
					<textarea id="fait" name="fait" class="form-control zone"><?php echo $fait ?></textarea>
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
		<center><button class="btn btn-success btn-lg mt-20" type="submit">Valider</button></center>
		</div>
	</div>
	<div class="container-fluid">
		<div class="col-md-12">
			<div class="jumbotron">
				<div class="row vertical-align mt-10">
					<div class="col-md-2 text-center">
						<h3 class="d-inline"><strong>Semaine n°</strong></h3> 
					</div>
					<div class="col-md-2">
						<div class="autre-form">
							<input id="semaine" name="semaine" placeholder="Numéro de semaine" value="<?php echo $semaine ?>"  type="text" class="form-control"  size="2" />
						</div>
					</div>
					<div class="col-md-2 text-center">
						<h3 class="d-inline"><strong>de l'année</strong></h3> 
					</div>
					<div class="col-md-2">
						<div class="autre-form">
							<input id="annee" name="annee" placeholder="Année" value="<?php echo $annee ?>"  type="text" class="form-control"  size="4" />
						</div>
					</div>
					<div class="col-md-4">
						<div onclick="changeUrl()" class="btn btn-success btn-block">Valider</div>
					</div>
				</div>
				<div class="row date mb-10">
					<div class="col-md-4">
						<div onclick="semainePrecedente()" class="date btn btn-block btn-primary">Semaine précédente</div>
					</div>
					<div class="col-md-4">
						<div onclick="semaineEnCours()" class="date btn btn-block btn-info">Semaine en cours</div>
					</div>
					<div class="col-md-4">
						<div onclick="semaineSuivante()" class="date btn btn-block btn-primary">Semaine suivante</div>
					</div>		
				</div>
				<div class="row date_annuelle btn_annuelle mb-10">
					<div class="col-md-4">
						<div onclick="annee_prec()" class="btn btn-block btn-primary">Année précédente</div>
					</div>
					<div class="col-md-4">
						<div onclick="annee_cours()" class="btn btn-block btn-info">Année en cours</div>
					</div>
					<div class="col-md-4">
						<div onclick="annee_suiv()" class="btn btn-block btn-primary">Année suivante</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script src="../js/publication.js"></script>

<?php
echo '<script type="text/javascript">changeDeviation('.$deviation.')</script>';
require('bottom.php');

