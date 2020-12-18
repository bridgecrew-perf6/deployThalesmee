<?php
/*cette partie est présente sur toutes les pages de la partie admin
test si droit d'accées + demarrage de la session (avec le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur)
*/
session_start();
//on verifie que l'utilisateur a le droit d'etre sur cette page
if(isset($_SESSION["metro"]) && isset($_SESSION["metro"]["labo"])) 
{
	$nom=$_SESSION['metro']['nom'];
	$prenom=$_SESSION['metro']['prenom'];
	
	?>
	<!DOCTYPE html>
	<html lang="fr">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Métrologie</title>
		<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon" />
		<!-- Bootstrap core CSS -->
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />

		<!-- Custom styles for this template -->
		<link href="../css/starter-template.css" rel="stylesheet" />
		

		<!-- Placeholders.js v3.0.2 -> ajoute le support du placeholder pour IE sans effet pour ceux qui le supporte deja -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		
		 <!--[if lt IE 10]>
		 <script src="../js/Placeholder.js"></script>
		 
		 <script src="../js/html5shiv.js"></script>
		  <script src="../js/respond.min.js"></script>
		<![endif]-->
		
		<script src="../bootstrap/js/jquery.min.js"></script>
		<script src="../js/sweetAlert.js"></script>
	  </head>

	  <body>

		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		  <div class="container-fluid">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a class="navbar-brand" href="./index.php">Accueil</a>
			</div>
			<div class="collapse navbar-collapse">
			 <ul class="nav navbar-nav">
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Instruments<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="listInstru.php">Liste des instuments</a></li>
					<li><a href="creerInstru.php">Ajouter un instrument</a></li>
					<li><a href="associerDocument.php">Associer un document</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Calibration<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="infoFutCalib.php">Infos Futures calibrations</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Rébut - Réforme<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="listInstruNonDispo.php">Listes des intruments</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Capteurs<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="listCapteur.php">Liste des capteurs</a></li>
					<li><a href="gestionBidon.php">Gestion projet bidon</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Gestion des entrées-sorties<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="listPret.php">Liste des entrées-sorties en cours</a></li>
					<li><a href="listHistoPret.php">Historique des entrées-sorties cloturées</a></li>
					<li><a href="creerModifierPret.php">Ajouter une entrée-sortie</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Divers<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="gestionLocal.php">Gestion des localisations</a></li>
					<li><a href="gestionUnite.php">Gestion des unités</a></li>
					<li><a href="exportBase.php">Exporter la base format excel</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Historique suppression<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="instru_suppr.php">Instruments supprimés</a></li>
				  </ul>
				</li>

				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tableaux récapitulatifs<b class="caret"></b></a>
				  <ul class="dropdown-menu">
				  	<li><a href='tableau_recap.php' />Tableaux récapitulatifs</a></li>
					<li><a href='historique.php' />Fichier historique</a></li>
				  </ul>
				</li>



				
			 </ul>
			  <ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
				 <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $prenom." ".$nom;  ?><b class="caret"></b></a>
				  <ul class="dropdown-menu">
				  <?php
					//si responsable on propose la partie administration)
					if($_SESSION["metro"]["categUser"]==2)
					{
						echo '<li><a href="../admin/index.php">Administration</a></li>';
					}
					?>
					<li><a href="../modifMonPwd.php">Modifier mon mot de passe</a></li>
					<li><a href="../deconnexion.php">Déconnexion</a></li>
				  </ul>
				</li>
			  </ul>
			</div><!--/.nav-collapse -->
		  </div>
		</div>

	 <!-- les balises noscript n'affichent leurs contenues que si le javascript est désactivé -->
	<noscript><div class="alert alert-danger">
	<strong>Veuillez activer le JavaScript de votre navigateur pour le bon fonctionnement de l'application !</strong>
	</div></noscript>
<?php
}
else
	header("Location: ../index.php");